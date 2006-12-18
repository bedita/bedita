<?php
/**
 * Modulo Cronologia.
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
class TimelinesController extends AppController {
	var $components = array('BeAuth');
	var $helpers 	= array('Bevalidation');
	var $uses	 	= array('Area');
	
	/**
	 * Nome modello
	 *
	 * @var string
	 */
	var $name = 'Timelines' ;

	/**
	 * Definisce l'utilizzo di Smarty
	 *
	 */
	function __construct() {
		parent::__construct() ;
		$this->view 	= 'Smarty';
	}

	/**
	 * 
	 *
	 * @todo TUTTO
	 */
	function index() {
		
		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;
	
		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;
	}

}

?>