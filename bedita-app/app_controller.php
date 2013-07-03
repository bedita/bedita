<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2012 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

App::import('Core', 'l10n');
App::import('Lib', 'BeLib');
BeLib::getObject("BeConfigure")->initConfig();

/**
 * Controller base class for backends+frontends
 *
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class AppController extends Controller
{
	var $helpers 	= array("Javascript", "Html", "Form", "Beurl", "Tr", "Session", "MediaProvider", "Perms", 'BeEmbedMedia');
	var $components = array('BeAuth', 'BeTree', 'BeCustomProperty', 'Transaction', 'Cookie', 'Session', 'RequestHandler','BeHash');
	var $uses = array('EventLog') ;

	protected $moduleName = NULL;
	protected $moduleList = NULL;
	protected $modulePerms = NULL;
	/**
	 * Result types for methods
	 */
	const OK 		= 'OK' ;
	const ERROR 	= 'ERROR' ;
	const VIEW_FWD = 'view://'; //

	public $result 		= 'OK' ;
	protected $skipCheck = false;

	protected static $current = NULL;

	protected $currLang = NULL; // selected UI lang
	protected $currLocale = NULL; // selected UI locale

	/**
	 * Specific per-controller model bindings
	 *
	 * @var array
	 */
	protected $modelBindings = array();

	/**
	 * Full url prefix
	 *
	 * @var string
	 */
	protected $fullBaseUrl = "";

	/**
	 * fields to save in history table
	 *
	 * @var array, set to null to avoid history insert also with history configure var setted
	 */
	protected $historyItem = array();

	public static function currentController() {
		return self::$current;
	}

	public static function handleExceptions(BeditaException $ex) {
		include_once (APP . 'app_error.php');
		if ($ex instanceof BeditaAjaxException) {
			return new AppError("handleAjaxException", array('details' => $ex->getDetails(), 'msg' => $ex->getMessage(),
				'result' => $ex->result, 'output' => $ex->getOutputType(),'headers' => $ex->getHeaders()), $ex->errorTrace());
		} elseif (self::currentController()->RequestHandler->isAjax()) {
			return new AppError("handleAjaxException", array('details' => $ex->getDetails(), 'msg' => $ex->getMessage(),
				'result' => $ex->result, 'output' => 'beditaMsg'), $ex->errorTrace());
		}
		return new AppError("handleException", array('details' => $ex->getDetails(), 'msg' => $ex->getMessage(),
				'result' => $ex->result), $ex->errorTrace());
	}

	public static function defaultError(Exception $ex) {
		include_once (APP . 'app_error.php');
		$errTrace =  get_class($ex)." -  ". $ex->getMessage()."\nFile: ".$ex->getFile().
					" - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();
		$messages = array('details' => $ex->getMessage(), 'msg' => $ex->getMessage(), 'result' => self::ERROR);
		$handleMethod = ($ex instanceof SmartyException)? 'handleSmartyException' : 'handleException';
		return new AppError($handleMethod, $messages, $errTrace);
	}

	public function handleError($eventMsg, $userMsg, $errTrace, $usrMsgParams=array()) {
		$this->log($errTrace);
		// end transactions if necessary
		if(isset($this->Transaction)) {
			if($this->Transaction->started())
				$this->Transaction->rollback();
		}
		$this->eventError($eventMsg);
		$layout = (!isset($usrMsgParams["layout"]))? "message" : $usrMsgParams["layout"];
		$params = (!isset($usrMsgParams["params"]))? array("class" => "error") : $usrMsgParams["params"];
		$params['detail'] = $eventMsg;
		$this->userErrorMessage($userMsg, $layout, $params);
	}

	public function setResult($r) {
		$this->result=$r;
	}

	protected function initAttributes() {}

	/**
	 *  convienience method to do operations before login check
	 *	for example you could set AppController::skipCheck to true avoiding user session check
	 */
	protected function beforeCheckLogin() {}
	
	
	final function beforeFilter() {
		self::$current = $this;
		$this->view = 'Smarty';
		$conf = Configure::getInstance();
		$this->set('conf',  $conf);

		// check/setup localization
		$this->setupLocale();

		// only backend
		if (BACKEND_APP) {
			if(isset($this->data["login"]) || $this->name === 'Authentications') {
			    return;
		    }
		    // load publications public url
			$publications = ClassRegistry::init("Area")->find("all", array(
				"contain" => array("BEObject")
			));
			$this->set("publications", $publications);
		}

		$this->beforeCheckLogin();
		$this->checkLogin();
		// convienience methods for frontends or backend to init attibutes before any other operations
		$this->initAttributes();
		$this->beditaBeforeFilter();
	}

	protected function setupLocale() {

		$this->currLang = $this->Session->read('Config.language');
		$conf = Configure::getInstance();
		if($this->currLang === null || empty($this->currLang)) {
			// read Cookie
			$lang = $this->Cookie->read('bedita.lang');
			if(isset($lang)) {
				$this->Session->write('Config.language', $lang);
				$this->currLang = $lang;
			} else {
				// HTTP autodetect
				$l10n = new L10n();
				$l10n->get();
				$this->currLang = $l10n->lang;
				if(!isset($this->currLang)) {
					$this->currLang = $conf->defaultUILang;
				} else if(!array_key_exists($this->currLang, $conf->langsSystem)) {
					if(isset( $conf->langsSystemMap[$this->currLang])) {
						$this->currLang = $conf->langsSystemMap[$this->currLang];
					} else { // use default
						$this->currLang = $conf->defaultUILang;
					}
				}
			}
			$this->Session->write('Config.language', $this->currLang);
			Configure::write('Config.language', $this->currLang);
		}
		$this->set('currLang', $this->currLang);
		if(isset($conf->langsSystemMapRev[$this->currLang])) {
			$this->set('currLang2', $conf->langsSystemMapRev[$this->currLang]);
		} else {
			$this->set('currLang2', $conf->defaultUILang2);
		}

		if(isset( $conf->locales[$this->currLang])) {
			$this->currLocale = setlocale(LC_ALL, $conf->locales[$this->currLang]);
		} else {
			$this->currLocale = setlocale(LC_ALL, '');
		}
		$this->set('currLocale', $this->currLocale);

		if(isset( $conf->datePatternLocale[$this->currLang])) {
			Configure::write('datePattern', $conf->datePatternLocale[$this->currLang]);
		}
		if(isset( $conf->dateTimePatternLocale[$this->currLang])) {
			Configure::write('dateTimePattern', $conf->dateTimePatternLocale[$this->currLang]);
		}
		$dateFormatValidation = $conf->datePattern;
		$dateFormatValidation = preg_replace(array("/%d/", "/%m/", "/%Y/"), array("dd","mm","yyyy"), $dateFormatValidation);
		Configure::write('dateFormatValidation', $dateFormatValidation);
	}

	/**
	 * Redirect and class method result.
	 * If form contains:
	 * 		$this->data['OK'] or $this->data['ERROR']
	 * redirect to thes values (on OK, on ERROR)
	 *
	 * If the class defines:
	 * 		$this->REDIRECT[<method_name>]['OK'] or $this->REDIRECT[<method_name>]['ERROR']
	 * redirect to thes values (on OK, on ERROR)
	 *
	 * Otherwise no redirect
	 */
	final function beforeRender() {

		// convienience methods for frontends [like beforeRender]
        $this->beditaBeforeRender();

		if (defined('FULL_BASE_URL')) {
			$this->fullBaseUrl = FULL_BASE_URL;
		}
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

	final function afterFilter() {
		// convienience methods for frontends [like afterFilter]
		$this->beditaAfterFilter();
		$this->updateHistory();
	}

	protected function updateHistory() {
		// save history if configured
		if ($this->params["url"]["url"] != "/") {
			$historyConf = Configure::read("history");
			if ( !empty($historyConf) && $this->historyItem !== null && !$this->RequestHandler->isAjax() && !$this->RequestHandler->isFlash()) {
				$historyModel = ClassRegistry::init("History");
				$this->historyItem["url"] = ($this->params["url"]["url"]{0} != "/")? "/" . $this->params["url"]["url"] : $this->params["url"]["url"];
				$user = $this->BeAuth->getUserSession();
				if (!empty($user)) {
					$this->historyItem["user_id"] = $user["id"];
					if (!$historyModel->save($this->historyItem)) {
						return;
					}
					$this->historyItem["id"] = $historyModel->id;
					$this->BeAuth->updateSessionHistory($this->historyItem, $historyConf);
				} elseif (!empty($historyConf["trackNotLogged"])) {
					$historyModel->save($this->historyItem);
				}

			}
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

	/**
	 *  local 'afterFilter' (for backend or frontend)
	 */
	protected function beditaAfterFilter() {
	}

    protected function eventLog($level, $msg) {
		$u = isset($this->BeAuth->user["userid"])? $this->BeAuth->user["userid"] : "-";
		$event = array('EventLog'=>array("log_level"=>$level,
			"userid"=>$u, "msg"=>$msg, "context"=>strtolower($this->name)));
		$this->EventLog->create();
		$this->EventLog->save($event);
	}

	/**
	 * User error message (will appear in messages div)
	 * @param string $msg
	 */
	protected function userErrorMessage($msg, $layout="message", $params=array("class" => "error")) {
		$this->Session->setFlash($msg, $layout, $params, 'error');
	}

	/**
	 * User warning message (will appear in messages div)
	 * @param string $msg
	 */
	protected function userWarnMessage($msg, $layout="message", $params=array("class" => "warn")) {
		$this->Session->setFlash($msg, $layout, $params, 'warn');
	}

	/**
	 * User info message (will appear in messages div)
	 * @param string $msg
	 */
	protected function userInfoMessage($msg, $layout="message", $params=array("class" => "info")) {
		$this->Session->setFlash($msg, $layout, $params, 'info');
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
	 * Modules access: verify user permits.
	 * Uses BeAuth Component.
	 *
	 * Load the list of the modules availables for the user.
	 */
	protected function checkLogin() {
		static  $_loginRunning = false ;

		if($_loginRunning) return true ;
		else $_loginRunning = true ;

		// skip authorization check, if specific controller set skipCheck = true
		if($this->skipCheck) return true;

		// Verify authorization
		if(!$this->BeAuth->isLogged()) {
			if ($this->RequestHandler->isAjax()) {
				throw new BeditaAjaxException(__("Session Expired", true), array("output" => "reload"));
			}

			if ($this->view == 'Smarty') {
				echo $this->render(null, null, VIEWS."home".DS."login.tpl") ;
			} elseif ($this->view == 'ThemeSmarty'){
				echo $this->render(null, null, VIEWS."themed".DS.$this->theme.DS."home".DS."login.tpl") ;
			} elseif ($this->view == 'Theme'){
				echo $this->render(null, null, VIEWS."themed".DS.$this->theme.DS."home".DS."login.ctp") ;
			} else {
				echo $this->render(null, null, VIEWS."home".DS."login.ctp") ;
			}

			$_loginRunning = false;
			exit;
		}

		// module list
		$this->moduleList = ClassRegistry::init("PermissionModule")->getListModules($this->BeAuth->user["userid"]);
		$this->set('moduleList', $this->moduleList) ;
		$this->set('moduleListInv', array_reverse($this->moduleList)) ;

		// verify basic access
		if(isset($this->moduleName)) {
			$moduleStatus = ClassRegistry::init("Module")->field("status", array("name" => $this->moduleName));
			if ($moduleStatus != "on") {
				if ($this->RequestHandler->isAjax()) {
					throw new BeditaAjaxException(__("Module not available", true));
				}
				$logMsg = "Module [". $this->moduleName.  "] status off";
				$this->handleError($logMsg, __("Module not available",true), $logMsg);
				$this->redirect($this->referer());
			}
			foreach ($this->moduleList as $mod) {
			 	if($this->moduleName == $mod['name']) {
			 		$this->modulePerms = $mod['flag'];
				}
			}
			$this->set("module_modify",(isset($this->moduleName) && ($this->modulePerms & BEDITA_PERMS_MODIFY)) ? "1" : "0");
			if(!isset($this->modulePerms) || !($this->modulePerms & BEDITA_PERMS_READ)) {
				if ($this->RequestHandler->isAjax()) {
					throw new BeditaAjaxException(__("You haven't grants for this operation", true));
				}
				$logMsg = "Module [". $this->moduleName.  "] access not authorized";
				$this->handleError($logMsg, __("Module access not authorized",true), $logMsg);
				$this->redirect("/");
			}
			$this->set('moduleName', $this->moduleName);
			if (!empty($this->moduleName))
				$this->set('currentModule', $this->moduleList[$this->moduleName]);


		}

		$_loginRunning = false ;

		return true ;
	}

	/**
	 * Function arguments setup.
	 * If variable is null, use $this->params[url].
	 * Parameters:
	 * 0..N array:
	 * 		0	var name
	 * 		1	type [string] (for the function settype)
	 * 		2	reference to the variable to modify
	 *
	 */
	protected function setup_args() {
		$size = func_num_args() ;
		$args = func_get_args() ;

		for($i=0; $i < $size ; $i++) {
			// If parameter is in 'params' or in 'pass', load it
			if(isset($this->params["url"][$args[$i][0]]) && !empty($this->params["url"][$args[$i][0]])) {
				$args[$i][2] = $this->params["url"][$args[$i][0]] ;
				$this->passedArgs[$args[$i][0]] = $this->params["url"][$args[$i][0]] ;

			} elseif(isset($this->params["named"][$args[$i][0]]) && !empty($this->params["named"][$args[$i][0]])) {
				$args[$i][2] = $this->params["named"][$args[$i][0]] ;
				$this->passedArgs[$args[$i][0]] = $this->params["named"][$args[$i][0]] ;

			} elseif(isset($this->passedArgs[$args[$i][0]])) {
				$args[$i][2] = $this->passedArgs[$args[$i][0]] ;
			}

			// If value is not null, define type and insert in 'namedArgs'
			if(!is_null($args[$i][2])) {
				settype($args[$i][2], $args[$i][1]) ;
				$this->params["url"][$args[$i][0]] = $args[$i][2] ;

				$this->namedArgs[$args[$i][0]] = $args[$i][2] ;
			}
		}

	}


	protected function loadModelByObjectTypeId($obj_type_id) {
		$conf  = Configure::getInstance();
		if (isset($conf->objectTypes[$obj_type_id])) {
			$modelClass = $conf->objectTypes[$obj_type_id]["model"];
		} else {
			$modelClass = $conf->objectTypesExt[$obj_type_id]["model"];
		}
		return $this->loadModelByType($modelClass);
	}

	protected function loadModelByType($modelClass) {
		$model = ClassRegistry::init($modelClass);
		if($model === false) {
			throw new BeditaException(__("Object type not found - ", true).$modelClass);
		}
		return $model;
	}

	/**
	 * set model bindings and return the array used
	 *
	 * @param Model $modelObj
	 * @param string $level binding level as defined in Model::modelBindings array
	 * @return array
	 */
	protected function modelBindings(Model $modelObj, $level = 'default') {
		$conf = Configure::getInstance();
		$name = $modelObj->name;
		$bindings = array();
		if(isset ($this->modelBindings[$name])) {
			$bindings = $this->modelBindings[$name];
			$modelObj->contain($bindings);
		} else if(isset ($conf->modelBindings[$name])) {
			$bindings = $conf->modelBindings[$name];
			$modelObj->contain($bindings);
		} else {
			$bindings = $modelObj->containLevel($level);
		}
		return $bindings;
	}

	/**
	 * Reorder content objects relations in array where keys are relation names
	 *
	 * @param array $objectArray
	 * @param array $status, default get all objects
	 * @return array
	 */
	protected function objectRelationArray($objectArray, $status=array(), $options=array()) {
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
				if(isset($objDetail['url']))
					$objDetail['filename'] = substr($objDetail['url'],strripos($objDetail['url'],"/")+1);

				// set fields with "mainLanguage" value. Usually used in frontend (frontend_controller.php)
				if (!empty($options["mainLanguage"])) {
					if(!isset($this->BeLangText)) {
						App::import('Component', 'BeLangText');
						$this->BeLangText = new BeLangTextComponent();
					}
					$this->BeLangText->setObjectLang($objDetail, $options["mainLanguage"], $status);
				}
				$relationArray[$rel][] = $objDetail;
			}
		}
		return $relationArray;
	}

	/**
	 * Setup object array with annotations-type details
	 *
	 * @param array $objectArray
	 * @param array $status, default get all objects
	 * @return array
	 */
	protected function setupAnnotations(array &$objectArray, $status=array()) {
		$typesCount = array();
		$beObject = ClassRegistry::init("BEObject");
		$annotationModel = ClassRegistry::init("Annotation");
		foreach ($objectArray['Annotation'] as $obj) {
			$modelClass = $beObject->getType($obj['id']);
			$this->{$modelClass} = $this->loadModelByType($modelClass);
			$this->modelBindings($this->{$modelClass});
			if(!($objDetail = $this->{$modelClass}->findById($obj['id']))) {
				continue ;
			}
            if (empty($status) || in_array($objDetail["status"],$status)) {
				//$objectArray[$modelClass][] = $objDetail;
				$annotationModel->putAnnotationInThread($objectArray[$modelClass], $objDetail);
				if(!array_key_exists($modelClass, $typesCount)) {
					$typesCount[$modelClass] = 1;
				} else {
					$typesCount[$modelClass] = $typesCount[$modelClass] + 1;
				}
			}
		}
		foreach ($typesCount as $k => $v) {
			$objectArray['num_of_'.Inflector::underscore($k)] = $v;
		}
	}

	protected function checkObjectWritePermission($objectId) {
		$permission = ClassRegistry::init('Permission');
		if(!$permission->isWritable($objectId, $this->BeAuth->user))
			throw new BeditaException(__("No write permissions on object", true));
	}

	protected function saveObject(BEAppModel $beModel) {

		if(empty($this->data))
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;

		if(!$new) {
			$this->checkObjectWritePermission($this->data['id']);
		}

		// Format custom properties
		$this->BeCustomProperty->setupForSave() ;

		$name = Inflector::underscore($beModel->name);

		$categoryModel = ClassRegistry::init("Category");
		$tagList = array();
		if (!empty($this->params["form"]["tags"]))
			$tagList = $categoryModel->saveTagList($this->params["form"]["tags"]);
		$this->data["Category"] = (!empty($this->data["Category"]))? array_merge($this->data["Category"], $tagList) : $tagList;

		$fixed = false;
		if(!$new) {
			$fixed = ClassRegistry::init("BEObject")->isFixed($this->data['id']);
			if($fixed) { // unset pubblication date, TODO: throw exception if pub date is set!
				unset($this->data['start_date']);
				unset($this->data['end_date']);
			}
		}

		if(!isset($this->data['Permission']))
			$this->data['Permission'] = array() ;

		if(!$beModel->save($this->data)) {
			throw new BeditaException(__("Error saving $name", true), $beModel->validationErrors);
		}

		// handle tree. Section and Area handled in AreaController
		if(!$fixed && isset($this->data['destination']) && $beModel->name != "Section" &&  $beModel->name != "Area") {
			$this->BeTree->updateTree($beModel->id, $this->data['destination']);
		}
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
			$objectsToDel = array($this->data['id']);
		}

		$this->Transaction->begin() ;

		$beObject = ClassRegistry::init("BEObject");

		foreach ($objectsToDel as $id) {
			$this->checkObjectWritePermission($id);

			if ($beObject->isFixed($id)) {
				throw new BeditaException(__("Error, trying to delete fixed object!", true));
			}

			if ($model != "Stream") {
				if(!ClassRegistry::init($model)->delete($id))
					throw new BeditaException(__("Error deleting object: ", true) . $id);
			} else {
				if(!$this->BeFileHandler->del($id))
					throw new BeditaException(__("Error deleting object: ", true) . $id);
			}
			$objectsListDesc .= $id . ",";
		}
		$this->Transaction->commit() ;
		return trim($objectsListDesc, ",");
	}

	/**
	 * View revision data for a specific object and revision number:
	 *  * all data as in revision in $revision array
	 *  * array of changed fields in $diff
	 *
	 * @param BEAppModel $beModel
	 * @param int $id, object ud
	 * @param int $rev, revision number
	 */
	protected function viewRevision(BEAppModel $beModel, $id, $rev) {
		if(empty($id) || empty($rev)) {
			throw new BeditaException(__("Missing object id or revision number", true));
		}
		$versionModel = ClassRegistry::init("Version");
		$nRev = $versionModel->numRevisions($id);
		if($rev < 1 || $rev > $nRev) {
			throw new BeditaException(__("Wrong revision number", true));
		}
		$revisionData = $versionModel->revisionData($id, $rev, $beModel);
		$diffData = $versionModel->diffData($id, $rev);
		$this->set('totRevision',	$nRev);
		$this->set('revision',	$revisionData);
		$this->set('diff',	$diffData);
		$versionRow = $versionModel->find("all", array("conditions" =>
			array("Version.object_id" => $id, "Version.revision" => $rev)));
		$this->set('version',	$versionRow[0]["Version"]);
		$this->set('user',	$versionRow[0]["User"]);
		$conf = Configure::getInstance();
		$moduleName = $conf->objectTypes[Inflector::underscore($beModel->alias)]["module_name"];
		$this->set('moduleName', $moduleName);
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
	 * @param unknown_type $filter
	 * @param unknown_type $order
	 * @param unknown_type $dir
	 * @param unknown_type $page
	 * @param unknown_type $dim
	 */
	protected function paginatedList($id, $filter, $order, $dir, $page, $dim) {
		$this->setup_args(
			array("id", "integer", &$id),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;


		// get selected section
		$sectionSel = null;
		$pubSel = null;
		if(isset($id)) {
			$section = $this->loadModelByType("section");
			$section->containLevel("minimum");
			$sectionSel = $section->findById($id);
			$pubSel = $this->BeTree->getAreaForSection($id);
		}

		$filter["count_permission"] = true;

		$objects = $this->BeTree->getChildren($id, null, $filter, $order, $dir, $page, $dim)  ;
		$this->params['toolbar'] = &$objects['toolbar'] ;
		// template data
		$this->set('tree', $this->BeTree->getSectionsTree());
		$this->set('sectionSel',$sectionSel);
		$this->set('pubSel',$pubSel);
		$this->set('objects', $objects['items']);

		// set prevNext array to session
		$this->setSessionForObjectDetail($objects['items']);
	}


	public function changeStatusObjects($modelName=null) {
		$objectsToModify = array();
		$objectsListDesc = "";
		$beObject = ClassRegistry::init("BEObject");

		if(!empty($this->params['form']['objects_selected'])) {
			$objectsToModify = $this->params['form']['objects_selected'];

			$this->Transaction->begin() ;

			if (empty($modelName))
				$modelName = "BEObject";

			foreach ($objectsToModify as $id) {
				$this->checkObjectWritePermission($id);
				$model = $this->loadModelByType($modelName);
				if ($beObject->isFixed($id)) {
					throw new BeditaException(__("Error: changing status to a fixed object!", true));
				}
				$model->id = $id;
				if(!$model->saveField('status',$this->params['form']["newStatus"]))
					throw new BeditaException(__("Error saving status for item: ", true) . $id);
				$objectsListDesc .= $id . ",";
			}

			$this->Transaction->commit() ;
		}
		return trim($objectsListDesc, ",");
	}

	public function assocCategory() {
		$this->checkWriteModulePermission();
		if(!empty($this->params['form']['objects_selected'])) {
			$objects_to_assoc = $this->params['form']['objects_selected'];
			$category_id = $this->data['category'];
			$beObject = ClassRegistry::init("BEObject");
			$categories = array();
			$this->Transaction->begin() ;
			foreach($objects_to_assoc as $k => $id) {
				$object_type_id = $beObject->findObjectTypeId($id);
				$modelLoaded = $this->loadModelByObjectTypeId($object_type_id);
				$modelLoaded->contain(array("BEObject"=>array("Category")));
				$obj = $modelLoaded->findById($id);
				$category_present = false;
				foreach($obj['Category'] as $key => $cat) {
					if($cat['id'] == $category_id) {
						$category_present = true;
					}
					$categories[$cat['id']] = $cat['id'];
				}
				if(!$category_present) {
					$categories[$category_id] = $category_id;
					unset($obj['Category']);
					$obj['Category'] = $categories;
					$modelLoaded->create();
					$modelLoaded->save($obj);
				}
			}
			$this->Transaction->commit() ;
			$this->userInfoMessage(__("Added items association to category", true) . " - " . $category_id);
			$this->eventInfo("added items association to category " . $category_id);
		}
	}

	public function disassocCategory() {
		$this->checkWriteModulePermission();
		if(!empty($this->params['form']['objects_selected'])) {
			$objects_to_assoc = $this->params['form']['objects_selected'];
			$category_id = $this->data['category'];
			$beObject = ClassRegistry::init("BEObject");
			$categories = array();
			$this->Transaction->begin() ;
			foreach($objects_to_assoc as $k => $id) {
				$object_type_id = $beObject->findObjectTypeId($id);
				$modelLoaded = $this->loadModelByObjectTypeId($object_type_id);
				$modelLoaded->contain(array("BEObject"=>array("Category")));
				$obj = $modelLoaded->findById($id);
				$category_present = false;
				foreach($obj['Category'] as $key => $cat) {
					if($cat['id'] == $category_id) {
						$category_present = true;
					} else {
						$categories[$cat['id']] = $cat['id'];
					}
				}
				if($category_present) {
					unset($obj['Category']);
					$obj['Category'] = $categories;
					$modelLoaded->create();
					$modelLoaded->save($obj);
				}
			}
			$this->Transaction->commit() ;
			$this->userInfoMessage(__("Removed items association to category", true) . " - " . $category_id);
			$this->eventInfo("removed items association to category " . $category_id);
		}
	}

	public function addItemsToAreaSection() {
		$this->itemsAreaSectionOp('add',$this->params['form']['objects_selected'],$this->data['destination']);
	}

	public function moveItemsToAreaSection() {
		$this->itemsAreaSectionOp('del',$this->params['form']['objects_selected'],$this->data['source'],false);
		$this->itemsAreaSectionOp('add',$this->params['form']['objects_selected'],$this->data['destination']);
	}

	public function removeItemsFromAreaSection() {
		$this->itemsAreaSectionOp('del',$this->params['form']['objects_selected'],$this->data['source']);
	}

	private function itemsAreaSectionOp($op='add',$objects_to_assoc=array(),$area_section_id=null,$user_info=true) {
		$this->checkWriteModulePermission();
		if(!empty($objects_to_assoc)) {
			$modelTree = ClassRegistry::init("Tree");
			$beObject = ClassRegistry::init("BEObject");
			$object_type_id = $beObject->findObjectTypeId($area_section_id);
			$modelLoaded = $this->loadModelByObjectTypeId($object_type_id);
			$modelLoaded->contain("BEObject");
			if(!($area_section = $modelLoaded->findById($area_section_id))) {
				throw new BeditaException(sprintf(__("Error loading section: %d", true), $area_section_id));
			}
			$this->Transaction->begin() ;
			for($i=0; $i < count($objects_to_assoc) ; $i++) {
				if ($beObject->isFixed($objects_to_assoc[$i])) {
					throw new BeditaException(__("Error: modifying a fixed object!", true));
				}
				$parents = $this->BeTree->getParents($objects_to_assoc[$i]);
				$is_already_present = in_array($area_section_id, $parents);
				if ($op=='add' && !$is_already_present) {
					if(!$modelTree->appendChild($objects_to_assoc[$i],$area_section_id)) {
						throw new BeditaException( __("Error during append child", true));
					}
				} else if ($op=='del' && $is_already_present) {
					if(!$modelTree->removeChild($objects_to_assoc[$i],$area_section_id)) {
						throw new BeditaException( __("Error during remove child", true));
					}
				}
			}
			$this->Transaction->commit() ;
			$op_str = ($op=='add') ? "associated to" : "removed from";
			if($user_info) {
				$this->userInfoMessage(__("Items $op_str area/section", true) . " - " . $area_section['title']);
			}
			$this->eventInfo("items $op_str area/section " . $area_section_id);
		}
	}

	/**
	 * Return preview links for $obj_nick in publications
	 *
	 * @param $sections array of section/area id
	 * @param $obj_nick object nickname
	 * @return array of previews divided by publications, all object's url are in "object_url" => array
	 */
	public function previewsForObject($sections,$obj_nick) {
		$previews = array();
		if(empty($obj_nick) || empty($sections)) {
			return $previews;
		}

		$treeModel = ClassRegistry::init("Tree");
		$beObjectModel = ClassRegistry::init("BEObject");
		$areaModel = ClassRegistry::init("Area");

		$pubId = array();
		foreach($sections as $key => $section_id) {
			$path = $treeModel->field("parent_path", array("id" => $section_id));
			$path = trim($path,"/");
			$parents = array();
			$nickPath = "";
			if (!empty($path)) {
				$parents = explode("/", $path);
				$area_id = array_shift($parents);
				if (!empty($parents)) {
					foreach ($parents as $val) {
						$nickPath .= "/" . $beObjectModel->getNicknameFromId($val);
					}
				}
				$nickPath .= "/" . $beObjectModel->getNicknameFromId($section_id);
			} else {
				$area_id = $section_id;
			}

			$nickPath .= "/" . $obj_nick;

			if (empty($previews[$area_id])) {
				$previews[$area_id] = $areaModel->find("first", array(
					"conditions" => array("Area.id" => $area_id),
					"contain" => array("BEObject")
					)
				);
				$previews[$area_id]["object_url"] = array();
			}

			$obj_url = array();
			if (!empty($previews[$area_id]["public_url"])) {
				$obj_url["public_url"] = $previews[$area_id]["public_url"] . $nickPath;
			}
			if (!empty($previews[$area_id]["staging_url"])) {
				$obj_url["staging_url"] = $previews[$area_id]["staging_url"] . $nickPath;
			}

			$previews[$area_id]["object_url"][] = $obj_url;
		}

		return $previews;
	}

	public function cloneObject() {
		unset($this->data['id']);
		$this->data['status']='draft';
		$this->data['fixed'] = 0;
		$this->save();
	}

	protected function checkAutoSave() {
		$new = (empty($this->data['id'])) ? true : false ;
		$beObject = ClassRegistry::init("BEObject");
		if(!$new) {
			$objectId = $this->data['id'];
			$status = $beObject->field("status", "id = $objectId");
			if($status == "on") {
				throw new BeditaException(__("Autosave: bad status", true));
			}

			// check perms on object/module
		 	$user = $this->Session->read("BEAuthUser");
			$permission = ClassRegistry::init('Permission');
			if(!$permission->isWritable($objectId, $user)) {
				throw new BeditaException(__("Autosave: no write permission", true));
			}

			// check editors
			$objectEditor = ClassRegistry::init("ObjectEditor");
			$objectEditor->cleanup($objectId);
			$res = $objectEditor->loadEditors($objectId);
			if(count($res) > 1) {
				throw new BeditaException(__("Autosave: other editors present", true));
			}
		}

		if(!($this->modulePerms & BEDITA_PERMS_MODIFY)) {
			throw new BeditaException(__("Autosave: no module permission", true));
		}
	}

	public function autoSaveObject(BEAppObjectModel $model) {
		$this->checkAutoSave();
		// disable behaviors
		$disableBhv = array("RevisionObject", "Notify");
		$disabled = array();
		foreach ($disableBhv as $dis) {
			if($model->Behaviors->enabled($dis)) {
				$model->Behaviors->disable($dis);
				$disabled[] = $dis;
			}
		}
		$this->saveObject($model);
		// re-enable behaviors
		foreach ($disabled as $d) {
			$model->Behaviors->enable($d);
		}
		$this->set('id', $model->id);
	}

	protected function viewObject(BEAppModel $beModel, $id = null) {
		if(Configure::read("langOptionsIso") == true) {
			Configure::load('langs.iso') ;
		}
		$obj = null ;
		$parents_id = array();
		$relations = array();
		$relationsCount = array();
		$previews = array();
		$name = Inflector::underscore($beModel->name);
		if(isset($id)) {
			$id = ClassRegistry::init("BEObject")->objectId($id);
			$objEditor = ClassRegistry::init("ObjectEditor");
			$objEditor->cleanup($id);

			$beModel->containLevel("detailed");
			if(!($obj = $beModel->findById($id))) {
				throw new BeditaException(__("Error loading $name: ", true).$id);
			}
			if(!$beModel->checkType($obj['object_type_id'])) {
               throw new BeditaException(__("Wrong content type: ", true).$id);
			}
			if (!empty($obj['RelatedObject'])) {
				$relations = $this->objectRelationArray($obj['RelatedObject']);
			}
			foreach ($relations as $k=>$v) {
				$relationsCount[$k] = count($v);
			}
			if (!empty($obj['Annotation'])) {
				$this->setupAnnotations($obj);
			}
			unset($obj['Annotation']);
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

			$previews = $this->previewsForObject($parents_id, $obj["nickname"]);

			$this->historyItem["object_id"] = $id;
		}

		$property = $this->BeCustomProperty->setupForView($obj, Configure::read("objectTypes." . $name . ".id"));

		$this->set('object',	$obj);
		$this->set('attach', isset($relations['attach']) ? $relations['attach'] : array());
		$this->set('relObjects', $relations);
		$this->set('relationsCount', $relationsCount);
		$this->set('objectProperty', $property);
		$this->set('availabeRelations', $this->getAvailableRelations($name));

		$this->set('parents',	$parents_id);
		$this->set('tree', 		$this->BeTree->getSectionsTree());
		$this->set('previews',	$previews);
		$categoryModel = ClassRegistry::init("Category");
		$areaCategory = $categoryModel->getCategoriesByArea(Configure::read('objectTypes.'.$name.'.id'));
		$this->set("areaCategory", $areaCategory);
		$this->setSessionForObjectDetail();
	}


	/**
	 * set session vars to use in objects detail:
	 * 		- backFromView
	 * 		- array with prev and next
	 *
	 * @param array $objects if it's defined prepare prevNext array for session
	 */
	protected function setSessionForObjectDetail($objects=null) {
		$modulePath = $this->viewVars["currentModule"]["url"];

		// set array of previous and next objects
		if (!empty($objects) && strstr($this->here, $modulePath)) {
			foreach ($objects as $k => $o) {
				$prevNextArr[$o["id"]]["prev"] = (!empty($objects[$k-1]))? $objects[$k-1]["id"] : "";
				$prevNextArr[$o["id"]]["next"] = (!empty($objects[$k+1]))? $objects[$k+1]["id"] : "";
			}
			$this->Session->write("prevNext", $prevNextArr);
		}

		$backURL = $this->Session->read('backFromView');

		$baseModuleUrl = rtrim($this->base,"/") . "/" . $modulePath;
		// set backFromView session vars and reset prevNext if necessary
		if (!empty($this->here) && (strstr($this->here, $modulePath . "/index") || $this->here === $baseModuleUrl)) {
			$backURL = (empty($this->params["form"]["searchstring"]))? $this->here : rtrim($this->here,"/") . "/query:" . urlencode($this->params["form"]["searchstring"]);
			$this->Session->write('backFromView', $backURL);
		} elseif (empty($backURL) || !strstr($backURL, $modulePath) || !strstr($this->referer(), $modulePath)) {
			$this->Session->write('backFromView', $baseModuleUrl);
			$this->Session->write("prevNext", "");
		}
	}

	protected function loadCategories($objectTypes = array()) {
		$categoryModel = ClassRegistry::init("Category");
		$categoryModel->Behaviors->disable('CompactResult');
		$categories = $categoryModel->find("list", array(
			"fields" => array("id","label"),
			"conditions" => array("object_type_id" => $objectTypes),
			"order" => "label"
		));
		$categoryModel->Behaviors->enable('CompactResult');
		$this->set("categories",$categories);
	}

	protected function showCategories(BEAppModel $beModel) {
		$conf  = Configure::getInstance() ;
		$type = $conf->objectTypes[Inflector::underscore($beModel->name)]["id"];
		$categoryModel = ClassRegistry::init("Category");
		$this->set("categories", $categoryModel->find("all", array(
			"conditions" => array("Category.object_type_id" => $type), "order" => "label"
		)));
		$this->set("object_type_id", $type);
		$this->set("areasList", ClassRegistry::init("BEObject")->find('list', array(
										"conditions" => "object_type_id=" . Configure::read("objectTypes.area.id"),
										"order" => "title",
										"fields" => "BEObject.title"
										)
									)
								);
	}

	/**
	 * Returns array of available relations for an $objectType
	 * relations with "hidden" => true are excluded
	 * If "inverse" relation is defined ("inverse" => "inverseName"), then types on "right" side
	 *  will have "inverseName" relation
	 *
	 * @param string $objectType
	 * @return array
	 */
	protected function getAvailableRelations($objectType) {
		$allRelations = BeLib::getObject("BeConfigure")->mergeAllRelations();
		$availableRelations = array();
		foreach ($allRelations as $relation => $rule) {
			if (empty($rule["hidden"])) {
				// no rule defined
				if (empty($rule[$objectType]) && empty($rule["left"]) && empty($rule["right"])) {
					$availableRelations[] = $relation;
				// rule on objectType
				} elseif (!empty($rule[$objectType])) {
					$availableRelations[] = $relation;
				// rule on sideA / sideB
				} else {
					$addRelation = array();
					if (key_exists("left", $rule)) {
						if(is_array($rule["left"]) && (in_array($objectType, $rule["left"]) || (empty($rule["left"])))) {
							$addRelation[] = $relation;
						} else if($rule["left"] === $objectType) {
							$addRelation[] = $relation;
						}
					}
					if (key_exists("right", $rule)) {
						$rightRel = !empty($rule["inverse"]) ? $rule["inverse"] : $relation;
						if(is_array($rule["right"]) && (in_array($objectType, $rule["right"]) || (empty($rule["right"])))) {
							$addRelation[] = $rightRel;
						} else if($rule["right"] === $objectType) {
							$addRelation[] = $rightRel;
						}
					}
					$availableRelations= array_merge($availableRelations, $addRelation);
				}
			}
		}
		return array_unique($availableRelations);
	}


	/**
	 * return array of object types belong to module
	 *
	 * @param string $moduleName
	 * @return array
	 */
	protected function getModuleObjectTypes($moduleName) {
		$otModel = ClassRegistry::init("ObjectType");
		$ot = $otModel->find("all", array(
				"conditions" => array("module_name" => $moduleName),
				"fields" => "id",
				"contain" => array()
			)
		);
		$objectTypes = array();
		if (!empty($ot)) {
			foreach ($ot as $o) {
				$objectTypes[] = $o["ObjectType"]["id"];
			}
		}
		return $objectTypes;
	}

	/**
	 * Generic view method: to override in real modules or create specific view methods
	 * if more types are handled by this module, like view[ModelName] (e.g. viewDocument, viewEvent...)
	 * This methods will be called automagically....
	 *
	 * @param integer $id object id to view
	 */
	public function view($id) {
		$modelName = ClassRegistry::init("BEObject")->getType($id);
		$method = "view" . $modelName;
		if (!method_exists($this, $method)) {
			throw new BeditaException(__("Missing view method", true)." - ".$method);
		}
		$this->action = $method;
		$this->{$method}($id);
	}

	/**
	 * Generic delete method: to override in real modules.
	 * If more types are handled by this module create specific delete methods
	 * like delete[ModelName] (e.g. deleteDocument, deleteEvent...)
	 * This methods will be called automagically....
	 */
	public function delete() {
		$modelName = ClassRegistry::init("BEObject")->getType($this->data["id"]);
		$method = "delete" . $modelName;
		if (!method_exists($this, $method)) {
			throw new BeditaException(__("Missing delete method", true)." - ".$method);
		}
		$this->action = $method;
		$this->{$method}();
	}

	/**
	 * Generic save method: to override in real modules.
	 * If more types are handled by this module create specific 'save' methods
	 * like save[ModelName] (e.g. saveDocument, saveEvent...)
	 * This methods will be called automagically....
	 */
	public function save() {
		if(!empty($this->data["id"])) {
			$modelName = ClassRegistry::init("BEObject")->getType($this->data["id"]);
		} else {
			$objTypeId = $this->data["object_type_id"];
			$modelName = Configure::read("objectTypes.$objTypeId.model");
		}
		$method = "save" . $modelName;
		if (!method_exists($this, $method)) {
			throw new BeditaException(__("Missing save method", true)." - ".$method);
		}
		$this->action = $method;
		$this->{$method}();
	}

}
?>