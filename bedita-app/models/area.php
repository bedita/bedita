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
require_once("view_category.php") ;
require_once("view_section.php") ;
require_once("view_subject.php") ;
require_once("view_tipology.php") ;

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
	 * Torna l'elenco delle aree con i gruppi connessi nell'array Groups utilizzando join con una vista
	 *
	 * @param mixed	$tree	Dove tornare il risultato
	 * @param integer $type	Tipologia del gruppo da selezionare
	 * @param integer $id	ID dell'area da espandere. 0: nessuna; FF: tutte;integer un'area
	 * 
	 */
	function tree($type = Area::SUBJECT, $id = 0) {
		$tree = array();
		
		$this->unbindModel(array('hasAndBelongsToMany' => array('Group')));
		
		if(!($tree = $this->findAll())) return false ;
		
		// se nn c'e' da espandere nessuna area, torna il risultato
		if(!$id) return $tree ; 
		
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
			$tmp = array();
			if($id == 0xFF || $tree[$i]["Area"]["id"] == $id) {
				$groups->findFromArea($tmp, $tree[$i]["Area"]["id"]) ;
				$tree[$i][$nameGroups] = $tmp ;
			}				
		}
		
		return $tree;
	}

	/**
	 * ATTENZIONE QUESTO METODO FUNZIONA SOLO DA MySQL 5.0.33 circa in avanti
	 * 
	 * Appena possibile inizierei ad usare questo metodo che comporta l'eliminazione di tutti i require all'inizio
	 * del file ed è più pulito e performante
	 * 
	 * Torna l'elenco delle aree con i gruppi connessi nell'array Groups utilizzando join con una vista
	 *
	 * @param integer $type	Tipologia del gruppo da selezionare
	 * @param integer $id	ID dell'area da espandere. 0: nessuna; FF: tutte;integer un'area
	 * 
	*/
//	function tree($type = Area::SUBJECT, $id = 0) {
//		
//		switch ($type) {
//			case Area::SUBJECT:
//				$SQLview = 'ViewSubject';
//				break;
//			case Area::TIPOLOGY:
//				$SQLview = 'ViewTipology';
//				break;
//			case Area::CATEGORY:
//				$SQLview = 'ViewCategory';
//				break;
//			case Area::SECTION:
//				$SQLview = 'ViewSection';
//				break;
//			default:
//				$SQLview = 'ViewSubject';
//				break;
//		}
//		
//		$this->unbindModel(array('hasAndBelongsToMany' => array('Group')));
//		$this->bindModel(array('hasMany' => array(
//                							'Groups' => array('className' => $SQLview)
//            							)
//        				));
//        				
//        return $this->findAll();
//		
//	}


}
?>
