<?php
/**
 *
 * @filesource
 * @copyright
 * @link
 * @package
 * @subpackage
 * @since
 * @version
 * @modifiedby
 * @lastmodified
 * @license
 * @author 			d.domenico@channelweb.it
 */

class GalleriesController extends AppController {
	var $name = 'Galleries';
	var $helpers 	= array('Beurl', 'BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');
	var $uses = array('Area', 'Section',  'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Gallery', 'Tree', 'Image', 'Audio', 'Video');
	protected $moduleName = 'galleries';
	
	/**
	 * Public methods for the controller
	 */

	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 10) {
		$this->loadGalleries($id,$order,$dir,$page,$dim);
	}

	public function view($id = null) {
		$this->loadGallery($id);
	}

	function select_from_list($id = null, $order = "", $dir = true, $page = 1, $dim = 10) {
		$this->loadGalleries($id,$order,$dir,$page,$dim);
	}	
	
	public function save() {
		$this->checkWriteModulePermission();
		if(empty($this->data))  throw new BeditaException( __("No data", true));
		
		$new = (empty($this->data['id'])) ? true : false;
		
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) {
			throw new BeditaException(__("Error modify permissions", true));
		}
		 
		// Formattazione dei dati da salvare
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]);
		$this->BeLangText->setupForSave($this->data["LangText"]);
		
		$multimedia = (isset($this->data['multimedia']))?$this->data['multimedia']:array() ;
		unset($this->data['multimedia']);
		
		$this->Transaction->begin();
		
		if(!$this->Gallery->save($this->data)) {
			throw new BeditaException( __("Error saving gallery", true), $this->Gallery->validationErrors);
		}		
		
		// aggiorna i permessi
		$perms = isset($this->data["Permissions"])?$this->data["Permissions"]:array();
		if(!$this->Permission->saveFromPOST(
				$this->Gallery->id, $perms,
				(empty($this->data['recursiveApplyPermissions'])?false:true), 'gallery')
			) {
				throw new BeditaException( __("Error saving permissions", true));
		}
		
		// Inserisce gli oggetti multimediali selezionati, cancellando le precedenti associazioni
		if(!$this->Gallery->removeChildren()) throw new BeditaException( __("Remove children", true));
		
		for($i=0; $i < count($multimedia) ; $i++) {
			if(!$this->Gallery->appendChild($multimedia[$i]['id'],null,$multimedia[$i]['priority'])) {
				throw new BeditaException( __("Append child", true));
			}
		}
		
	 	$this->Transaction->commit();
	}

	public function delete() {
		$this->checkWriteModulePermission();
	 	if(empty($this->data['id'])) throw new BeditaException(__("No data", true));
	 		
		if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
			throw new BeditaException(__("Error delete permissions", true));
		}
	 	
	 	$this->Transaction->begin() ;
	 	
		// Cancellla i dati
	 	if(!$this->Gallery->delete($this->data['id'])) throw new BeditaException( sprintf(__("Error deleting area: %d", true), $this->data['id']));
		 	
	 	$this->Transaction->commit() ;
	}

	/**
	 * Private methods
	 */

	private function loadGalleries($id,$order,$dir,$page,$dim) {
 		$conf = Configure::getInstance();
		$this->setup_args(
			array("id", "integer", &$id),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim)
		);
		$galleries = $this->BeTree->getDiscendents($id, null, $conf->objectTypes['gallery'], $order, $dir, $page, $dim);
		$this->params['toolbar'] = &$galleries['toolbar'];
		$this->set('tree', 		$this->BeTree->expandOneBranch($id));
		$this->set('galleries', (count($galleries['items'])==0) ? array() : $galleries['items']);
		$this->set('toolbar', 	$galleries['toolbar']);
		$this->set('selfPlus',	$this->createSelfURL(false));
		$this->set('self',		($this->createSelfURL(false)."?"));
	}

	private function loadGallery($id) {
		$this->setup_args(array("id", "integer", &$id));

		$conf 		= Configure::getInstance();
		$obj 		= null;
		$multimedia = array();
		
		// preleva la galleria selezionata e gli oggetti associati
		if($id) {
			$this->Gallery->bviorHideFields = array('Version', 'Index', 'current');
			if(!($obj = $this->Gallery->findById($id))) {
				throw new BeditaException( sprintf(__("Error loading area: %d", true), $id));
				return;
			}
			
			// Preleva i contentuti della galleria
			$types = array($conf->objectTypes['image'], $conf->objectTypes['audio'], $conf->objectTypes['video']) ;
			$children = $this->BeTree->getChildren($id, null, $types, "priority") ;
			$objForGallery = &$children['items'] ;
			
			foreach($objForGallery as $index => $object) {
				$type = $conf->objectTypeModels[$object['object_type_id']] ;
			
				$this->{$type}->bviorHideFields = array('UserCreated','UserModified','Permissions','Version','CustomProperties','Index','langObjs', 'images', 'multimedia', 'attachments');
				if(!($Details = $this->{$type}->findById($object['id']))) continue ;

				$Details['priority'] = $object['priority'];
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
			
				$multimedia[$index]=$Details;
			}
		}
		
		if(isset($obj["LangText"])) $this->BeLangText->setupForView($obj["LangText"]);
		
		$this->set('object',	$obj);
		$this->set('multimedia',$multimedia);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", $id) ));
		$this->set('self',		($this->createSelfURL(false)."?"));
		$this->set('conf',		$conf);
		$this->set('CACHE',		'imgcache/');
		$this->set('MEDIA_URL',	MEDIA_URL);
		$this->set('MEDIA_ROOT',MEDIA_ROOT);
	}
	
	protected function forward($action, $esito) {
		$REDIRECT = array("save"	=> 	array(
							"OK"	=> "./view/{$this->Gallery->id}",
							"ERROR"	=> "./view/{$this->Gallery->id}" 
						),"delete"	=> 	array(
							"OK"	=> "./",
							"ERROR"	=> "./view/{@$this->params['pass'][0]}" 
						) 
		);
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito];
	 	return false;
	}
}