<?php

App::import('Core', 'l10n');

class AppController extends Controller
{
	var $helpers 	= array("Javascript", "Html", "Form", "Beurl", "Tr", "Session", "Msg", "MediaProvider", "Perms", 'BeEmbedMedia', 'BeThumb');
	var $components = array('BeAuth', 'BeTree', 'BePermissionModule', 'BeCustomProperty', 'Permission', 'Transaction', 'Cookie', 'Session');
	var $uses = array('EventLog') ;
	
	protected $moduleName = NULL;
	protected $modulePerms = NULL;
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

	protected $currLang = NULL; // selected UI lang - 
	
	/**
	 * Specific per-controller model bindings
	 *
	 * @var array
	 */
	protected $modelBindings = array();
	/////////////////////////////////		
	/////////////////////////////////		
	public static function currentController() {
		return self::$current;
	}
	
	public static function handleExceptions(BeditaException $ex) {
		include_once (APP . 'app_error.php');
		return new AppError('handleException', array('details' => $ex->getDetails(), 'msg' => $ex->getMessage(), 
				'result' => $ex->result), $ex->errorTrace());
	}

	public static function defaultError(Exception $ex) {

		$errTrace =  get_class($ex)." -  ". $ex->getMessage()."\nFile: ".$ex->getFile().
					" - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
		include_once (APP . 'app_error.php');
		return new AppError('handleException', array('details' => $ex->getMessage(), 
				'msg' => $ex->getMessage(), 
				'result' => self::ERROR), $errTrace);
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
		$l10n = new L10n();
		$l10n->get();		
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
			echo $this->render(null, null, VIEWS."home".DS."login.tpl") ; 
			$_loginRunning = false; 
			exit;
		}
		
		// module list
		$moduleList = $this->BePermissionModule->getListModules($this->BeAuth->user["userid"]);
		$this->set('moduleList', $moduleList) ;			
		$this->set('moduleListInv', array_reverse($moduleList)) ;	
		
		// verify basic access
		if(isset($this->moduleName)) { 
			foreach ($moduleList as $mod) {
			 	if($this->moduleName == $mod['name']) { 
			 		$this->modulePerms = $mod['flag'];
				}
			}
			$this->set("module_modify",(isset($this->moduleName) && ($this->modulePerms & BEDITA_PERMS_MODIFY)) ? "1" : "0");
			if(!isset($this->modulePerms) || !($this->modulePerms & BEDITA_PERMS_READ)) {
					$logMsg = "Module [". $this->moduleName.  "] access not authorized";
					$this->handleError($logMsg, __("Module access not authorized",true), $logMsg);
					$this->redirect("/");
			}
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
	
	
	protected function loadModelByObjectTypeId($obj_type_id) {
		$conf  = Configure::getInstance();
		$modelClass = $conf->objectTypes[$obj_type_id]["model"];
		return $this->loadModelByType($modelClass);
	}

	protected function loadModelByType($modelClass) {
		$model = ClassRegistry::init($modelClass);
		if($model === false) {
			throw new BeditaException(__("Object type not found - ", true).$modelClass);			
		}
		return $model;
	}
	
	public function modelBindings(Model $modelObj, $level = 'default') {
		$conf = Configure::getInstance();
		$name = $modelObj->name;
		if(isset ($this->modelBindings[$name])) {
			$modelObj->contain($this->modelBindings[$name]);
		} else if(isset ($conf->modelBindings[$name])) {
			$modelObj->contain($conf->modelBindings[$name]);
		} else {
			$modelObj->containLevel($level);
		}
	}	
		
	/**
	 * Reorder content objects relations in array where keys are relation names
	 *
	 * @param array $objectArray
	 * @param array $status, default get all objects
	 * @return array
	 */
	protected function objectRelationArray($objectArray, $status=array()) {
		$conf  = Configure::getInstance() ;
		$relationArray = array();
		
		$beObject = ClassRegistry::init("BEObject");
		foreach ($objectArray as $obj) {	
			$rel = $obj['switch'];
			$modelClass = $beObject->getType($obj['object_id']);
			$this->{$modelClass} = $this->loadModelByType($modelClass);
			$this->modelBindings($this->{$modelClass});
	
			if(!($objDetail = $this->{$modelClass}->findById($obj['object_id']))) {
				continue ;
			}
            if (empty($status) || in_array($objDetail["status"],$status)) {
				$objDetail['priority'] = $obj['priority'];
				if(isset($objDetail['path']))
					$objDetail['filename'] = substr($objDetail['path'],strripos($objDetail['path'],"/")+1);
	
				$relationArray[$rel][] = $objDetail;
			}	
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
		
		$objects = $this->BeTree->getChildren($id, null, $typesArray, $order, $dir, $page, $dim)  ;
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
		} else {
			if(empty($this->data['id'])) 
				throw new BeditaException(__("No data", true));
			if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
				throw new BeditaException(__("Error delete permissions", true));
			}
			$objectsToDel = array($this->data['id']);
		}

		$this->Transaction->begin() ;

		$beObject = ClassRegistry::init("BEObject");
		
		foreach ($objectsToDel as $id) {
			if(!$this->Permission->verify($id, $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
				throw new BeditaException(__("Error delete permissions", true));
			}
			
			$fixed = $beObject->field("fixed", array("id" => $id));
			
			if ( $fixed == 0 ) {
				if ($model != "Stream") {
					if(!$this->{$model}->delete($id))
						throw new BeditaException(__("Error deleting object: ", true) . $id);
				} else {
					if(!$this->BeFileHandler->del($id))
						throw new BeditaException(__("Error deleting object: ", true) . $id);
				}
				$objectsListDesc .= $id . ",";
			}
		}
		
		if (empty($objectsListDesc))
			throw new BeditaException(__("No object deleted, maybe you're trying to delete fixed object", true));
		
		$this->Transaction->commit() ;
		return trim($objectsListDesc, ",");
	}

	public function changeStatusObjects($modelName=null) {
		$objectsToModify = array();
		$objectsListDesc = "";
		if(!empty($this->params['form']['objects_selected'])) {
			$objectsToModify = $this->params['form']['objects_selected'];
		
			$this->Transaction->begin() ;

			if (empty($modelName))
				$modelName = "BEObject";
			
			foreach ($objectsToModify as $id) {
				$model = $this->loadModelByType($modelName);
				
				$fixed = ( ($modelName == "BEObject")? $model->field("fixed", array("id" => $id)) : $model->BEObject->field("fixed", array("id" => $id)) );
				if ( $fixed == 0 ) {
					$model->id = $id;
					if(!$model->saveField('status',$this->params['form']["newStatus"]))
						throw new BeditaException(__("Error saving status for item: ", true) . $id);
				}
				$objectsListDesc .= $id . ",";
			}
			
			$this->Transaction->commit() ;
		}
		return trim($objectsListDesc, ",");
	}

	 /**
	  * Add Link with Ajax...
	  */
	
	public function addLink() {
		$this->layout="empty";
	 	$this->data = $this->params['form'];
		$this->data["status"] = "on";
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
			$modelLoaded->contain("BEObject");
			if(!($section = $modelLoaded->findById($destination))) {
				throw new BeditaException(sprintf(__("Error loading section: %d", true), $destination));
			}
			$this->Transaction->begin() ;
			for($i=0; $i < count($objects_to_assoc) ; $i++) {
				
				$fixed = $this->BEObject->field("fixed", array("id" => $objects_to_assoc[$i]));
				
				if ($fixed == 0) {
					$parents = $this->BeTree->getParents($objects_to_assoc[$i]);
					if (!in_array($section['id'], $parents)) { 
						if(!$modelLoaded->appendChild($objects_to_assoc[$i],$section['id'])) {
							throw new BeditaException( __("Error during append child", true));
						}
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
		$this->data['fixed'] = 0;
		$this->save();
	}

	protected function viewObject(BEAppModel $beModel, $id = null) {
		Configure::load('langs.iso') ;
		$obj = null ;
		$parents_id = array();
		$relations = array();
		$previews = array();
		$name = strtolower($beModel->name);
		if(isset($id)) {
			$beModel->containLevel("detailed");
			if(!($obj = $beModel->findById($id))) {
				throw new BeditaException(__("Error loading $name: ", true).$id);
			}
			if(!$beModel->checkType($obj['object_type_id'])) {
               throw new BeditaException(__("Wrong content type: ", true).$id);
			}
			$relations = array();
			if (!empty($obj['RelatedObject'])) {
				$relations = $this->objectRelationArray($obj['RelatedObject']);
			}
			// build array of id's categories associated
			$obj["assocCategory"] = array();
			if (isset($obj["Category"])) {
				$objCat = array();
				foreach ($obj["Category"] as $oc) {
					$objCat[] = $oc["id"];
				}
				$obj["assocCategory"] = $objCat;
			}			
			$treeModel = ClassRegistry::init("Tree");
			$parents_id = $treeModel->getParent($id) ;
			if($parents_id === false) 
				$parents_id = array() ;
			elseif(!is_array($parents_id))
				$parents_id = array($parents_id);
		
			$previews = $this->previewsForObject($parents_id, $id, $obj['status']);
		}

		$this->set('object',	$obj);
		$this->set('attach', isset($relations['attach']) ? $relations['attach'] : array());
		$this->set('relObjects', $relations);

		$this->set('parents',	$parents_id);
		$this->set('tree', 		$this->BeTree->getSectionsTree());
		$this->set('previews',	$previews);
		
		$categoryModel = ClassRegistry::init("Category");
		$areaCategory = $categoryModel->getCategoriesByArea(Configure::read('objectTypes.'.$name.'.id'));
		$this->set("areaCategory", $areaCategory);
	}

	protected function saveObject(BEAppModel $beModel) {

		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// Verify object permits
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
			throw new BeditaException(__("Error modify permissions", true));
		// Format custom properties
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
		
		$name = strtolower($beModel->name);
		
		$categoryModel = ClassRegistry::init("Category");
		$tagList = array();
		if (!empty($this->params["form"]["tags"]))
			$tagList = $categoryModel->saveTagList($this->params["form"]["tags"]);
		$this->data["Category"] = (!empty($this->data["Category"]))? array_merge($this->data["Category"], $tagList) : $tagList;
		
		if(!$beModel->save($this->data)) {
			throw new BeditaException(__("Error saving $name", true), $beModel->validationErrors);
		}
		if( empty($this->data['fixed']) ) {
			if(!isset($this->data['destination'])) 
				$this->data['destination'] = array() ;
			$this->BeTree->updateTree($beModel->id, $this->data['destination']);
		}
	 	// update permissions
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
		$this->Permission->saveFromPOST($beModel->id, $this->data['Permissions'], 
	 			!empty($this->data['recursiveApplyPermissions']), $name);
	}

	protected function showCategories(BEAppModel $beModel) {
		$conf  = Configure::getInstance() ;
		$type = $conf->objectTypes[strtolower($beModel->name)]["id"];
		$categoryModel = ClassRegistry::init("Category");
		$this->set("categories", $categoryModel->findAll("Category.object_type_id=".$type));
		$this->set("object_type_id", $type);
		$this->set("areasList", $this->BEObject->find('list', array(
										"conditions" => "object_type_id=" . Configure::read("objectTypes.area.id"), 
										"order" => "title", 
										"fields" => "BEObject.title"
										)
									)
								);	
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
        return get_class($this)." - ".$this->getDetails()." \nFile: ". 
            $this->getFile()." - line: ".$this->getLine()." \nTrace:\n".
            $this->getTraceAsString();   
    }
}

?>
