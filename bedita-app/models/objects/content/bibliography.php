<?php
/**
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
 * 				Esprime  un'ntita' di tipo bibliografia.
 * 				Che ha tutte le caratteristiche di un contenuto ma con
 * 				e' anche un contenitore di oggetti specifici:
 * 				BiblioItem e Book		
*/
class Bibliography extends BeditaContentModel 
{
	/**
	 * Per cancellare gli oggetti BiblioItem associati
	 *
	 */	
	function beforeDelete() {
		$conf  	= Configure::getInstance() ;
		if(!class_exists('BiblioItem')) loadModel('BiblioItem') ;
		
		$this->getItems($this->{$this->primaryKey}, $children) ;
		for($i=0; $i < count($children) ; $i++) {
			$tmp = &$children[$i] ;
			$obj = null ;
			
			if($tmp['object_type_id'] != $conf->objectTypes['biblioitem']) {
				continue ;
			}
			
			$obj = new BiblioItem() ; 
			if(!$obj->delete($tmp['id'])) {
				return false ;
			}
			
			unset($obj) ;
		}
		
		return true ;
	}	
		
	/**
	 * Torna l'elenco degli item della bibliografia
	 *
	 * @param unknown_type $id
	 */
	function getItems($id = null, &$items) {
		$conf  	= Configure::getInstance() ;
		$items	= array() ;
		
		if(!class_exists('BiblioItem')) loadModel('BiblioItem') ;
		if(!class_exists('Book')) 		loadModel('Book') ;
		
		if(isset($id)) $this->id  = $id ;
		
		$id = $this->id ;
		
		$ret = $this->query("SELECT 
					objects.id, 
					objects.object_type_id,
					content_objects.priority
					FROM 
					content_objects INNER JOIN objects ON content_objects.id = objects.id 
					WHERE
					content_objects.object_id = {$id} AND switch = 'BIBLIOS' 
					ORDER BY priority"
				) ;
		if(!is_array($ret)) return false ;
		
		$hiddenField = array('Permission', 'Version', 'CustomProperties', 'Index', 'ObjectType');
		
		for($i=0; $i < count($ret) ; $i++) {
			$tmp = am($ret[$i]['content_objects'], $ret[$i]['objects']) ;
			$obj = null ;
			
			switch ($tmp['object_type_id']) {
				case $conf->objectTypes['biblioitem']: 	$obj = new BiblioItem() ; break ;
				case $conf->objectTypes['book']: 		$obj = new Book() ; break ;
			}
			
			$obj->bviorHideFields = $hiddenField ;
			if(!($items[] = $obj->findById($tmp['id']))) {
				return false ;
			}
			$items[count($items)-1]['priority'] = $tmp['priority'] ;
			
			unset($obj) ;
		}
		
		return true ;
	}
	

	function appendChild($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		
		$this->query("CALL appendChildBibliography({$id}, {$this->id})");
	}
	
	function moveChildUp($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}
		$this->query("CALL moveChildBibliographyUp({$id}, {$this->id})");
	}
	
	function moveChildDown($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}		
		$this->query("CALL moveChildBibliographyDown({$id}, {$this->id})");
	}

	function moveChildFirst($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}		
		$this->query("CALL moveChildBibliographyFirst({$id}, {$this->id})");
	}
	
	function moveChildLast($id, $idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}		
		$this->query("CALL moveChildBibliographyLast({$id}, {$this->id})");
	}

	function removeAllChildren($idParent = false) {
		if (!empty($idParent)) {
			$this->id = $idParent ;
		}		
		$this->query("CALL removeAllChildrenBibliography({$this->id})");
	}
	
	/**
	 * Un oggetto crea un clone di se stesso.
	 *
	 */
	function __clone() {
		$conf  	= Configure::getInstance() ;
		if(!class_exists('BiblioItem')) loadModel('BiblioItem') ;
		
		$idSourceObj = $this->{$this->primaryKey} ;
		
		// Clona l'oggetto
		parent::__clone();
		
		$idNewObj = $this->id ;
		
		// Associa gli items
		if(!$this->getItems($idSourceObj, $items)) {
			throw new BEditaErrorCloneException("Bibliography::getItems") ;
		}
		
		// I libri riassociati, gli item clonati
		for ($i=0; $i < count($items) ; $i++) {
			if($items[$i]['object_type_id'] == $conf->objectTypes['biblioitem']) {
				$item = new BiblioItem() ;
				$item->id 				= $items[$i]['id'] ;
				$item->bibliography_id 	= $idNewObj ;
				
				$clone = clone $item ;
				
				$idItem = $clone->id ;
			} else {
				$idItem = $items[$i]['id'] ;
				
				// Aggiunge il libro
				if($this->appendChild($idItem, $idNewObj)) {
					throw new BEditaErrorCloneException("Bibliography::appendChild") ;
				}
			}
			
		}
	}
}













?>
