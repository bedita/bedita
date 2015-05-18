<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
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

	public $controller = null;
	public $filter = array();
	protected $Tree	= null;
	protected $BEObject	= null;
	protected $Section = null;
	
	function __construct() {
		$this->Tree = ClassRegistry::init('Tree');
		$this->BEObject = ClassRegistry::init('BEObject');
		$this->Section = ClassRegistry::init('Section');
	} 

	/**
	 * set filter parameters for controller
	 * 
	 * @param array object $controller
	 */
	function startup(&$controller) {
		$this->controller 	= $controller;
	}

	/**
	 * clean filter
	 */
	public function cleanFilter() {
		$this->filter = array();
	}

	/**
	 * Tree of publications and sections for user connected
	 * 
	 * @return array
	 */
	public function getSectionsTree() {
		$conf  = Configure::getInstance() ;
		
		// Get connected user
		$userid = $this->controller->BeAuth->userid();
		$filter = array(
			"object_type_id" => array($conf->objectTypes['area']['id'],$conf->objectTypes['section']['id']) ,
			"count_permission" => true
		);
		$tree = $this->Tree->getAll(null, $userid, null, $filter) ;
		
		return $tree ;	
	}

	/**
	 * Tree of a publication and sections for user connected
	 * 
	 * @return array
	 */
	public function getPublicationTree($id) {
		$conf  = Configure::getInstance() ;
		
		// Get connected user
		$userid = $this->controller->BeAuth->userid();
		$filter = array(
			"object_type_id" => array($conf->objectTypes['area']['id'],$conf->objectTypes['section']['id']) ,
			"count_permission" => true
		);
		$tree = $this->Tree->getAll($id, $userid, null, $filter) ;
		
		return $tree ;	
	}

	/**
	 * get publication (area) data for the section $section_id
	 * 
	 * @param int $section_id
	 * @return array
	 */
	public function getAreaForSection($section_id) {
		$area = ClassRegistry::init('Area');
		$area->containLevel("minimum");
		$area_id = $this->Tree->getRootForSection($section_id);
		return $area->findById($area_id);
	}

	/**
	 * Get tree with one branch expanded (the branch where object $id is)
	 *
	 * @param integer $id
	 * @return array
	 */
	public function expandOneBranch($id = null) {
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
	 * @param integer $id node ID
	 * @param mixed $status
	 * @param mixed $filter
	 * @param mixed $order
	 * @param boolean $dir
	 * @param int $page
	 * @param int $dim
	 * @param array $excludeIds
	 * @return array
	 */
	public function getChildren($id = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = null, $excludeIds = array()) {
		$conf  = Configure::getInstance() ;
		// Get user connected
		$userid = $this->controller->BeAuth->userid();
		if (empty($userid)) {
			$userid = null;
		}
		$filter = ($filter)? array_merge($this->filter, $filter) : $this->filter;
		$objs = &  $this->Tree->getChildren($id, $userid, $status, $filter, $order, $dir, $page, $dim, $excludeIds) ;
		return $objs ;
	}

	/**
	 * Return descendants of a tree node
	 *
	 * @param integer $id node ID
	 * @param mixed $status
	 * @param mixed $filter
	 * @param mixed $order
	 * @param boolean $dir
	 * @param int $page
	 * @param int $dim
	 * @param array $excludeIds
	 * @return array
	 */
	public function getDescendants($id = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = null, $excludeIds = array()) {
		$conf  = Configure::getInstance() ;
		// Get user data
		$userid = $this->controller->BeAuth->userid();
		if (empty($userid)) {
			$userid = null;
		}
		$filter = ($filter)? array_merge($this->filter, $filter) : $this->filter;
		$objs = &  $this->Tree->getDescendants($id, $userid, $status, $filter, $order, $dir, $page, $dim, $excludeIds) ;
		return $objs ;
	}
	
	/**
	 * TODO: remove this method
	 * Array of parent objects of $id...
	 *
	 * @param integer $id
	 * @return array parents ids
	 */
	public function getParents($id = null, $area_id=null, $status = array()) {
		return $this->Tree->getParents($id, $area_id, $status);
	}

	/**
	 * TODO: remove this method. Used only in some addons shell scripts
	 * update tree position of object $id with new $destination array
	 *
	 * @param integer $id
	 * @param array $destination
	 */
	public function updateTree($id, $destination) {
		return $this->Tree->updateTree($id, $destination);
	}

	/**
	 * setup array of tree destinations (parent ids)
	 * if some parents is forbidden to user (backend_private permission)
	 * then add it to $destination because user can't edit that destination
	 *
	 * @param  int $objectId
	 * @param  array  $destination array of parent ids
	 */
	public function setupForSave($objectId, &$destination = array()) {
		$permission = ClassRegistry::init('Permission');
	    $parentIds = $this->Tree->find('list', array(
	    	'fields' => array('parent_id'),
	    	'conditions' => array('id' => $objectId)
	    ));
	    $userData = $this->controller->BeAuth->getUserSession();
	    foreach ($parentIds as $parent_id) {
	    	if ($permission->isForbidden($parent_id, $userData) && !in_array($parent_id, $destination)) {
	    		$destination[] = $parent_id;
	    	}
	    }
	}

	/////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////
	/**
	 * Delete branches that don't contain object $id
	 *
	 * @param array $tree	tree where to perform search
	 * @param integer $id	object to search
	 * @return boolean
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