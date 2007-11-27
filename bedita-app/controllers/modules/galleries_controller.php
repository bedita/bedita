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
	var $uses = array('Area', 'Section',  'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Gallery', 'Tree', 'Image');
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

	public function save($id = null, $order = "", $dir = true, $page = 1, $dim = 10) {
		$savedGalleryId=$this->saveGallery($id);
		$this->loadGallery($savedGalleryId);
	}

	public function delete($id = null, $order = "", $dir = true, $page = 1, $dim = 10) {
	 	$this->deleteGallery($id);
	 	$this->loadGalleries(null,$order,$dir,$page,$dim);
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
	 	$conf = Configure::getInstance();
		$this->setup_args(array("id", "integer", &$id));
		$obj = null;
		if($id) {
			$this->Gallery->bviorHideFields = array('Version', 'Index', 'current');
			if(!($obj = $this->Gallery->findById($id))) {
				$this->Session->setFlash(sprintf(__("Error loading gallery: %d", true), $id));
				return;
			}
		}
		if(isset($obj["LangText"])) $this->BeLangText->setupForView($obj["LangText"]);
		$tree = $this->BeTree->getSectionsTree();
		$parents_id = isset($id) ? $this->Tree->getParent($id) : 0;
		if(!is_array($parents_id)) $parents_id = array($parents_id);
		$idGallery = ($id == null) ? 0 : $id;
		$children = $this->BeTree->getDiscendents($idGallery, null, $conf->objectTypes['image'], false, null, 1, 100);
		$imagesForGallery = (isset($children['items'])) ? $children['items'] : array();
		$images = array();
		foreach($imagesForGallery as $index => $image) {
			$this->Image->bviorHideFields = array('UserCreated','UserModified','Permissions','Version','CustomProperties','Index','langObjs', 'images', 'multimedia', 'attachments');
			$imageDetails = $this->Image->findById($image['id']);
			$imageDetails['priority'] = $image['priority'];
			$imageDetails['filename'] = substr($imageDetails['path'],strripos($imageDetails['path'],"/")+1);
			$images[$index]=$imageDetails;
		}
		$this->set('object',	$obj);
		$this->set('tree', 		$tree);
		$this->set('parents',	$parents_id);
		$this->set('images',	$images);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", $id) ));
		$this->set('self',		($this->createSelfURL(false)."?"));
		$this->set('conf',		$conf);
		$this->set('CACHE',		'imgcache/');
		$this->set('MEDIA_URL',	MEDIA_URL);
		$this->set('MEDIA_ROOT',MEDIA_ROOT);
	}

	private function saveGallery($id) {
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false;
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY))
			throw new BeditaException(__("Error modify permissions", true));
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]);
		$this->BeLangText->setupForSave($this->data["LangText"]);
		
		$this->Transaction->begin();
		if(!$this->Gallery->save($this->data)) 
			throw new BeditaException( __("Error saving gallery", true), $this->Gallery->validationErrors);
			if(($parents = $this->Tree->getParent($this->Gallery->id)) !== false) {
			if(!is_array($parents)) $parents = array($parents);
		} else {
			$parents = array();
		}
		if(!isset($this->data['destination'])) $this->data['destination'] = array();
		$remove = array_diff($parents, $this->data['destination']);
		foreach ($remove as $parent_id) { $this->Tree->removeChild($this->Gallery->id, $parent_id); }
		$add = array_diff($this->data['destination'], $parents);
		foreach ($add as $parent_id) { $this->Tree->appendChild($this->Gallery->id, $parent_id); }
		if(!$this->Permission->saveFromPOST(
			$this->Gallery->id,
		 	(isset($this->data["Permissions"]))?$this->data["Permissions"]:array(),
		 	(empty($this->data['recursiveApplyPermissions'])?false:true))) {
				throw new BeditaException( __("Error saving permissions", true));
		}
	 	$this->Transaction->end();
	 	return $this->Gallery->id;
	}

	private function deleteGallery($id) {
	 	if(empty($id)) {
	 		$this->log("TEST");
			throw new BeditaException(__("No data", true));
		}
	 	$this->Transaction->begin();
		$this->Gallery->delete($id);
		$this->Transaction->end();
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