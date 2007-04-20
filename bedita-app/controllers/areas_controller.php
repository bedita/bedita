<?php
/**
 * Modulo Aree.
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
class AreasController extends AppController {
	
	var $helpers 	= array('Bevalidation');
	var $uses	 	= array('Area');
	
	/**
	 * Nome modello
	 *
	 * @var string
	 */
	var $name = 'Areas' ;

	/**
	 * 
	 *
	 * @todo TUTTO
	 */
	function index() {
		
		// Verifica i permessi d'accesso
//		if(!$this->checkLogin()) return ;
		
		$this->Area->recursive = 0 ;
		if(($Aree = $this->Area->findAll()) === false) return false ;
//pr($Aree);
		// Setup dei dati da passare al template
		
		$this->set('Aree', 		$Aree);
	}
	
	
}

?>