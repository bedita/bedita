<?php
/**
 * Modulo moduli.
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

class ModulesController extends AppController {
	var $name = 'Modules';
	
	function __construct() {
		parent::__construct() ;
		$this->view = 'Smarty';
	}
	
	////////////////////////////////////////////////////////////////////////
	/**
	 * Preleva la lista completa dei moduli x un select e/o radio, etc...
	 *
	*/
	function getModuleList() {
		return $this->Module->generateList(null, 'label ASC', null, '{n}.Module.id', '{n}.Module.label') ;
	}
	
	/**
	 * Torna la lista dei moduli con un flag che indica l'accessibilitˆ da parte dell'utente
	 *
	 * @param integer $userID	utente abilitato
	 */
	function getListEnabledModules($userID = 0) {
		return $this->Module->generateListEnambledModules($userID) ;
	}
}

?>