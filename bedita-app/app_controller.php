<?php

App::import('Core', 'i18n');

class AppController extends Controller
{
	var $helpers 	= array("Javascript", "Html", "Form", "Beurl", "Tr", "Session", "Msg", "MediaProvider", "Perms", 'BeEmbedMedia', 'BeThumb');
	var $components = array('BeAuth', 'BeTree', 'BePermissionModule','Transaction', 'Cookie', 'Session');
	var $uses = array('EventLog') ;
	
	protected $moduleName = NULL;
	protected $modulePerms = NULL;
	protected $moduleColor = NULL;
	/**
	 * tipologie di esito operazione e esisto dell'operazione
	 *
	 */
	const OK 		= 'OK' ;
	const ERROR 	= 'ERROR' ;
	const VIEW_FWD = 'view://'; // 
	
	public $result 		= 'OK' ;
	protected $skipCheck = false;
	
	protected static $current = NULL;

	protected $selfUrlParams = NULL;

	protected $currLang = NULL; // selected UI lang - 
	
	/**
	 * Specific per-controller model bindings
	 *
	 * @var array
	 */
	protected $modelBindings = array();
	/////////////////////////////////		
	/////////////////////////////////		

	public static function handleExceptions(BeditaException $ex) {
		$errTrace =  $ex->errorTrace();   
		if(isset(self::$current)) {
			try {
				self::$current->handleError($ex->getDetails(), $ex->getMessage(), $errTrace);
				self::$current->setResult($ex->result);
				self::$current->beforeRender();
			} catch (Exception $e) { // error 500 if another exception is thrown here
				header('HTTP/1.1 500 Internal Server Error');
				// log error
				$errTrace = get_class($e). ": ". $e->getMessage()."\nFile: ".$e->getFile().
					" - line: ".$e->getLine()."\nTrace:\n". $e->getTraceAsString();
				self::$current->log($errTrace);
				App::import('View', "Smarty");
				$viewObj = new SmartyView(self::$current);
				return $viewObj->render(null, "error", VIEWS."errors/error500.tpl");				
			}
		} else {
			// TODO: what else??
			$obj = new AppController();
			$obj->log($errTrace);
		}
	}

	public static function defaultError(Exception $ex) {
		$r = new ReflectionObject($ex);
		$errTrace =  $r->getName()." -  ". $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
		if(isset(self::$current)) {
			self::$current->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			self::$current->setResult(self::ERROR);
			self::$current->beforeRender();
		} else {
			// TODO: what else??
			$obj = new AppController();
			$obj->log($errTrace);
		}
	}
	
	public function handleError($eventMsg, $userMsg, $errTrace) {
		$this->log($errTrace);
		// end transactions if necessary
		if(isset($this->Transaction)) {
			if($this->Transaction->started())
				$this->Transaction->rollback();
		}
		$this->eventError($eventMsg);
		$this->userErrorMessage($userMsg);
		/** 
		 * @todo : send mail in some cases.....
		 */
	}
	
	public function setResult($r) {
		$this->result=$r;
	}

	protected function initAttributes() {}	
	
	final function beforeFilter() {

		self::$current = $this;
		$this->view = 'Smarty';
		// convienience methods for frontends
		$this->initAttributes();
	 	$this->beditaBeforeFilter() ;

	 	// Exit on login/logout
	 	if(isset($this->data["login"]) || $this->name === 'Authentications') {
			return;
		}
		// Check login
		$this->BeAuth->startup($this) ;
		$this->set('conf',  Configure::getInstance());
		// check/setup localization
		$this->setupLocale();
		
		if(!$this->checkLogin($this->skipCheck)) 
			return;
	}

	protected function setupLocale() {
		// read Cookie
		$lang = $this->Cookie->read('bedita.lang');
		$conf = Configure::getInstance();
		if(isset($lang) && $conf->multilang === true) {
			$this->Session->write('Config.language', $lang);
		}
		I18n::getInstance();
		$this->currLang = $conf->Config['language'];
		if(!array_key_exists($this->currLang, $conf->langsSystem)) {
			if(isset( $conf->langsSystemMap[$this->currLang])) {
				$this->currLang = $conf->langsSystemMap[$this->currLang];
			} else { // use default
				$this->currLang = $conf->defaultLang;
			}
		}
		$this->set('currLang', $this->currLang);
		if(isset( $conf->datePatternLocale[$this->currLang])) {
			Configure::write('datePattern', $conf->datePatternLocale[$this->currLang]);
		}
		if(isset( $conf->dateTimePatternLocale[$this->currLang])) {
			Configure::write('dateTimePattern', $conf->dateTimePatternLocale[$this->currLang]);
		}

	}
	
	/**
	 * Gestisce il redirect in base all'esito di un metodo.
	 * Se c'e' nel form:
	 * 		$this->data['OK'] o $this->data['ERROR']
	 *  	seleziona.
	 * 
	 * Se nella classe è definito:
	 * 		$this->REDIRECT[<nome_metodo>]['OK'] o $this->REDIRECT[<nome_metodo>]['ERROR']
	 *  	seleziona.
	 * 
	 * Altrimenti non fa il redirect
	 * 
	 */
	final function beforeRender() {

		// setup return URL (if session expires)
		if(empty($this->selfUrlParams)) {
            $this->set('selfPlus', $this->createSelfURL(false)) ;
		} else {
            $this->set('selfPlus', $this->createSelfURL(false, $this->selfUrlParams));
		}
        $this->set('self',  ($this->createSelfURL(false)."?")) ;
		
		// convienience methods for frontends [like beforeRender]
        $this->beditaBeforeRender() ;
		
		if(isset($this->data[$this->result])) {
			$this->redirUrl($this->data[$this->result]);
		
		} elseif ($URL = $this->forward($this->action, $this->result)) {
			$this->redirUrl($URL);
		
		}
		
	}
	
	private function redirUrl($url) {
		if(strpos($url, self::VIEW_FWD) === 0) {
			$this->action=substr($url, strlen(self::VIEW_FWD));
		} else {
			$this->redirect($url);
		}
	}
	
	protected function forward($action, $outcome) {	return false ; }
	
	/**
	 *  local 'beforeFilter' (for backend or frontend)
	 */
	protected function beditaBeforeFilter() {
	}

    /**
     *  local 'beforeRender' (for backend or frontend)
     */
    protected function beditaBeforeRender() {
    }
	
    protected function eventLog($level, $msg) {
		$u = isset($this->BeAuth->user["userid"])? $this->BeAuth->user["userid"] : "-";
		$event = array('EventLog'=>array("level"=>$level, 
			"user"=>$u, "msg"=>$msg, "context"=>strtolower($this->name)));
		$this->EventLog->save($event);
	}
	
	/**
	 * User error message (will appear in messages div)
	 * @param string $msg
	 */
	protected function userErrorMessage($msg) {
		$this->Session->setFlash($msg, NULL, NULL, 'error');
	}

	/**
	 * User warning message (will appear in messages div)
	 * @param string $msg
	 */
	protected function userWarnMessage($msg) {
		$this->Session->setFlash($msg, NULL, NULL, 'warn');
	}

	/**
	 * User info message (will appear in messages div)
	 * @param string $msg
	 */
	protected function userInfoMessage($msg) {
		$this->Session->setFlash($msg, NULL, NULL, 'info');
	}
	
	/**
	 * Info message in system event logs
	 * @param string $msg
	 */
	protected function eventInfo($msg) {
		return $this->eventLog('info', $msg);
	}
	
	/**
	 * Warning message in system event logs
	 *
	 * @param string $msg
	 */
	protected function eventWarn($msg) {
		return $this->eventLog('warn', $msg);
	}

	/**
	 * Error message in system event logs
	 *
	 * @param string $msg
	 */
	protected function eventError($msg) {
		return $this->eventLog('err', $msg);
	}
	
	/**
	 * Verifica dell'accesso al modulo dell'utente.
	 * Deve essere inserito il componente: BeAuth.
	 * 
	 * Preleva l'elenco dei moduli visibili dall'utente corrente.
	 */
	protected function checkLogin() {
		static  $_loginRunning = false ;

		if($_loginRunning) return true ;
		else $_loginRunning = true ;

		// skip authorization check, if specific controller set skipCheck = true
		if($this->skipCheck) return true;

		// Verify authorization
		if(!$this->BeAuth->isLogged()) { 
			$this->render(null, null, VIEWS."home".DS."login.tpl") ; 
			$_loginRunning = false; 
			exit;
		}
		
		// module list
		$moduleList = $this->BePermissionModule->getListModules($this->BeAuth->user["userid"]);
		$this->set('moduleList', $moduleList) ;			
		
		// verify basic access
		if(isset($this->moduleName)) { 
			foreach ($moduleList as $mod) {
			 	if($this->moduleName == $mod['label']) { 
			 		$this->modulePerms = $mod['flag'];
			 		$this->moduleColor = $mod['color'];
				}
			}
			$this->set("module_modify",(isset($this->moduleName) && ($this->modulePerms & BEDITA_PERMS_MODIFY)) ? "1" : "0");
			if(!isset($this->modulePerms) || !($this->modulePerms & BEDITA_PERMS_READ)) {
					$logMsg = "Module [". $this->moduleName.  "] access not authorized";
					$this->log($logMsg);
					$this->handleError($logMsg, __("Module access not authorized",true));
					$this->redirect("/");
			}
			$this->set('moduleColor',$this->moduleColor);
			$this->set('moduleName', $this->moduleName);
		}
		
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
	protected function setup_args() {
		$size = func_num_args() ;
		$args = func_get_args() ;
		
		for($i=0; $i < $size ; $i++) {
			// Se il parametro e' in params o in pass, lo preleva e lo inserisce
			if(isset($this->params["url"][$args[$i][0]]) && !empty($this->params["url"][$args[$i][0]])) {
				$args[$i][2] = $this->params["url"][$args[$i][0]] ;
				$this->passedArgs[$args[$i][0]] = $this->params["url"][$args[$i][0]] ;
				
			} elseif(isset($this->params["named"][$args[$i][0]]) && !empty($this->params["named"][$args[$i][0]])) {
				$args[$i][2] = $this->params["named"][$args[$i][0]] ;
				$this->passedArgs[$args[$i][0]] = $this->params["named"][$args[$i][0]] ;

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
	protected function createSelfURL($cake = true) {
		if(!isset($this->params["controller"]) || !isset($this->params["action"]))
			throw new BeditaException("Configuration error", "cake params controller/action missing!!");
		
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
	
	protected function loadModelByObjectTypeId($obj_type_id) {
		$conf  = Configure::getInstance();
		$modelClass = $conf->objectTypeModels[$obj_type_id];
		return $this->loadModelByType($modelClass);
	}

	protected function loadModelByType($modelClass) {
		if(!class_exists($modelClass)){
			App::import('Model',$modelClass);
		}
		if (!class_exists($modelClass)) {
			throw new BeditaException(__("Object type not found - ", true).$modelClass);			
		}
		return new $modelClass();
	}
	
	public function modelBindings(Model $modelObj) {
		$conf = Configure::getInstance();
		$name = $modelObj->name;
		if(isset ($this->modelBindings[$name])) {
			$modelObj->restrict($this->modelBindings[$name]);
		} else if(isset ($conf->modelBindings[$name])) {
			$modelObj->restrict($conf->modelBindings[$name]);
		}
	}	
		
	/**
	 * Reorder content objects relations in array where keys are relation names
	 *
	 * @param array $objectArray
	 * @return array
	 */
	protected function objectRelationArray($objectArray,$langTextExtended=false) {
		$conf  = Configure::getInstance() ;
		$relationArray = array();
		foreach ($objectArray as $obj) {
			$rel = $obj['ContentBasesObject']['switch'];
			$modelClass = $conf->objectTypeModels[$obj['object_type_id']] ;
			if(!class_exists($modelClass)){
				App::import('Model',$modelClass);
			}
			if (!class_exists($modelClass)) {
				throw new BeditaException(__("Object type not found - ", true).$modelClass);			
			}
			$this->{$modelClass} = new $modelClass();
			$this->modelBindings($this->{$modelClass});

			if(!($objDetail = $this->{$modelClass}->findById($obj['id']))) {
				continue ;
			}
			$objDetail['priority'] = $obj['ContentBasesObject']['priority'];
			
			if(isset($objDetail['path']))
				$objDetail['filename'] = substr($objDetail['path'],strripos($objDetail['path'],"/")+1);

			$relationArray[$rel][] = $objDetail;
		}
		
		return $relationArray;
	}
}

/**
 * Base class for modules
 *
 */
abstract class ModulesController extends AppController {

	protected function checkWriteModulePermission() {
		if(isset($this->moduleName) && !($this->modulePerms & BEDITA_PERMS_MODIFY)) {
				throw new BeditaException(__("No write permissions in module", true));
		}
	}
	
	/**
	 * Method for paginated objects, used in ModuleController::index()...
	 *
	 * @param unknown_type $id
	 * @param unknown_type $typesArray
	 * @param unknown_type $order
	 * @param unknown_type $dir
	 * @param unknown_type $page
	 * @param unknown_type $dim
	 */
	protected function paginatedList($id, $typesArray, $order, $dir, $page, $dim) {
		$this->setup_args(
			array("id", "integer", &$id),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;

		// get section selected
		$section = $this->loadModelByType("section");
		$this->modelBindings['Section'] = array("BEObject");
		$this->modelBindings($section);
		$sectionSel = $section->findById($id);
		unset($this->modelBindings['Section']);
		
		$objects = $this->BeTree->getDiscendents($id, null, $typesArray, $order, $dir, $page, $dim)  ;
		$this->params['toolbar'] = &$objects['toolbar'] ;
		
		// template data
		$this->set('tree', $this->BeTree->getSectionsTree());
		$this->set('sectionSel',$sectionSel);
		$this->set('objects', $objects['items']);
	}
	
	/**
	 * Delete objects
	 *
	 * @param model name
	 * @return string of objects'ids deleted
	 */
	protected function deleteObjects($model) {
		$objectsToDel = array();
		$objectsListDesc = "";
		
		if(!empty($this->params['form']['objects_selected'])) {
			
			$objectsToDel = $this->params['form']['objects_selected'];
			$objectsListDesc = implode(",", $objectsToDel);
			
		} else {
			if(empty($this->data['id'])) 
				throw new BeditaException(__("No data", true));
			if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
				throw new BeditaException(__("Error delete permissions", true));
			}
			$objectsToDel = array($this->data['id']);
			$objectsListDesc = $this->data['id'];
		}

		$this->Transaction->begin() ;

		foreach ($objectsToDel as $id) {
			if(!$this->Permission->verify($id, $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
				throw new BeditaException(__("Error delete permissions", true));
			}
			if(!$this->{$model}->delete($id)) {
				throw new BeditaException(__("Error deleting object: ", true) . $id);
			}
		}
		
		$this->Transaction->commit() ;
		return $objectsListDesc;
	}

	protected function deleteMultimediaObjects() {
		$objectsToDel = array();
		$objectsListDesc = "";
		if(!empty($this->params['form']['objects_selected'])) {
			
			$objectsToDel = $this->params['form']['objects_selected'];
			$objectsListDesc = implode(",", $objectsToDel);
			
		} else {
			if(empty($this->data['id'])) 
				throw new BeditaException(__("No data", true));
			if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
				throw new BeditaException(__("Error delete permissions", true));
			}
			$objectsToDel = array($this->data['id']);
			$objectsListDesc = $this->data['id'];
		}

		$this->Transaction->begin() ;

		foreach ($objectsToDel as $id) {
			if(!$this->BeFileHandler->del($id))
				throw new BeditaException(__("Error deleting object: ", true) . $id);
		}
		
		$this->Transaction->commit() ;
		return $objectsListDesc;
	}

	public function changeStatusObjects() {
		$objectsToModify = array();
		$objectsListDesc = "";
		if(!empty($this->params['form']['objects_selected'])) {
			$objectsToModify = $this->params['form']['objects_selected'];
			$objectsListDesc = implode(",", $objectsToModify);
		
			$this->Transaction->begin() ;
			foreach ($objectsToModify as $id) {
				$this->BEObject->id = $id;
				if(!$this->BEObject->saveField('status',$this->params['form']["newStatus"]))
					throw new BeditaException(__("Error saving status for object: ", true) . $id);
			}
			
			$this->Transaction->commit() ;
		}
		return $objectsListDesc;
	}
	
	protected function setUsersAndGroups() {
		if(!class_exists('User')) {
			App::import('Model', 'User') ;
		}
		if(!class_exists('Group')) {
			App::import('Model', 'Group') ;
		}
		$this->User = new User();
		$this->Group = new Group();
		// get users and groups list. 
		$this->User->displayField = 'userid';
		$this->set("usersList", $this->User->find('list', array("order" => "userid")));
		$this->set("groupsList", $this->Group->find('list', array("order" => "name")));
	}

	 /**
	  * Add Link with Ajax...
	  */
	
	public function addLink() {
		$this->layout="empty";
	 	$this->data = $this->params['form'];
		$this->Transaction->begin() ;
		$linkModel = $this->loadModelByType("Link");
		if(!$linkModel->save($this->data)) {
	 		throw new BeditaException(__("Error saving link", true), $linkModel->validationErrors);
	 	}
 		$this->Transaction->commit() ;
		$this->eventInfo("link [". $this->data["title"]."] saved");
		$this->data["id"] = $linkModel->id;
		$this->set("objRelated", $this->data);
	 }
	 	
	public function addItemsToAreaSection() {
		$this->checkWriteModulePermission();
		
		if(!empty($this->params['form']['objects_selected'])) {
			$objects_to_assoc = $this->params['form']['objects_selected'];
			$destination = $this->data['destination'];
		
			$object_type_id = $this->BEObject->findObjectTypeId($destination);
			$modelLoaded = $this->loadModelByObjectTypeId($object_type_id);
			$modelLoaded->restrict("BEObject");
			if(!($section = $modelLoaded->findById($destination))) {
				throw new BeditaException(sprintf(__("Error loading section: %d", true), $destination));
			}
			$this->Transaction->begin() ;
			for($i=0; $i < count($objects_to_assoc) ; $i++) {
				$parents = $this->BeTree->getParents($objects_to_assoc[$i]);
				if (!in_array($section['id'], $parents)) { 
					if(!$modelLoaded->appendChild($objects_to_assoc[$i],$section['id'])) {
						throw new BeditaException( __("Error during append child", true));
					}
				}
			}
			$this->Transaction->commit() ;
			$this->userInfoMessage(__("Items associated to area/section", true) . " - " . $section['title']);
			$this->eventInfo("items associated to area/section " . $section['id']);
		}
	}

	/**
	 * Return preview links for $obj_id in $sections
	 * 
	 * @return array of previews - single preview is like array('url'=>'...','desc'=>'...')
	 * @param $sections array of section id
	 * @param $obj_id object id
	 */
	public function previewsForObject($sections,$obj_id,$status) {
		$previews = array();
		if(empty($obj_id) || empty($sections))
			return $previews;
		foreach($sections as $section_id) {
			$a = $this->BeTree->getAreaForSection($section_id);
			if(!empty($a)) {
				$desc = $this->BEObject->field('title',array("id=$section_id"));
				$field = ($status=='on') ? 'public_url' : 'staging_url';
				if(!empty($a[$field])) {
					$previews[]=array(
						'url'=>$a[$field]."/section/$section_id/$obj_id",
						'desc'=>$desc);
				}
			}
		}
		return $previews;
	}
	
	public function cloneObject() {
		unset($this->data['id']);
		$this->data['status']='draft';
		$this->save();
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Bedita basic exception
 */
class BeditaException extends Exception
{
	public $result;
	protected $errorDetails; // details for log file
	
	public function __construct($message = NULL, $details = NULL, $res  = AppController::ERROR, $code = 0) {
   		if(empty($message)) {
   			$message = __("Unexpected error, operation failed",true);
   		}
   		$this->errorDetails = $message;
   		if(!empty($details)) {
   			if(is_array($details)) {
   				foreach ($details as $k => $v) {
					$this->errorDetails .= "; [$k] => $v";
				}
   			} else {
   				$this->errorDetails = $this->errorDetails . ": ".$details; 
   			}
   		}
   		$this->result = $res;
        parent::__construct($message, $code);
    }
    
    public function  getDetails() {
    	return $this->errorDetails;
    }
    
    public function errorTrace() {
        return get_class($this)." - ".$this->getDetails()."\nFile: ". 
            $this->getFile()." - line: ".$this->getLine()."\nTrace:\n".
            $this->getTraceAsString();   
    }
}

?>
