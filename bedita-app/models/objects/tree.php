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
	
	function removeChild($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		$ret = $this->query("DELETE FROM trees WHERE id = {$id} AND parent_id = {$this->id}");
		return (($ret === false)?false:true) ;
	}

	function removeChildren($idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		
		// preleva i figli
		$conditions = array() ;
		$this->_getCondition_parentID($conditions, $this->id) ;
		$sqlClausole = ConnectionManager::getDataSource($this->useDbConfig)->conditions($conditions, true, true) ;
		
		$children = $this->execute("SELECT id FROM view_trees {$sqlClausole}") ;
		
		// Cancella i rami di cui i figli sono radice
		for ($i =0; $i < count($children); $i++) {
			$tmp = $this->am($children[$i]);
			
			if($this->query("CALL deleteTreeWithParent({$tmp['id']}, {$this->id})") === false) return false ;
		}
				
		return true ;
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
		if(!empty($ret["parent"])) return  false ;

 		$ret = $this->query("CALL moveTree({$id}, {$idOldParent}, {$idNewParent})");
		return (($ret === false)?false:true) ;
	}

	/**
	 * Cancella il ramo con la root con un determinato id.
	 */
	function del($id = null, $idParent = null) {
		if (!empty($id)) {
			$this->id = $id;
		}
		$id = $this->id;
		if(isset($idParent)) $ret = $this->query("CALL deleteTreeWithParent({$id}, {$idParent})");
		else $ret = $this->query("CALL deleteTree({$id})");
		
		return (($ret === false)?false:true) ;
	}

	/**
	 * Preleva l'abero di cui id  la radice.
	 * Se l'userid e' presente, preleva solo gli oggetti di cui ha i permessi, se ''  un utente anonimo,
	 * altrimenti li prende tutti.
	 * Si possono selezionare i tipi di oggetti da inserire nell'albero.
	 *
	 * @param integer $id		id della radice da selezionare.
	 * @param string $userid	l'utente che accede. Se null: non controlla i permessi. Se '': utente guest.
	 * 							Default: non verifica i permessi.
	 * @param string $status	Prende oggetti solo con lo status passato
	 * @param array $filter		definisce i tipi gli oggetti da prelevare. Es.:
	 * 							1,3, 22 ... aree, sezioni, documenti.
	 * 							Default: tutti. (false)
	 */
	function getAll($id = null, $userid = null, $status = null, $filter = false) {
		$fields  = " * " ;

		// Setta l'id
		if (!empty($id)) {
			$this->id = $id;
		}

		// setta le condizioni di ricerca
		$conditions = array() ;
		$this->_getCondition_filterType($conditions, $filter) ;
		$this->_getCondition_userid($conditions, $userid ) ;
		$this->_getCondition_status($conditions, $status) ;
		$this->_getCondition_parentPath($conditions, $id) ;

		// Esegue la ricerca
		$db 		 =& ConnectionManager::getDataSource($this->useDbConfig);
//		$sqlClausole = $db->conditions($conditions, false, true) ;
		$sqlClausole = $db->conditions($conditions, true, true) ;

		$records  = $this->execute("SELECT {$fields} FROM view_trees AS Tree {$sqlClausole}") ;

		// Costruisce l'albero
		$roots 	= array() ;
		$tree 	= array() ;
		$size	= count($records) ;

		for ($i=0; $i < $size ; $i++) {
			if(isset($records[$i]['Tree'])){
				$root = am($records[$i]['Tree'], (isset($records[$i][0])?$records[$i][0] :array())) ;
			} else {

				if(!isset($records[$i]['trees'])) $records[$i]['trees'] = "";
				if(!isset($records[$i]['objects'])) $records[$i]['objects'] = "";
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

	/**
	 * Preleva i figli di cui id e' radice.
	 * Se l'userid e' presente, preleva solo gli oggetti di cui ha i permessi, se ''  un utente anonimo,
	 * altrimenti li prende tutti.
	 * Si possono selezionare i tipi di oggetti da prelevare.
	 *
	 * @param integer $id		id della radice da selezionare.
	 * @param string $userid	l'utente che accede. Se null: non controlla i permessi. Se '': utente guest.
	 * 							Default: non verifica i permessi.
	 * @param string $status	Prende oggetti solo con lo status passato
	 * @param array $filter		definisce i tipi gli oggetti da prelevare. Es.:
	 * 							1, 3, 22 ... aree, sezioni, documenti.
	 * 							Default: tutti.
	 * @param string $order		Campo testuale su cui ordinare il risultato
	 * @param boolean $dir		TRUE, ordine ascenedente, altrimenti discendente. Default: TRUE
	 * @param integer $page		Numero di pagina da selezionare
	 * @param integer $dim		Dimensione della pagina
	 */
	function getChildren($id = null, $userid = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000) {
		return $this->_getChildren($id, $userid, $status, $filter, $order, $dir, $page, $dim, false) ;
	}

	/**
	 * Preleva i discendenti di cui id e' radice.
	 * (vedere: beobject->find(), per ricerche al di fuori dell'albero dei contenuti ).
	 * Se l'userid e' presente, preleva solo gli oggetti di cui ha i permessi, se ''  un utente anonimo,
	 * altrimenti li prende tutti.
	 * Si possono selezionare i tipi di oggetti da prelevare.
	 *
	 * @param integer $id		id della radice da selezionare.
	 * @param string $userid	l'utente che accede. Se null: non controlla i permessi. Se '': utente guest.
	 * 							Default: non verifica i permessi.
	 * @param string $status	Prende oggetti solo con lo status passato
	 * @param array $filter		definisce i tipi gli oggetti da prelevare. Es.:
	 * 							1, 3,  22 ... aree, sezioni, documenti.
	 * 							Default: tutti.
	 * @param string $order		Campo testuale su cui ordinare il risultato
	 * @param boolean $dir		TRUE, ordine ascenedente, altrimenti discendente. Default: TRUE
	 * @param integer $page		Numero di pagina da selezionare
	 * @param integer $dim		Dimensione della pagina
	 */
	function getDiscendents($id = null, $userid = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000) {
		return $this->_getChildren($id, $userid, $status, $filter, $order, $dir, $page, $dim, true) ;
	}

	function findCount($conditions = null, $recursive = null) {
		$from = "view_trees AS Tree INNER JOIN objects ON Tree.id = objects.id" ;
		list($data)  = $this->execute("SELECT COUNT(*) AS count FROM {$from} {$conditions}") ;

		if (isset($data[0]['count'])) {
			return $data[0]['count'];
		} elseif (isset($data[$this->name]['count'])) {
			return $data[$this->name]['count'];
		}

		return false;
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
		
		$tmp  	= $this->execute($sql) ;
		return ((isset($tmp[0]['trees']['id'])) ? $tmp[0]['trees']['id'] : null) ;
	}
	
	////////////////////////////////////////////////////////////////////////
	/**
	 * Preleva i figli/discendenti di cui id e' radice.
	 * Se l'userid e' presente, preleva solo gli oggetti di cui ha i permessi, se ''  un utente anonimo,
	 * altrimenti li prende tutti.
	 * Si possono selezionare i tipi di oggetti da prelevare.
	 *
	 * @param integer $id		id della radice da selezionare.
	 * @param string $userid	l'utente che accede. Se null: non controlla i permessi. Se '': utente guest.
	 * 							Default: non verifica i permessi.
	 * @param string $status	Prende oggetti solo con lo status passato
	 * @param array $filter		definisce i tipi gli oggetti da prelevare. Es.:
	 * 							1, 3, 22 ... aree, sezioni, documenti.
	 * 							Default: tutti.
	 * @param string $order		Campo testuale su cui ordinare il risultato
	 * @param boolean $dir		TRUE, ordine ascenedente, altrimenti discendente. Default: TRUE
	 * @param integer $page		Numero di pagina da selezionare
	 * @param integer $dim		Dimensione della pagina
	 * @param boolean $all		Se true, prende anche i discendenti
	 */
	function _getChildren($id, $userid, $status, $filter, $order, $dir, $page, $dim, $all) {
		
		// Setta l'id
		if (!empty($id)) {
			$this->id = $id;
		}

		if(!isset($userid)) {
			$fields 		= " *, prmsUserByID ('{$userid}', id, 15) as perms " ;
		} else {
			$fields  = " * " ;
		}
		
		// setta le condizioni di ricerca
		$conditions = array() ;
		$this->_getCondition_filterType($conditions, $filter) ;
		$this->_getCondition_userid($conditions, $userid ) ;
		$this->_getCondition_status($conditions, $status) ;
		$this->_getCondition_current($conditions, true) ;

		if($all) $this->_getCondition_parentPath($conditions, $id) ;
		else $this->_getCondition_parentID($conditions, $id) ;

		// Costruisce i criteri di ricerca
		$db 		 =& ConnectionManager::getDataSource($this->useDbConfig);
		$sqlClausole = $db->conditions($conditions, true, true) ;
		
		// Clausola ordinamento
		$ordClausole  = "" ;
		if(is_string($order) && strlen($order)) {
			if($this->hasField($order)) {
				$order = "Tree.{$order}" ;
			} else {
				loadModel("BEObject") ;			
				$obj = new BEObject() ;
				
				if($obj->hasField($order)) {
					$order = "objects.{$order}" ;
				}
			}
			
			$ordClausole = " ORDER BY {$order} " . ((!$dir)? " DESC " : "") ;
		}

		// costruisce il join dalle tabelle
		$from = "view_trees AS Tree INNER JOIN objects ON Tree.id = objects.id" ;
		
		
		// Esegue la ricerca
		$limit 	= $this->_getLimitClausole($page, $dim) ;
		$tmp  	= $this->execute("SELECT {$fields} FROM {$from} {$sqlClausole} {$ordClausole} LIMIT {$limit}") ;
		
		// Torna il risultato
		$recordset = array(
			"items"		=> array(),
			"toolbar"	=> $this->toolbar($page, $dim, $sqlClausole)
		) ;
		for ($i =0; $i < count($tmp); $i++) {
			$recordset['items'][] = $this->am($tmp[$i]);
		}

		return $recordset ;
	}

	private function _getLimitClausole($page = 1, $dim = 100000) {
		// Esegue la ricerca
		if($page > 1) $offset = ($page -1) * $dim ;
		else $offset = null ;
		$limit = isset($offset)? "$offset, $dim" : "$dim";

		return $limit ;
	}


	private function _getCondition_filterType(&$conditions, $filter = false) {
		if(!$filter) return ;
		$conditions['Tree.object_type_id'] = $filter ;
	}

	private function _getCondition_userid(&$conditions, $userid = null) {
		if(!isset($userid)) return ;

		$conditions[] 	= " prmsUserByID ('{$userid}', Tree.id, ".BEDITA_PERMS_READ.") > 0 " ;
	}

	private function _getCondition_status(&$conditions, $status = null) {
		if(!isset($status)) return ;

		$conditions[] = array('status' => "'$status'") ;
	}

	private function _getCondition_parentID(&$conditions, $id = null) {
		if(isset($this->id)) $conditions[] = array("parent_id" => $this->id) ;
		else $conditions[] = array("parent_id" => null);
	}

	private function _getCondition_parentPath(&$conditions, $id = null) {
		if(isset($id)) {
			$conditions[] = " path LIKE (CONCAT((SELECT path FROM trees WHERE id = {$id}), '%')) " ;
		}
	}

	private function _getCondition_current(&$conditions, $current = true) {
		if(!$current) return ;
		$conditions[] = array("objects.current" => 1);
	}

}


?>
