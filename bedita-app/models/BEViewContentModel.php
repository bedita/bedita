<?php
/**
 * Classe base per tutte le operazioni sui diversi tipi di contenuto.
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

class BEViewContentModel extends BEAppModel
{
	var $contents 		= null ;
	var $content_types 	= null ;
	
	/**
	 * stringa utlizzata per la selezione di liste di contenuti filtrando
	 * per area o gruppo.
	 * Il settaggio dipende dalla clase vista derivata.
	 *
	 * @var string
	 */
	protected $__joinFragment = null ;
	
	
	function __construct() {
		parent::__construct() ;
		$this->contents 		= new _emptyAfterFindView ;
		$this->content_types 	= new _emptyAfterFindView ;
	}

	/**
	 * Torna una lista di contenuti. Il tipo di contenuti e' definito da $name.
	 * Il nome pu˜ essere cambiato dalle classe derivate.
	 *
	 * @param array $recordset		Dove inserire il risultato
	 * @param integer ida			Area di cui selezionare i contenuti
	 * @param integer idg			Gruppo di cui selezionare i contenuti. Ha la precedenza su idarea
	 * @param integer $page			Pagina dell'elenco richiesta
	 * @param integer $dim			Dimensione della pagina
	 * @param string $order			Campo su cui ordinare.Anche nome_campo + spazio + DESC
	 * @return boolean
	 */
	function listContents(&$recordset, $ida = null, $idg = null, $page = 1, $dim = 10, $order = null) 	{

		// verifica della presenza dei filtri
		if($idg) {
			$this->__setupJoinGroups() ;
			$conditions = array("ACG.group_id" => $idg)  ;  
		} else if($ida) {
			$this->__setupJoinAreas() ;
			$conditions = array("ACG.area_id" => $ida)  ;  
		} else {
			$conditions = null ;
		}
		
		// Esegue la ricerca
        if(($tmp = $this->findAll($conditions, null, $order, $dim, $page, 0)) === false) return false ;

		// Formatta il record set da tornare
		for ($i =0; $i < count($tmp); $i++) {
			$tmp[$i] = $this->am($tmp[$i]);
		}

		$recordset = array(
			"items"		=> &$tmp,
			"toolbar"	=> $this->toolbar($page, $dim, $conditions)
		) ;
		
		
		return true ;
	}
	
/**
 * Before find callback
 *
 * @param array $queryData Data used to execute this query, i.e. conditions, order, etc.
 * @return boolean True if the operation should continue, false if it should abort
 */
	function beforeFind(&$queryData) {
		$queryData["joins"] 	= array($this->__joinFragment) ;
		
		if(@empty($queryData["fields"]))
			$queryData["fields"] 	= " DISTINCT * " ;
		
		return true;
	}
	
	/**
	 * Sovrascritte dalle classi derivate
	 *
	 * @return unknown
	 */
	protected function __setupJoinGroups() 	{$this->__joinFragment = "" ; }
	
	protected function __setupJoinAreas() 	{$this->__joinFragment = "" ; }
}
?>
