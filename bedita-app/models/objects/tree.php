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
	
	function appendChild($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		
		$this->query("CALL appendTree({$id}, {$this->id})");
	}
	
	function moveChildUp($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		$this->query("CALL moveChildTreeUp({$id}, {$this->id})");
	}
	
	function moveChildDown($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}		
		$this->query("CALL moveChildTreeDown({$id}, {$this->id})");
	}

	function moveChildFirst($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}		
		$this->query("CALL moveChildTreeFirst({$id}, {$this->id})");
	}
	
	function moveChildLast($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}		
		$this->query("CALL moveChildTreeLast({$id}, {$this->id})");
	}

	function move($idNewParent, $id = false) {
		if (!empty($id)) {
			$this->id = $id ;
		}
		
		// Verifica che il nuovo parent non sia un discendente dell'albero da spostare
		$ret = $this->query("SELECT isParentTree({$this->id}, {$idNewParent}) AS parent");
		if(!empty($ret["parent"])) return  false ;
		
 		$this->query("CALL moveTree({$this->id}, {$idNewParent})");
 		
 		return true ;
	}
	
	/**
	 * Cancella il ramo con la root con un determinato id.
	 */
	function del($id = null, $cascade = true) {
		if (!empty($id)) {
			$this->id = $id;
		}
		$id = $this->id;		
		$this->query("CALL deleteTree({$id})");
	}
	
	function getChildren($id, $hiddenField = null, $compact = true) {
		if (!empty($id)) {
			$this->id = $id;
		}
		$id = $this->id;		
		$ret = $this->query("SELECT id, path, priority FROM tree WHERE parent_id = '{$id}' ORDER BY priority ");
		
		return $ret ;
	}
	
}


?>