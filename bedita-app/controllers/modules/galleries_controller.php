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
 * @author 			d.domenico@channelweb.it, ste@channelweb.it
 */

class GalleriesController extends ModulesController {
	var $name = 'Galleries';
	var $helpers 	= array('Beurl', 'BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');
    var $uses = array('BEObject', 'Gallery', 'Tree', 'Category') ;
	protected $moduleName = 'galleries';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 10) {
		$conf  = Configure::getInstance() ;
		$types = array($conf->objectTypes['gallery']);
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	}

    public function view($id = null) {
    	$conf       = Configure::getInstance();
        $obj        = null;
        $multimedia = array();
        $parents_id = array();
        
        // get Gallery data
        if($id) {
            $this->Gallery->contain(array(
                                        "BEObject" => array("ObjectType", 
                                                            "UserCreated", 
                                                            "UserModified", 
                                                            "Permissions",
                                                            "CustomProperties",
                                                            "LangText",
                                                            "RelatedObject",
                                                            "Category"
                                                      ),
                                        )
                                    );
            if(!($obj = $this->Gallery->findById($id))) {
                throw new BeditaException( sprintf(__("Error loading gallery: %d", true), $id));
            }
			if(!$this->Gallery->checkType($obj['object_type_id'])) {
               throw new BeditaException(__("Wrong content type: ", true).$id);
			}
           $relations = $this->objectRelationArray($obj['RelatedObject']);
            
           $parents_id = $this->Tree->getParent($id) ;
            if($parents_id === false) 
                $parents_id = array() ;
            elseif(!is_array($parents_id))
                $parents_id = array($parents_id);
        }

        $tree = $this->BeTree->getSectionsTree() ;
    
        $status = (!empty($obj['status'])) ? $obj['status'] : null;
        $previews = (isset($id)) ? $this->previewsForObject($parents_id,$id,$status) : array();

        $this->set('object',    $obj);
        $this->set('attach', isset($relations['attach']) ? $relations['attach'] : array());
        $this->set('relObjects', isset($relations) ? $relations : array());
        $this->set('tree',      $tree);
        $this->set('parents',   $parents_id);
        $this->set('previews',  $previews);
        $this->setUsersAndGroups();
    }
    
	function select_from_list($id = null, $order = "", $dir = true, $page = 1, $dim = 10) {
		$this->loadGalleries($id,$order,$dir,$page,$dim);
	}	
	
	public function save() {

        $this->checkWriteModulePermission();
        if(empty($this->data)) 
            throw new BeditaException( __("No data", true));
        $new = (empty($this->data['id'])) ? true : false ;
        // Verify object permits
        if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
            throw new BeditaException(__("Error modify permissions", true));
        // Format custom properties
        $this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
        
        $this->Transaction->begin() ;
        // Save data
        $this->data["Category"] = $this->Category->saveTagList($this->params["form"]["tags"]);
		if(!$this->Gallery->save($this->data)) {
			throw new BeditaException( __("Error saving gallery", true), $this->Gallery->validationErrors);
		}		
        // update permissions
        if(!isset($this->data['Permissions'])) 
            $this->data['Permissions'] = array() ;
        $this->Permission->saveFromPOST($this->Gallery->id, $this->data['Permissions'], 
                !empty($this->data['recursiveApplyPermissions']), 'gallery');
        $this->Transaction->commit() ;
		$this->userInfoMessage(__("Gallery saved", true) . "<br />" . $this->data["title"]);
		$this->eventInfo("gallery ". $this->data["title"]." saved");
	}

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Gallery");
		$this->userInfoMessage(__("Galleries deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("galleries $objectsListDeleted deleted");
	}

	protected function forward($action, $esito) {
		$REDIRECT = array("save"	=> 	array("OK"	=> "./view/{$this->Gallery->id}",
		                                      "ERROR"	=> "./view/{$this->Gallery->id}"),
						"delete"	=> 	array("OK"	=> "./",
						                      "ERROR"	=> "./view/{@$this->params['pass'][0]}"));
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito];
		return false;
	}
}

?>