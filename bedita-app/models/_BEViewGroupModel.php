<?php
/**
 *
 * Verificare protabilita' cambio DB... 
 * Cambiare se cambia la query SQL della vista
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
class BEViewGroupModel extends BEAppModel
{
//	var $name = 'ViewSubject';
	
	var $ACG 			= null ;
	var $areas 			= null ;
	var $groups 		= null ;
	var $group_types	= null ;
	var $LT 			= null ;
		
	function __construct() {
		parent::__construct() ;
		
		$this->ACG 			= new _emptyAfterFindView ;
		$this->areas 		= new _emptyAfterFindView ;
		$this->groups 		= new _emptyAfterFindView ;
		$this->group_types	= new _emptyAfterFindView ;
		$this->LT 			= new _emptyAfterFindView ;
	}

	/**
	 * Torna i gruppi di una determinata area.
	 * Il tipo di gruppo dipende dalal classe derivata
	 * 
	 * @param mixed list 	dove tornare il risultato 
	 * @param integer id 	area
	 * 
	 * @return boolean   
	 */
	function findFromArea(&$list, $id) {
		$condition = array("area_id" => $id) ;
		if(!($list = $this->findAll($condition, null, "prior", null, null, 0))) return false ;
	
		for ($i=0; $i < count($list) ; $i++) {
			$list[$i] = $this->am($list[$i]) ;
		}
				
		return true ;	
	}
	
}
?>
