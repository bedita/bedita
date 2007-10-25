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
	var $helpers 	= array('Bevalidation', 'BeTree', 'BeToolbar');
	var $components = array('BeAuth', 'BeTree', 'Transaction', 'Permission', 'BeCustomProperty', 'BeLangText');
	var $uses = array('Area', 'Section',  'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Gallery', 'Tree');

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
		$children = $this->BeTree->getDiscendents(null, null, $conf->objectTypes['image'], false, null, 1, 100);
		$imagesAll = (isset($children['items'])) ? $children['items'] : array();
		$idGallery = ($id == null) ? 0 : $id;
		$children = $this->BeTree->getDiscendents($idGallery, null, $conf->objectTypes['image'], false, null, 1, 100);
		$imagesForGallery = (isset($children['items'])) ? $children['items'] : array();
		$galleryImages =
		$this->set('object',	$obj);
		$this->set('tree', 		$tree);
		$this->set('parents',	$parents_id);
		$this->set('images',	$imagesAll);
		$this->set('imagesForGallery',	$imagesForGallery);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", $id) ));
		$this->set('self',		($this->createSelfURL(false)."?"));
		$this->set('conf',		$conf);
	}

	private function saveGallery($id) {
		try {
			if(empty($this->data)) throw new BEditaActionException($this, __("No data", true));
			$new = (empty($this->data['id'])) ? true : false;
		 	if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY))
		 			throw new BEditaActionException($this, "Error modify permissions");
		 	$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]);
		 	$this->BeLangText->setupForSave($this->data["LangText"]);
			$this->Transaction->begin();
		 	if(!$this->Gallery->save($this->data)) throw new BEditaActionException($this, $this->Gallery->validationErrors);
			if(($parents = $this->Tree->getParent($this->Gallery->id)) !== false) {
				if(!is_array($parents)) $parents = array($parents);
			} else {
				$parents = array();
			}
			if(!isset($this->data['destination'])) $this->data['destination'] = array();
			$remove = array_diff($parents, $this->data['destination']);
			foreach ($remove as $parent_id) {
				$this->Tree->removeChild($this->Gallery->id, $parent_id);
			}
			$add = array_diff($this->data['destination'], $parents);
			foreach ($add as $parent_id) {
				$this->Tree->appendChild($this->Gallery->id, $parent_id);
			}
		 	if(!$this->Permission->saveFromPOST(
		 			$this->Gallery->id,
		 			(isset($this->data["Permissions"]))?$this->data["Permissions"]:array(),
		 			(empty($this->data['recursiveApplyPermissions'])?false:true))
		 		) {
		 			throw new BEditaActionException($this, __("Error saving permissions", true));
		 	}
	 		$this->Transaction->commit();
	 	} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->Transaction->rollback();
	 	}
	 	return $this->Gallery->id;
	}

	private function deleteGallery($id) {
	 	try {
		 	if(empty($id)) throw BEditaActionException($this,__("No data", true));
	 		$this->Transaction->begin();
	 		if(!$this->Gallery->delete($id)) throw new BEditaActionException($this, sprintf(__("Error deleting gallery: %d", true), $id));
		 	$this->Transaction->commit();
	 	} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->Transaction->rollback();
		}
	}
}