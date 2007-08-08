<?php
/**
 *
 * PHP versions 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2007
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license
 * @author 		giangi giangi@qwerg.com	
 * 		
 * 				Implementa le operazioni di:
 * 				Inserimento nell'albero, cancellazione, spostamento di ramificazione
 * 				Le operazioni di save e delete vengono completamente  ridefinite.
*/
class Tree extends BEAppModel 
{
	var $name 		= 'Tree';
	var $useTable	= "view_trees" ;
	
	/**
	 * save.
	 * Non fa niente
	 * 
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
	 * Torna il parent/ i parent dell'albero
	 *
	 * @param integer $id
	 * 
	 * @return mixed	integer, se solo un parent
	 * 					array, se inserito in + parent
	 * 					false, in caso d'errore
	 */
	function getParent($id = null) {
		if (isset($id)) {
			$this->id = $id ;
		}
		$id = $this->id ;
		
		if(($ret = $this->query("SELECT parent_id FROM  trees WHERE id = {$id}")) === false) {
			return false ;
		}
		
		if(!count($ret)) return false ;
		else if(count($ret) == 1) return $ret[0]['trees']['parent_id'] ;
		else {
			$tmp = array() ;
			for($i=0; $i < count($ret) ; $i++) {
				$tmp[] = $ret[$i]['trees']['parent_id'] ;
			}
			
			return $tmp ;
		}
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

	function setPriority($id, $priority, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}		
		$ret =  $this->query("UPDATE trees SET priority_s = {$priority} WHERE id = {$id} AND parent_id = {$this->id}");
		return (($ret === false)?false:true) ;
	}
	
	
	function move($idNewParent, $idOldParent, $id = false) {
		if (!empty($id)) {
			$this->id = $id ;
		}
		
		// Verifica che il nuovo parent non sia un discendente dell'albero da spostare
		$ret = $this->query("SELECT isParentTree({$this->id}, {$idNewParent}) AS parent");
		if(!empty($ret["parent"])) return  false ;
		
 		$ret = $this->query("CALL moveTree({$this->id}, {$idOldParent}, {$idNewParent})");
		return (($ret === false)?false:true) ;
	}
	
	/**
	 * Cancella il ramo con la root con un determinato id.
	 */
	function del($id = null, $cascade = true) {
		if (!empty($id)) {
			$this->id = $id;
		}
		$id = $this->id;		
		$ret = $this->query("CALL deleteTree({$id})");
		return (($ret === false)?false:true) ;
	}

	/**
	 * Preleva l'abero di cui id  la radice.
	 * Se l'userid e' presente, preleva solo gli oggetti di cui ha i permessi, se ''  un utente anonimo,
	 * altrimenti li prende tutti.
	 * Si possono selezionare i tipi di oggetti da inserire nell'albero.
	 * 
	 * @param integer $id		id della radice da selezionare. 
	 * @param string $userid	l'utente che accede. Se null: non controlla i permessi. Se '': uente guest.
	 * 							Default: non verifica i permessi.
	 * @param string $status	Prende oggetti solo con lo status passato
	 * @param array $filter		definisce i tipi gli oggetti da prelevare. Es.:
	 * 							1 | 3 | 22 ... aree, sezioni, documenti.
	 * 							Default: tutti.
	 */
	function getAll($id = null, $userid = null, $status = null, $filter = 0xFF) {
		$fields  = " * " ;
		
		// Setta l'id
		if (!empty($id)) {
			$this->id = $id;
		}

		// setta le condizioni di ricerca
		$conditions = array() ;
		if($filter) {
			$conditions[] = array("object_type_id" => " (object_type_id & {$filter})  ") ;
		}

		if(isset($userid)) {
			// Preleva i permessi dell'utente sugli oggetti selezionati
			$fields 		= " Tree.*, prmsUserByID ('{$userid}', id, 15) as perms " ;
			$conditions[] 	= " prmsUserByID ('{$userid}', id, ".BEDITA_PERMS_READ.") > 0 " ;
		}
			
		if(isset($status)) {
			$conditions[] = " status = '{$status}' " ;
		}

		if(isset($id)) {
			$conditions[] = " path LIKE (CONCAT((SELECT path FROM trees WHERE id = {$id}), '%')) " ;
		}
		
		// Esegue la ricerca
		$db 		 =& ConnectionManager::getDataSource($this->useDbConfig);
		$sqlClausole = $db->conditions($conditions, false, true) ;
		
		$records  = $this->execute("SELECT {$fields} FROM view_trees AS Tree {$sqlClausole}") ;
		
		// Costruisce l'albero
		$roots 	= array() ;
		$tree 	= array() ;
		$size	= count($records) ;
		
		for ($i=0; $i < $size ; $i++) {
			if(isset($records[$i]['Tree'])){
				$root = am($records[$i]['Tree'], (isset($records[$i][0])?$records[$i][0] :array())) ;
			} else {
				$root = am($records[$i]['trees'], $records[$i]['objects'], (isset($records[$i][0])?$records[$i][0] :array())) ;
			}
			
			$root['children']	= array() ;
			$roots[$root['id']] = &$root ;
			
			if(isset($root['parent_id']) && isset($roots[$root['parent_id']])) {
				$roots[$root['parent_id']]['children'][] = &$root ;
			} else {
				$tree[] = &$root ; 
			}
			
			unset($root);
		}
		
		// scarta tutti i rami che non sono root e che non coincidono con $id
		// sono rami su cui l'utente non ha permessi sui parent
		$tmp = array() ;
		for ($i=0; $i < count($tree) ; $i++) {
			if(isset($id) && $tree[$i]['id'] == $id) {
				$tmp[] = &$tree[$i] ;
				continue ;
			}

			if(!isset($id) && empty($tree[$i]['parent_id'])) {
				$tmp[] = &$tree[$i] ;

				continue ; 
			}
		}
		
		return $tmp ;
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
	
	
/*	
	function getChildren($id, $hiddenField = null, $compact = true) {
		if (!empty($id)) {
			$this->id = $id;
		}
		$id = $this->id;		
		$ret = $this->query("SELECT id, path, priority FROM tree WHERE parent_id = '{$id}' ORDER BY priority ");
		
		return $ret ;
	}
*/


}


?>