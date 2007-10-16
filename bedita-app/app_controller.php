<?php

uses('L10n');

class AppController extends Controller
{
	var $helpers 	= array("Javascript", "Html", "Bevalidation", "Form", "Tr");
	var $components = array('BeAuth', 'BePermissionModule');
	
	/**
	 * tipologie di esito operazione e esisto dell'operazione
	 *
	 */
	static $OK 		= 'OK' ;
	static $ERROR 	= 'ERROR' ;
	
	var $esito 		= 'OK' ;
	
	/////////////////////////////////		
	/////////////////////////////////		
	
	function beforeFilter() {
		// Templater
		$this->view = 'Smarty';
		
		// preleva, per il template, i dati di configurazione
	 	$this->setupCommonData() ;
	 	
	 	// Non genera automaticamente l'output, per far eseguire prima afterFilter
	 	$this->autoRender = false ;
	 	
	 	// Se viene richiesto il login esce
		if(isset($this->data["login"])) return  ;
		
		// Esegue la verifca di login
		$this->BeAuth->startup($this) ;	
	 	if(!$this->checkLogin()) return ;
		
	}
	
	/**
	 * Gestisce il redirect in base all'esito di un metodo.
	 * Se c'e' nel form:
	 * 		$this->data['OK'] o $this->data['ERROR']
	 *  	seleziona.
	 * 
	 * Se nella classe � definito:
	 * 		$this->REDIRECT[<nome_metodo>]['OK'] o $this->REDIRECT[<nome_metodo>]['ERROR']
	 *  	seleziona.
	 * 
	 * Altrimenti non fa il redirect
	 * 
	 */
	function afterFilter() {
		if($this->autoRender) return ;
				
		if(isset($this->data[$this->esito])) {
			$this->redirect($this->data[$this->esito]);
		
		} elseif ($URL = $this->_REDIRECT($this->action, $this->esito)) {
			$this->redirect($URL);
		
		} else {
			$this->output = $this->render($this->action);
		}
	}
	
	function _REDIRECT($action, $esito) {	return false ; }
	
	/**
	 * Setta i dati utilizzabili nelle diverse pagine.
	 * 
	 * @todo DATI SPECIFICI AREA SELEZIONATA
	 */
	function setupCommonData() {
		// E' necessario?
		$conf  		= Configure::getInstance() ;
		
		$this->pageTitle = __($this->name, true);
		
		$this->set('conf', $conf) ;
	}
	
	/**
	 * Verifica dell'accesso al modulo dell'utente.
	 * Deve essere inserito il componente: BeAuth.
	 * 
	 * Preleva l'elenco dei moduli visibili dall'utente corrente.
	 */
	function checkLogin() {				
		static  $_loginRunning = false ;
 		
		if($_loginRunning) return true ;
 		else $_loginRunning  = true ;
		 
		// Verifica i permessi d'accesso
		if(!$this->BeAuth->isLogged()) { $this->render(null, null, VIEWS."pages/login.tpl") ; $_loginRunning = false; exit; }
		
//		// Preleva lista dei moduli
		$this->set('moduleList', $this->BePermissionModule->getListModules($this->BeAuth->user["userid"])) ;			
		
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
			// Se il parametro e' in params o in pass, lo preleva e lo inserisce
			if(isset($this->params["url"][$args[$i][0]]) && !empty($this->params["url"][$args[$i][0]])) {
				$args[$i][2] = $this->params["url"][$args[$i][0]] ;
				
				$this->passedArgs[$args[$i][0]] = $this->params["url"][$args[$i][0]] ;
				
			} elseif(isset($this->passedArgs[$args[$i][0]])) {
				$args[$i][2] = $this->passedArgs[$args[$i][0]] ;
			}
			
			// Se il valore non e' nullo, ne definisce il tipo e lo inserisce in namedArgs
			if(!is_null($args[$i][2])) {
				settype($args[$i][2], $args[$i][1]) ;
				$this->params["url"][$args[$i][0]] = $args[$i][2] ;
				
				$this->namedArgs[$args[$i][0]] = $args[$i][2] ;
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
		$baseURL = "/" . $this->params["controller"] ."/". $this->params["action"] ;
		
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


////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * 
 * Eccezione sollevata dai controller per indicare l'esito negativo di un'action.
 * 
 */
class BEditaActionException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($controller, $message, $code  = 0) {
        // some code
   		$controller->esito = AppController::$ERROR ;
        
        // make sure everything is assigned properly
        parent::__construct($message, $code);
    }
}



?>
