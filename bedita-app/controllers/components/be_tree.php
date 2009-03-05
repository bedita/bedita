<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * Accesse e gestione dell'albero dei contenuti.
 * 
 * I permessi sono espressi in un integer che raprresenta una combinazione 
 * di bit definiti nel file di configurazione (bedita.ini.php):
 * BEDITA_PERMS_READ	0x1
 * BEDITA_PERMS_MODIFY	0x2
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeTreeComponent extends Object {
	var $controller		= null ;
	protected $Tree		= null ;
	protected $BEObject	= null ;
	protected $Section	= null ;
	var $filter			= array();
	
	function __construct() {
		$this->Tree = ClassRegistry::init('Tree');
		$this->BEObject = ClassRegistry::init('BEObject');
		$this->Section = ClassRegistry::init('Section');
	} 

	/**
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$this->controller 	= $controller;
		// set filter parameters
		if (!empty($this->controller->passedArgs["category"])) {
			$this->filter["category"] = $this->controller->passedArgs["category"];
			$this->controller->set("categorySearched", $this->controller->passedArgs["category"]);
		}
		if (!empty($this->controller->passedArgs["relation"]))
			$this->filter["ObjectRelation.switch"] = $this->controller->passedArgs["relation"];
			
		if (!empty($this->controller->passedArgs["rel_object_id"]))
			$this->filter["ObjectRelation.object_id"] = $this->controller->passedArgs["rel_object_id"];
			
		if (!empty($this->controller->passedArgs["rel_detail"]))
			$this->filter["rel_detail"] = $this->controller->passedArgs["rel_detail"];
			
		if (!empty($this->controller->passedArgs["comment_object_id"]))
			$this->filter["Comment.object_id"] = $this->controller->passedArgs["comment_object_id"];
			
		if (!empty($this->controller->passedArgs["mail_group"]))
			$this->filter["mail_group"] = $this->controller->passedArgs["mail_group"];
		
		if (!empty($this->controller->passedArgs["tag"]))
			$this->filter["tag"] = $this->controller->passedArgs["tag"];

		if (!empty($this->controller->params["form"]["searchstring"])) {
			$this->filter["search"] = addslashes($this->controller->params["form"]["searchstring"]);
			$this->controller->params["named"]["search"] = urlencode($this->controller->params["form"]["searchstring"]);
			$this->controller->set("stringSearched", $this->controller->params["form"]["searchstring"]);
		} elseif (!empty($this->controller->passedArgs["search"])) {
			$this->controller->params["named"]["search"] = urlencode($this->controller->passedArgs["search"]);
			$this->filter["search"] = addslashes(urldecode($this->controller->passedArgs["search"]));
			$this->controller->set("stringSearched", urldecode($this->controller->passedArgs["search"]));
		}
	}
	
	function cleanFilter() {
		$this->filter = array();
	}

	/**
	 * Torna l'albero delle aree e delle sezioni a cui l'utente connesso
	 * pu� accedere almeno in lettura.
	 *
	 */
	function getSectionsTree() {
		$conf  = Configure::getInstance() ;
		
		// Preleva l'utente connesso
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : '' ;
		
		$tree = $this->Tree->getAll(null, $userid, null, array($conf->objectTypes['area']['id'],$conf->objectTypes['section']['id'])) ;
		
		return $tree ;	
	}
	
	function getAreaForSection($section_id) {
		$area = ClassRegistry::init('Area');
		$area->containLevel("minimum");
		$area_id = $this->Tree->getRootForSection($section_id);
		return $area->findById($area_id);
	}

	/**
	 * Torna l'albero con espanso solo quello dove l'oggetto selezionato � presente.
	 * Mantiene solo i figli.
	 *
	 * @param integer $id
	 */
	function expandOneBranch($id = null) {
		$tree = $this->getSectionsTree() ;
		
		for($i=0; $i < count($tree) ; $i++) {
			if(!isset($id) || !$this->_searchRootDeleteOther($tree[$i], $id)) {
				unset($tree[$i]['children']) ;
			} 
		}
		
		return $tree ;
	}
	
	/**
	 * Torna l'elenco dei figli di un data nodo dell'albero
	 *
	 * @param integer $id		ID del nodo
	 */
	function getChildren($id = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000) {
		$conf  = Configure::getInstance() ;
		
		// Preleva l'utente connesso
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : null ;
		
		$filter = ($filter)? array_merge($this->filter, $filter) : $this->filter;
		
		$objs = &  $this->Tree->getChildren($id, $userid, $status, $filter, $order, $dir, $page, $dim) ;
		
		return  $objs ;
	}
	
	/**
	 * Return discendents of a tree node
	 *
	 * @param integer $id		node ID
	 */
	function getDiscendents($id = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000) {
		$conf  = Configure::getInstance() ;
		// Get user data
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : null ;
		
		$filter = ($filter)? array_merge($this->filter, $filter) : $this->filter;
			
		$objs = &  $this->Tree->getDiscendents($id, $userid, $status, $filter, $order, $dir, $page, $dim) ;
		
		return  $objs ;
	}
	
	/**
	 * Stante un path "/<...>/<...>/..." torna l'idi dell'oggetto puntato
	 * 
	 * @param integer $path	Path dell'oggetto da trovare
	 */
	function getIdFromNickname($path) {
		return $this->_getIdFromNickname($path) ;
	}
	
	/**
	 * Array of parent objects of $id...
	 *
	 * @param integer $id
	 */
	public function getParents($id = null) {
		$parents_id = array();
		if(isset($id)) {
			$parents_id = $this->Tree->getParent($id) ;
			if($parents_id === false) 
				$parents_id = array(); // ???
			elseif(!is_array($parents_id))
				$parents_id = array($parents_id);
		}
		return $parents_id;
	}

	/**
	 * update tree position of object $id with new $destination array
	 *
	 * @param integer $id
	 * @param array $destination
	 */
	public function updateTree($id, $destination) {

		$currParents = $this->getParents($id);
		// remove
		$remove = array_diff($currParents, $destination) ;
		foreach ($remove as $parent_id) {
			$this->Tree->removeChild($id, $parent_id) ;
		}
		// insert
		$add = array_diff($destination, $currParents) ;
		foreach ($add as $parent_id) {
			$this->Tree->appendChild($id, $parent_id) ;
		}

	}
	
	private function _getIdFromNickname($path) {
		$nickname = basename($path) ;
		if(@empty($nickname)) return null ;
		
		$parent_id = $this->_getIdFromNickname(dirname($path)) ;
		
		return $this->Tree->getIdFromNickname($nickname,  $parent_id) ;
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////
	/**
	 * Cancella i rami che non contengono id
	 *
	 * @param array $trees	albero dove cercare
	 * @param integer $id	oggetto da cercare
	 */
	private function _searchRootDeleteOther(&$tree, $id) {
		// Se la radice cercata
		if($tree['id'] == $id) {
			for($i=0; $i < count($tree['children']) ; $i++) {
				unset($tree['children'][$i]['children']) ;
			}
			
			return true ;
		}
		
		// Cerca tra i discendenti
		$found = null ;
		for($i=0; $i < count($tree['children']) ; $i++) {
			if($this->_searchRootDeleteOther($tree['children'][$i], $id)) {
				$found = $i ;
			} 
		}
		
		// Se ha trovato cancella i rami da escludere
		if(isset($found)) {
			$tmp = $tree['children'][$found] ;
			
			unset($tree['children']) ;
			$tree['children'] = array($tmp) ;
			
			return true ;
		}
		
		return false ;
	}
}

?>