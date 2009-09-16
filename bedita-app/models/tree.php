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
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Tree extends BEAppModel
{

	/**
	 * save: do nothing
	 */
	function save($data = null, $validate = true, $fieldList = array()) {
		return true ;
	}

	/**
	 * definisce la radice dell'albero
	 */
	function setRoot($id) {
		$this->id = $id ;
	}

	/**
	 * ritorna l'indice  della radice dell'albero
	 */
	function getRoot() {
		return $this->id  ;
	}

	/**
	 * Crea il clone di una determinata ramificazione.
	 *
	 * @param integer $newId	Id radice ramificazione clonata
	 * @param integer $id		Id ramificazione
	 */
	function cloneTree($newId, $id = null) {
		if (isset($id)) {
			$this->id = $id ;
		}
		if(!isset($this->id)) return false ;

		$id = $this->id ;

		$ret = $this->query("CALL cloneTree({$id}, {$newId})");
		return (($ret === false)?false:true) ;
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
	//TODO: when area_id will be managed change LIKE condition with area_id => $area_id condition
	function getParent($id, $area_id=null) {
		if (empty($id)) {
			return false;
		}
		$conditions["id"] = $id;
		if (!empty($area_id)) {
			$conditions[] = "parent_path LIKE '/" . $area_id . "/%'";	
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
		$path = $this->field("path", array("id"=>$id));
		if($path === false) {
			$this->log("No path found in Tree for id ".$id);
			throw new BeditaException(__("No path found in Tree for id", true)." ".$id);
		}
		$path = substr($path,1);
		$pos = strpos($path,"/");
		if($pos === false)
			return $path;
		return substr($path,0, $pos);
	}

	function appendChild($id, $idParent = null) {
		$idParent = (empty($idParent)) ? "NULL" :  $idParent ;

		$ret = $this->query("CALL appendChildTree({$id}, {$idParent})");
		return (($ret === false)?false:true) ;

	}

	function moveChildUp($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		$ret = $this->query("CALL moveChildTreeUp({$id}, {$this->id})");
		return (($ret === false)?false:true) ;
	}

	function moveChildDown($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		$ret = $this->query("CALL moveChildTreeDown({$id}, {$this->id})");
		return (($ret === false)?false:true) ;
	}

	function moveChildFirst($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		$ret = $this->query("CALL moveChildTreeFirst({$id}, {$this->id})");
		return (($ret === false)?false:true) ;
	}

	function moveChildLast($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		$ret = $this->query("CALL moveChildTreeLast({$id}, {$this->id})");
		return (($ret === false)?false:true) ;
	}

	function switchChild($id, $priority, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		$ret = $this->query("CALL switchChildTree({$id}, {$this->id}, {$priority})");
		return (($ret === false)?false:true) ;
	}
	
	function removeChild($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		$ret = $this->query("DELETE FROM trees WHERE id = {$id} AND parent_id = {$this->id}");
		return (($ret === false)?false:true) ;
	}

	function setPriority($id, $priority, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		$ret =  $this->query("UPDATE trees SET priority = {$priority} WHERE id = {$id} AND parent_id = {$this->id}");
		return (($ret === false)?false:true) ;
	}


	function move($idNewParent, $idOldParent, $id = NULL) {
		if (empty($id)) {
			$id = $this->id;
		}

		// Verifica che il nuovo parent non sia un discendente dell'albero da spostare
		$ret = $this->query("SELECT isParentTree({$id}, {$idNewParent}) AS parent");
		if(!empty($ret[0][0]["parent"])) return  false ;

 		$ret = $this->query("CALL moveTree({$id}, {$idOldParent}, {$idNewParent})");
		return (($ret === false)?false:true) ;
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

		$res = $this->findObjects($id, $userid, $status, $filter, "parent_path, priority",true,1,100000,true);

		foreach ($res["items"] as $root) {

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
		
		return $tree ;
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
	 * Riorganizza le posizioni dell'intero albero passato.
	 *
	 * @param array $tree	Item con l'albero.
	 * 						{0..N}:
	 * 							id			Id dell'elemento
	 * 							parent		Nuovo parent
	 * 							children	Elenco dei discendenti
	 */
	function moveAll(&$tree) {
		// Cerca tutti parent_id dei rami passati e il priority
		$IDs = array() ;
		$this->_getIDs($tree, $IDs) ;
		$this->_setPriority($tree) ;

		if(!count($IDs)) return true ;
		$IDs = implode(",", $IDs) ;
		$ret = $this->query("SELECT id, parent_id, priority FROM trees WHERE id IN ({$IDs})");

		$IDOldParents 	 = array() ;
		$IDOldPriorities = array() ;

		for($i=0; $i < count($ret) ; $i++) {
			$IDOldParents[$ret[$i]["trees"]["id"]] 		= $ret[$i]["trees"]["parent_id"] ;
			$IDOldPriorities[$ret[$i]["trees"]["id"]] 	= $ret[$i]["trees"]["priority"] ;
		}
		unset($IDs) ;
		unset($ret) ;

		// Salva le ramificazioni spostate
		$ret = $this->_moveAll($tree, $IDOldParents, $IDOldPriorities) ;

		return $ret ;
	}

	/**
	 * Preleva i path dell'oggetto passato dove al posto degli id
 	 * ci sono i nickname
 	 *
 	 * @param integer  $id
	 * @return array/string
	 */
	function getPathNickname($id) {
		if (isset($id)) {
			$this->id = $id ;
		}
		$id = $this->id ;

		if(($ret = $this->query("SELECT path FROM  trees WHERE id = {$id}")) === false) {
			return false ;
		}

		$IDs = array() ;
		$paths = array() ;
		
		// Preleva gli ID 
		if(!count($ret)) return false ;
		else if(count($ret) == 1) {
			$paths[] = $ret[0]['trees']['path'] ;
			$tmp = explode("/", $ret[0]['trees']['path']) ;
			foreach ($tmp as $id) {
				if(@empty($id)) continue ;
				$IDs[$id] = null ;
			}
		}
		else {
			$tmp = array() ;
			for($i=0; $i < count($ret) ; $i++) {
				$paths[] = $ret[$i]['trees']['path'] ;
				$tmp = explode("/", $ret[$i]['trees']['path']) ;
				foreach ($tmp as $id) {
					if(@empty($id)) continue ;
					$IDs[$id] = null ;
				}
			}
		}
		
		// Preleva i nickname
		$tmp = array() ;
		foreach ($IDs as $id => $value) {
			$tmp[] = $id ;
		}
		$tmp = implode(",", $tmp);
		if(($ret = $this->query("SELECT id, nickname FROM  objects WHERE id IN({$tmp}) ")) === false) {
			return false ;
		}
		for($i=0; $i < count($ret) ; $i++) {
			$IDs[$ret[$i]['objects']['id']] = $ret[$i]['objects']['nickname'] ;
		}
		
		// Trasforma i path
		for($i=0; $i < count($paths) ; $i++) {
			$tmp = explode("/", $paths[$i]);
		
			for($x=0; $x < count($tmp) ; $x++) {
				if(!isset($IDs[$tmp[$x]])) continue ;
				$tmp[$x] = $IDs[$tmp[$x]] ;
			}
			$paths[$i] = implode("/", $tmp) ;
		}
		
		return $paths ;
	}	

	function isParent($idParent, $id = NULL) {
		if (empty($id)) {
			$id = $this->id;
		}

		// Verifica che il nuovo parent non sia un discendente dell'albero da spostare
		$ret = $this->query("SELECT isParentTree({$idParent}, {$id}) AS parent");
		
		if(empty($ret[0][0]["parent"])) return  false ;

		return (($ret[0][0]["parent"])?true:false) ;
	}
	
	function isParentByNickname($nickname, $id = NULL) {
		if (empty($id)) {
			$id = $this->id;
		}

		// Verifica che il nuovo parent non sia un discendente dell'albero da spostare
		$ret = $this->query("SELECT id FROM objects where nickname = '{$nickname}' ");
		if(!isset($ret[0]['objects']["id"])) return  false ;

		return $this->isParent($ret[0]['objects']["id"] , $id) ;
	}
	
	
	private function _moveAll(&$tree, &$IDOldParents, &$IDOldPriorities) {

		for($i=0; $i < count($tree) ; $i++) {
			if($tree[$i]["parent"] != $IDOldParents[$tree[$i]["id"]]) {
				if(!$this->move($tree[$i]["parent"], $IDOldParents[$tree[$i]["id"]], $tree[$i]["id"])) return false ;
			}
			if($tree[$i]["priority"] != $IDOldParents[$tree[$i]["id"]] && isset($tree[$i]["parent"])) {
				if(!$this->switchChild($tree[$i]["id"], $tree[$i]["priority"], $tree[$i]["parent"])) return false ;
//				if(!$this->setPriority($tree[$i]["id"], $tree[$i]["priority"], $tree[$i]["parent"])) return false ;
			}

			if(!$this->_moveAll($tree[$i]["children"], $IDOldParents, $IDOldPriorities)) return false ;
		}

		return true ;
	}

	private function _getIDs(&$tree, &$IDs) {
		for($i=0; $i < count($tree) ; $i++) {
			$IDs[] = $tree[$i]["id"] ;

			$this->_getIDs($tree[$i]["children"], $IDs) ;
		}
	}

	private function _setPriority(&$tree) {
		for($i=0; $i < count($tree) ; $i++) {
			$tree[$i]["priority"] = $i+1 ;

			$this->_setPriority($tree[$i]["children"]) ;
		}
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

	/**
	 * Torna l'ID delll'oggetto con un determinato nickname che ha come parent.
	 * Se ci sono + figli con lo stesso nick, torna il primo
	 * parent_id
	 *
	 * @param string $nickname
	 * @param integer $parent_id		
	 */
	function getIdFromNickname($nickname, $parent_id = null) {
		if(isset($parent_id)) {
			$sql = "SELECT trees.id FROM
					trees INNER JOIN objects ON trees.id = objects.id AND parent_id = {$parent_id}
					WHERE
					nickname = '{$nickname}' LIMIT 1
			" ;
		} else {
			$sql = "SELECT trees.* FROM
					trees INNER JOIN objects ON trees.id = objects.id AND parent_id IS NULL 
					WHERE
					nickname = '{$nickname}' LIMIT 1
			" ;
		}
		
		$tmp  	= $this->query($sql) ;
		return ((isset($tmp[0]['trees']['id'])) ? $tmp[0]['trees']['id'] : null) ;
	}
	
	////////////////////////////////////////////////////////////////////////

	private function _getCondition_parentID(&$conditions, $id = null) {
		if(isset($this->id)) $conditions[] = array("parent_id" => $this->id) ;
		else $conditions[] = array("parent_id" => null);
	}

	private function _getCondition_parentPath(&$conditions, $id = null) {
		if(isset($id)) {
			$conditions[] = " path LIKE (CONCAT((SELECT path FROM trees WHERE id = {$id}), '/%')) " ;
		}
	}


}


?>
