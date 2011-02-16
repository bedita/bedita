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
 * Tree structure
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Tree extends BEAppModel
{

	public $primaryKey = "object_path";

	function beforeSave() {
		if (!empty($this->data["Tree"]) && empty($this->data["Tree"]["area_id"]) && !empty($this->data["Tree"]["object_path"])) {
			$this->data["Tree"]["area_id"] = $this->getAreaIdByPath($this->data["Tree"]["object_path"]);
		}
		return true;
	}

	/**
	 * get parent or array of parents
	 *
	 * @param integer $id
	 * @param integer $area_id, publication id: if defined search parent only inside the publication
	 * 
	 * @return mixed	integer, if only one parent founded
	 * 					array, if two or more parents founded
	 * 					false, error or none parent founded
	 */
	function getParent($id, $area_id=null) {
		if (empty($id)) {
			return false;
		}
		$conditions["id"] = $id;
		if (!empty($area_id)) {
			$conditions["area_id"] = $area_id;
		}
		
		$ret = $this->find("all", array(
				"conditions" => $conditions,
				"fields" => array("parent_id")
			)
		);
		
		if(!$ret) {
			return false;
		}

		if(!count($ret)) {
			return false ;
		} else if (count($ret) == 1) {
			return $ret[0]['Tree']['parent_id'] ;
		} else {
			$tmp = array() ;
			for($i=0; $i < count($ret) ; $i++) {
				$tmp[] = $ret[$i]['Tree']['parent_id'] ;
			}

			return $tmp ;
		}
	}

	function getRootForSection($id) {
		$area_id = $this->field("area_id", array("id"=>$id));
		return $area_id;
	}

	/**
	 * append an object to a parent in tree
	 *
	 * @param int $id object id
	 * @param int $idParent parent object id
	 * @return boolean
	 */
	function appendChild($id, $idParent = null) {
		// root
		if (empty($idParent)) {
			$data["Tree"] = array(
				"id" => $id,
				"area_id" => $id,
				"object_path" => "/".$id,
				"parent_path" => "/",
				"priority" => 1
			);
		} else {
			$parentPath = $this->field("object_path", array("id" => $idParent));
			$area_id = $this->getAreaIdByPath($parentPath);
			$maxPriority = $this->field("priority", array("parent_id" => $idParent), "priority DESC");
			$maxPriority = (!empty($maxPriority))? $maxPriority + 1 : 1;

			$data["Tree"] = array(
				"id" => $id,
				"area_id" => $area_id,
				"parent_id" => $idParent,
				"object_path" => $parentPath . "/".$id,
				"parent_path" => $parentPath,
				"priority" => $maxPriority
			);
		}

		$ret = $this->save($data);

		return (($ret === false)?false:true) ;

	}

	public function getAreaIdByPath($path) {
		$pathArr = explode("/", trim($path, "/"));
		return $pathArr[0];
	}

	/**
	 * move up or down a leaf tree inside a branch
	 *
	 * @param int $id object id to move
	 * @param int $idParent parent object (branch)
	 * @param boolean $up true move up (priority - 1), false move down (priority + 1)
	 * @return boolean
	 */
	public function movePriority($id, $idParent, $up=true) {
		$treeRow = $this->find("first", array(
			"conditions" => array("id" => $id, "parent_id" => $idParent)
		));

		if (empty($treeRow)) {
			return false;
		}

		$origPriority = $treeRow["Tree"]["priority"];

		if ($up) {
			$op = " < ";
			$dir = "DESC";
		} else {
			$op = " > ";
			$dir = "ASC";
		}
		$op = ($up)? " < " : " > ";
		$otherRow = $this->find("first", array(
			"conditions" => array("parent_id" => $idParent, "priority" . $op . $origPriority),
			"limit" => 1,
			"order" => "priority " . $dir
		));
		
		if (empty($otherRow["Tree"]["priority"])) {
			return false;
		}

		$treeRow["Tree"]["priority"] = $otherRow["Tree"]["priority"];
		$otherRow["Tree"]["priority"] = $origPriority;

		if (!$this->save($treeRow)) {
			return false;
		}
		$this->create();
		if (!$this->save($otherRow)) {
			return false;
		}

		return true;
	}

	public function movePriorityUp($id, $idParent) {
		return $this->movePriority($id, $idParent);
	}

	public function movePriorityDown($id, $idParent) {
		return $this->movePriority($id, $idParent, false);
	}
	
	function removeChild($id, $idParent) {
		$ret = $this->deleteAll(array("id" => $id, "parent_id" => $idParent));
		return (($ret === false)?false:true) ;
	}

	function setPriority($id, $priority, $idParent) {
		$row = $this->find("first", array(
			"conditions" => array(
				"id" => $id,
				"parent_id" => $idParent
			)
		));
		if (empty($row["Tree"])) {
			return false;
		}
		$row["Tree"]["priority"] = $priority;
		$ret =  $this->save($row);
		return (($ret === false)?false:true) ;
	}


	/**
	 * move branch to another parent
	 *
	 * @param int $idNewParent
	 * @param int $idOldParent
	 * @param int $id
	 * @return boolean
	 */
	function move($idNewParent, $idOldParent, $id) {
		// Verify that new parent is not a descendant on the tree to move
		if ($this->isParent($id, $idNewParent)) {
			return false;
		}

		$rowToMove = $this->find("first", array(
			"conditions" => array(
				"id" => $id,
				"parent_id" => $idOldParent
			)
		));

		$newParentRow = $this->find("first", array(
			"conditions" => array(
				"id" => $idNewParent
			)
		));

		$newParentPath = $newParentRow["Tree"]["object_path"];
		$newPath = $newParentPath . "/" . $rowToMove["Tree"]["id"];
		$oldPath = $rowToMove["Tree"]["object_path"];

		$children = $this->find("all", array(
			"conditions" => array("object_path LIKE" => $oldPath."/%")
		));

		if (!$this->delete($rowToMove["Tree"]["object_path"])) {
			return false;
		}

		$area_id = $this->getAreaIdByPath($newPath);
		$rowToMove["Tree"]["parent_path"] = $newParentPath;
		$rowToMove["Tree"]["object_path"] = $newPath;
		$rowToMove["Tree"]["parent_id"] = $idNewParent;
		$rowToMove["Tree"]["area_id"] = $area_id;

		$maxBranchPriority = $this->field("priority", array("parent_id" => $idNewParent), "priority DESC");
		$rowToMove["Tree"]["priority"] = (empty($maxBranchPriority))? 1 : $maxBranchPriority + 1;

		$this->create();
		if (!$this->save($rowToMove)) {
			return false;
		}

		foreach ($children as $child) {
			if (!$this->delete($child["Tree"]["object_path"])) {
				return false;
			}
			$child["Tree"]["parent_path"] = str_replace($oldPath, $newPath, $child["Tree"]["parent_path"]);
			$child["Tree"]["object_path"] = str_replace($oldPath."/", $newPath."/", $child["Tree"]["object_path"]);
			$child["Tree"]["area_id"] = $area_id;
			$this->create();
			if (!$this->save($child)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get Tree where 'id' is root.
	 * @param integer $id		root id.
	 * @param string $userid	user. if null: no permission check (default); if '': guest user
	 * @param string $status	only objs with this status 
	 * @param array $filter		see BEAppModel::findObjects
	 */
	function getAll($id = null, $userid = null, $status = null, $filter = array()) {

		// build tree
		$roots 	= array() ;
		$tree 	= array() ;
		
		if (empty($id)) {
			$filter["Tree.*"] = "";
		}

		$res = $this->findObjects($id, $userid, $status, $filter, "parent_path, priority, title",true,1,100000,true);
		$tree = $this->buildTree($res["items"]);
		return $tree ;
	}

	public function buildTree($items) {
		$tree = array();
		foreach ($items as $root) {

			$root['children']	= array() ;
			$roots[$root['id']] = &$root ;

			if(isset($root['parent_id']) && isset($roots[$root['parent_id']])) {
				$roots[$root['parent_id']]['children'][] = &$root ;
			} elseif (!empty($root['parent_id'])) {
				$this->putBranchInTree($tree, $root);
			} elseif ( (empty($id) && $root["object_type_id"] == Configure::read("objectTypes.area.id"))
					|| ($id == $root["id"]) ) {
				$tree[] = &$root;
			}

			unset($root);
		}
		return $tree;
	}

	/**
	 * search where have to stay $branch in $tree and put in  
	 * @param array $tree
	 * @param array $branch to put in tree 
	 */
	private function putBranchInTree(&$tree, $branch) {
		foreach ($tree as $k => $t) {
			if (!empty($branch['parent_id']) && $t["id"] == $branch['parent_id']) {
				$tree[$k]['children'][] = $branch;
			} elseif (!empty($t['children'])) {
				$this->putBranchInTree($t['children'], $branch);
			}
		}
	}

	/**
	 * check if $idParent is an ancestor
	 *
	 * @param integer $idParent
	 * @param integer $id
	 * @return boolean
	 */
	function isParent($idParent, $id) {
		$c = $this->find("count", array(
			"conditions" => array(
				"parent_path LIKE" => "%/" . $idParent . "/%",
				"id" => $id
			)
		));

		if ($c === 0) {
			return false;
		}

		return true;
	}

	/**
	 * check if an object is on the tree
	 *
	 * @param integer $id
	 * @param integer $area_id if defined check if the object is a descendant of a publication
	 * @return boolean
	 */
	public function isOnTree($id, $area_id=null) {
		$conditions["id"] = $id;
		if (!empty($area_id)) {
			$conditions["area_id"] = $area_id;
		}
		$c = $this->find("count", array("conditions" => $conditions));
		if ($c === 0) {
			return false;
		}
		return true;
	}

	/**
	 * Children of id element (only 1 level in tree).
	 * If userid != null, only objects with read permissione for user, if ' ' - use guest/anonymous user,
	 * if userid = null -> no permission check.
	 * Filter: object types, search text query.
	 *
	 * @param integer $id		root id
	 * @param string $userid	user: null (default) => no permission check. ' ' => guest/anonymous user,
	 * @param string $status	object status
	 * @param array  $filter	Filter: object types, search text query, eg. array(21, 22, "search" => "text to search").
	 * 							Default: all object types
	 * @param string $order		field to order result (id, status, modified..)
	 * @param boolean $dir		true (default), ascending, otherwiese descending.
	 * @param integer $page		Page number (for pagination)
	 * @param integer $dim		Page dim (for pagination)
	 */
	function getChildren($id = null, $userid = null, $status = null, $filter = array(), $order = null, $dir  = true, $page = 1, $dim = 100000) {
		return $this->findObjects($id, $userid, $status, $filter, $order, $dir, $page, $dim, false) ;
	}

	/**
	 * Descendants of id element (all elements in tree).
	 * (see: BEObject->find(), to search not using content tree ).
	 * If userid present, only objects with read permissione, if ' ' - guest/anonymous user,
	 * if userid = null -> no permission check.
	 * Filter: object types, search text query.
	 *
	 * @param integer $id		root id
	 * @param string $userid	user: null (default) => no permission check. ' ' => guest/anonymous user,
	 * @param string $status	object status
	 * @param array  $filter	Filter: object types, search text query, eg. array(21, 22, "search" => "text to search").
	 * 							Default: all object types
	 * @param string $order		field to order result (id, status, modified..)
	 * @param boolean $dir		true (default), ascending, otherwiese descending.
	 * @param integer $page		Page number (for pagination)
	 * @param integer $dim		Page dim (for pagination)
	 */
	function getDescendants($id = null, $userid = null, $status = null, $filter = array(), $order = null, $dir  = true, $page = 1, $dim = 100000) {
		return $this->findObjects($id, $userid, $status, $filter, $order, $dir, $page, $dim, true) ;
	}

}


?>
