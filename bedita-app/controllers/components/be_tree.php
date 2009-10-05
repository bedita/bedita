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
 * Management of the tree of the contents.
 * 
 * Permits with bits representation (bedita.ini.php):
 * BEDITA_PERMS_READ	0x1
 * BEDITA_PERMS_MODIFY	0x2
 * 
 *
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
			$this->filter["query"] = addslashes($this->controller->params["form"]["searchstring"]);
			$this->controller->params["named"]["query"] = urlencode($this->controller->params["form"]["searchstring"]);
			$this->controller->set("stringSearched", $this->controller->params["form"]["searchstring"]);
		} elseif (!empty($this->controller->passedArgs["query"])) {
			$this->controller->params["named"]["query"] = urlencode($this->controller->passedArgs["query"]);
			$this->filter["query"] = addslashes(urldecode($this->controller->passedArgs["query"]));
			$this->controller->set("stringSearched", urldecode($this->controller->passedArgs["query"]));
		}
	}
	
	function cleanFilter() {
		$this->filter = array();
	}

	/**
	 * Tree of publications and sections for user connected
	 *
	 */
	function getSectionsTree() {
		$conf  = Configure::getInstance() ;
		
		// Get connected user
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : '' ;
		$filter = array(
			"object_type_id" => array($conf->objectTypes['area']['id'],$conf->objectTypes['section']['id']) 
		);
		$tree = $this->Tree->getAll(null, $userid, null, $filter) ;
		
		return $tree ;	
	}
	
	function getAreaForSection($section_id) {
		$area = ClassRegistry::init('Area');
		$area->containLevel("minimum");
		$area_id = $this->Tree->getRootForSection($section_id);
		return $area->findById($area_id);
	}

	/**
	 * Get tree with one branch expanded (the branch where object $id is)
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
	 * Get children for node $id
	 *
	 * @param integer $id		node ID
	 */
	function getChildren($id = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000) {
		$conf  = Configure::getInstance() ;
		
		// Get user connected
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : null ;
		
		$filter = ($filter)? array_merge($this->filter, $filter) : $this->filter;
		
		$objs = &  $this->Tree->getChildren($id, $userid, $status, $filter, $order, $dir, $page, $dim) ;
		
		return  $objs ;
	}
	
	/**
	 * Return descendants of a tree node
	 *
	 * @param integer $id		node ID
	 */
	function getDescendants($id = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000) {
		$conf  = Configure::getInstance() ;
		// Get user data
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : null ;
		
		$filter = ($filter)? array_merge($this->filter, $filter) : $this->filter;
			
		$objs = &  $this->Tree->getDescendants($id, $userid, $status, $filter, $order, $dir, $page, $dim) ;
		
		return  $objs ;
	}
	
	/**
	 * Get object id from path $path
	 * 
	 * @param integer $path	Path of the object to find
	 */
	function getIdFromNickname($path) {
		return $this->_getIdFromNickname($path) ;
	}
	
	/**
	 * Array of parent objects of $id...
	 *
	 * @param integer $id
	 */
	public function getParents($id = null, $area_id=null) {
		$parents_id = array();
		if(isset($id)) {
			$parents_id = $this->Tree->getParent($id,$area_id) ;
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
	 * Delete branches that don't contain object $id
	 *
	 * @param array $tree	tree where to perform search
	 * @param integer $id	object to search
	 */
	private function _searchRootDeleteOther(&$tree, $id) {
		// If it's the root...
		if($tree['id'] == $id) {
			for($i=0; $i < count($tree['children']) ; $i++) {
				unset($tree['children'][$i]['children']) ;
			}
			
			return true ;
		}
		
		// Search in children trees
		$found = null ;
		for($i=0; $i < count($tree['children']) ; $i++) {
			if($this->_searchRootDeleteOther($tree['children'][$i], $id)) {
				$found = $i ;
			} 
		}
		
		// If branches to exclude were found, delete them
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