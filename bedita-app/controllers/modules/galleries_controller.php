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
		$types = array($conf->objectTypes['gallery']["id"]);
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	}

    public function view($id = null) {

    	$this->viewObject($this->Gallery, $id);

    }
    
	public function save() {
        $this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->Gallery);
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