<?php
/* SVN FILE: $Id: app_controller.php 2951 2006-05-25 22:12:33Z phpnut $ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package			cake
 * @subpackage		cake.cake
 * @since			CakePHP v 0.2.9
 * @version			$Revision: 2951 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006-05-25 17:12:33 -0500 (Thu, 25 May 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake
 */
class AppController extends Controller {
	var $components = array('BeAuth');
	var $helpers 	= array('Bevalidation');
	
	 /**
	  * 
	  */
	 function beforeFilter() {
		$this->view 	= 'Smarty';
	 	$this->setupCommonData() ;
	 	
		if(isset($this->data["login"])) return  ;
	 	$this->BeAuth->startup($this) ;
			
	 	if(!$this->checkLogin()) return ;
	 	
	 }
	  
	/**
	 * Setta i dati utilizzabili nelle diverse pagine.
	 *
	 * @todo VERIFICA SE INSERIRE I DIVERSI DATI IN UN FILE DI CONFIGURAZIONE
	 */
	function setupCommonData() {
		// Possibili stati. versione  minimale
		$this->set('listSimpleStatues', array("on" =>"on", "off" => "off")) ;
		
		// Lista delle diverse lingue applicabili
		$this->set('listLangs', 
			array(
				"it"	=> "Italiano",
				"en"	=> "English",
				"fr"	=> "French",
				"es"	=> "Spanish",
				"de"	=> "German"
			)
		) ;
	}
	
	/**
	 * Verifica dell'accesso al modulo dell'utente.
	 * Deve essere inserito il componente: BeAuth.
	 * 
	 * Preleva l'elenco dei moduli visibili dall'utente corrente.
	 */
	function checkLogin() {				
		static  $_test = 1 ;
		static  $_loginRunning = false ;
 		
		if($_loginRunning) return true ;
 		else $_loginRunning  = true ;
		 
		// Verifica i permessi d'accesso
		if(!$this->BeAuth->isLogged()) { $this->render(null, null, VIEWS."pages/anonymous.tpl") ; $_loginRunning = false; exit; }
		
		// Preleva lista dei moduli
        $this->set('moduleList', $this->requestAction('/modules/getListEnabledModules/'.$this->BeAuth->user["id"]));
	
		$_loginRunning = false ;
		
        return true ;
	}
	
	/**
	 * Esegue il setup delle variabili passate ad una funzione del controller.
	 * Se la viarbile e' nul, usa il valore in $this->params[url] che contiene i
	 * valori da _GET.
	 * Parametri:
	 * 0..N array con i seguenti valori:
	 * 		0	nome della variabile
	 * 		1	stringa con il tipo da assumere (per la funzione settype)
	 * 		2	reference alla variabile da modificare
	 *
	 */
	function setup_args() {
		$size = func_num_args() ;
		$args = func_get_args() ;
		
		for($i=0; $i < $size ; $i++) {
			// Se il parametro e' in params, lo preleva e lo inserisce
			if(isset($this->params["url"][$args[$i][0]]) && !empty($this->params["url"][$args[$i][0]])) {
				$args[$i][2] = $this->params["url"][$args[$i][0]] ;
			}
			
			// Se il valore non e' nullo, ne definisce il tipo
			if(!is_null($args[$i][2])) {
				settype($args[$i][2], $args[$i][1]) ;
				$this->params["url"][$args[$i][0]] = $args[$i][2] ;
			}
		}
		
	}
	
	/**
	 * Crea l'URL per il ritorno allo stesso stato.
	 * Nelle 2 forme possibili:
	 * cake		controller/action[/params_1[/...]]
	 * get		controller/action[?param_1_name=parm_1_value[&amp;...]]
	 * 
	 * @param $cake true, torna nel formato cake; false: get 
	 * altri Parametri:
	 * 0..N array con i seguenti valori:
	 * 		0	nome della variabile
	 * 		2	valore
	 *
	 */
	function createSelfURL($cake = true) {
		$baseURL = $this->webroot . $this->params["controller"] ."/". $this->params["action"] ;
		$size  = func_num_args() ;
		$args  = func_get_args() ;
		
		if($size < 2) return $baseURL ;
		
		
		for($i=1; $i < $size ; $i++) {
			if($cake) {
				$baseURL .= "/" . urlencode($args[$i][1]) ;
				continue ;
			}
			
			if($i == 1) {
				$baseURL .= "?" .  urlencode($args[$i][0]) ."=" . urlencode($args[$i][1]) ;
			}  else {
				$baseURL .= "&amp;" .  urlencode($args[$i][0]) ."=" . urlencode($args[$i][1]) ;
			}
		}
	
		return $baseURL ;
	}

}

?>