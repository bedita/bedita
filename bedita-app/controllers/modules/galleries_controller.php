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

	// This controller does not use a model
	var $uses = array('Area', 'Section',  'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Document', 'Tree') ;

	function index($id = null, $order = "", $dir = true, $page = 1, $dim = 10) {

		$conf  = Configure::getInstance() ;

	 	// Setup parametri
		$this->setup_args(
			array("id", "integer", &$id),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;

		// Preleva l'albero delle aree e sezioni;
		$galleries = $this->BeTree->getDiscendents($id, null, $conf->objectTypes['gallery'], $order, $dir, $page, $dim)  ;

		$this->params['toolbar'] = &$galleries['toolbar'] ;

		// Setup dei dati da passare al template
		$this->set('tree', 		$this->BeTree->expandOneBranch($id));
		$this->set('galleries', (count($galleries['items'])==0) ? array() : $galleries['items']);
		$this->set('toolbar', 	$galleries['toolbar']);
		$this->set('selfPlus',	$this->createSelfURL(false)) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;
	 }

	function view($id = null, $order="", $dir = true, $page = 1, $dim = 10) {

		$conf  = Configure::getInstance() ;

	 	// Setup parametri
		$this->setup_args(
			array("id", "integer", &$id),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;

		// Preleva l'albero delle aree e sezioni;
		$galleries = $this->BeTree->getDiscendents($id, null, $conf->objectTypes['gallery'], $page, $dim)  ;

		$this->params['toolbar'] = &$galleries['toolbar'] ;

		// Setup dei dati da passare al template
		$this->set('tree', 		$this->BeTree->expandOneBranch($id));
		$this->set('galleries', (count($galleries['items'])==0) ? array() : $galleries['items']);
		$this->set('toolbar', 	$galleries['toolbar']);
		$this->set('selfPlus',	$this->createSelfURL(false)) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;
	 }

	 function save() {
	 	try {
		 	if(empty($this->data)) throw BEditaActionException($this, __("No data", true));

			$new = (empty($this->data['id'])) ? true : false ;

		 	// Verifica i permessi di modifica dell'oggetto
		 	if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY))
		 			throw new BEditaActionException($this, "Error modify permissions");

		 	// Formatta le custom properties
		 	$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;

		 	// Formatta i campi d tradurre
		 	$this->BeLangText->setupForSave($this->data["LangText"]) ;

			$this->Transaction->begin() ;

	 		// Salva i dati
		 	if(!$this->Gallery->save($this->data)) throw new BEditaActionException($this, $this->Gallery->validationErrors);

		 	// aggiorna i permessi
		 	if(!$this->Permission->saveFromPOST(
		 			$this->Gallery->id,
		 			(isset($this->data["Permissions"]))?$this->data["Permissions"]:array(),
		 			(empty($this->data['recursiveApplyPermissions'])?false:true))
		 		) {
		 			throw BEditaActionException($this, __("Error saving permissions", true));
		 	}
	 		$this->Transaction->commit() ;

	 	} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->Transaction->rollback() ;

			return ;
	 	}
	 }

	 function delete($id = null) {
		$this->setup_args(array("id", "integer", &$id)) ;

	 	try {
		 	if(empty($id)) throw BEditaActionException($this,__("No data", true));

		 	$this->Transaction->begin() ;

		 	// Cancellla i dati
		 	if(!$this->Gallery->delete($id)) throw new BEditaActionException($this, sprintf(__("Error deleting gallery: %d", true), $id));

		 	$this->Transaction->commit() ;
	 	} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->Transaction->rollback() ;

			return ;
	 	}

	 }
}