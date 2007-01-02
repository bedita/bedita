<?php
/**
 *
 * PHP versions 4 
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2006
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
*/
class Area extends BEAppModel
{
	var $name = 'Area';
	
	/**
	 * costanti che definiscono il tipo di gruppi da scegliere
	 */
	const SUBJECT 	= 1 ;
	const TIPOLOGY 	= 2 ;
	const CATEGORY 	= 3 ;
	const SECTION 	= 4 ;
	
	
    var $hasAndBelongsToMany = array('Group' =>
                               array('className'    => 'Group',
                                     'foreignKey'	=> 'area_id',
									 'joinTable'	=> 'areas_contents_groups'
                               )
    );
	
	
	function __construct() {
		parent::__construct() ;
	}


	/**
	 * Torna l'elenco delle aree con i gruppi connessi
	 * dell'area selezionata
	 *
	 * @param mixed	$tree	Dove tornare il risultato
	 * @param integer $type	Tipologia del gruppo da selezionare
	 * @param integer $id	ID dell'area da espandere. 0: nessuna; FF: tutte;integer un'area
	 * 
	 * @todo TUTTO
	 */
	function tree(&$tree, $type = Area::SUBJECT, $id = 0) {
		$tree = array();
		if(!($tree = $this->findAll())) return false ;
		
		// se nn c'e' da espandere nessuna area, torna il risultato
		if(!$id) return true ; 
		
		switch ($type) {
			case Area::SUBJECT: {
				$groups		= new ViewSubject() ;
				$nameGroups	= "Groups" ;				
			} break ;
			case Area::TIPOLOGY: {
				$groups		= new ViewTipology() ;
				$nameGroups	= "Groups" ;				
			} break ;
			case Area::CATEGORY: {
				$groups		= new ViewCategory() ;
				$nameGroups	= "Groups" ;				
			} break ;
			case Area::SECTION: {
				$groups		= new ViewSection() ;
				$nameGroups	= "Groups" ;				
			} break ;
			default: {
				$groups		= new ViewSubject ;
				$nameGroups	= "Groups" ;
			} break ;
			
		}
		
		for ($i=0; $i < count($tree) ; $i++) {
			if($id == 0xFF || $tree[$i]["Area"]["id"] == $id) {
				$groups->findFromArea($tmp, $tree[$i]["Area"]["id"]) ;
				$tree[$i][$nameGroups] = $tmp ;				
			}
		}
	}


/*	
	function listUser(&$recordset, $page = null, $dim = null, $order = null) {
		if(($tmp = $this->findAll(null, null, $order, $dim, $page, 0)) === false) return false ;
		
		$recordset = array(
			"items"		=> &$tmp,
			"toolbar"	=> $this->toolbar($page, $dim)
		) ;
		
		return true ;
	}

	function view(&$user, $id) {
		$this->id 	= $id;
		if( !($user = $this->read()) ) return false ;
		
		for($i=0; $i < count($user["Module"]) ; $i++) {
			$user["Module"][$i] = $user["Module"][$i]["id"] ;
		}
		
		return true ;
	}
*/
}
?>
