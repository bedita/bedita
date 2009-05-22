<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

App::import('Core', 'l10n');

/**
 * Controller base class for backends+frontends
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
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

	 	// Exit on login/logout
	 	if(isset($this->data["login"]) || $this->name === 'Authentications') {
			return;
		}
		// Check login
		$this->set('conf',  Configure::getInstance());
		// check/setup localization
		$this->setupLocale();
		$this->beditaBeforeFilter() ;
		
		if(!$this->checkLogin($this->skipCheck)) 
			return;
	}

	protected function setupLocale() {

		$this->currLang = $this->Session->read('Config.language');
		$conf = Configure::getInstance();
		if($this->currLang === null || empty($this->currLang)) {
			// read Cookie
			$lang = $this->Cookie->read('bedita.lang');
			if(isset($lang) && $conf->multilang === true) {
				$this->Session->write('Config.language', $lang);
				$this->currLang = $lang;
			} else {
				// HTTP autodetect
				$l10n = new L10n();
				$l10n->get();		
				$this->currLang = $l10n->lang;
				if(!isset($this->currLang)) {
					$this->currLang = $conf->defaultLang;
				} else if(!array_key_exists($this->currLang, $conf->langsSystem)) {
					if(isset( $conf->langsSystemMap[$this->currLang])) {
						$this->currLang = $conf->langsSystemMap[$this->currLang];
					} else { // use default
						$this->currLang = $conf->defaultLang;
					}
				}
			}
			$this->Session->write('Config.language', $this->currLang);
			Configure::write('Config.language', $this->currLang);
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
			if (!empty($this->moduleName))
				$this->set('currentModule', $moduleList[$this->moduleName]);
				
		
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
				if(isset($objDetail['path']))
					$objDetail['filename'] = substr($objDetail['path'],strripos($objDetail['path'],"/")+1);
				
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
		foreach ($objectArray['Annotation'] as $obj) {	
			$modelClass = $beObject->getType($obj['id']);
			$this->{$modelClass} = $this->loadModelByType($modelClass);
			$this->modelBindings($this->{$modelClass});
			if(!($objDetail = $this->{$modelClass}->findById($obj['id']))) {
				continue ;
			}
            if (empty($status) || in_array($objDetail["status"],$status)) {
				$objectArray[$modelClass][] = $objDetail;
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
	
	protected function saveObject(BEAppModel $beModel) {

		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// Verify object permits
//		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
//			throw new BeditaException(__("Error modify permissions", true));
		// Format custom properties
		$this->BeCustomProperty->setupForSave() ;

		$name = strtolower($beModel->name);
		
		$categoryModel = ClassRegistry::init("Category");
		$tagList = array();
		if (!empty($this->params["form"]["tags"]))
			$tagList = $categoryModel->saveTagList($this->params["form"]["tags"]);
		$this->data["Category"] = (!empty($this->data["Category"]))? array_merge($this->data["Category"], $tagList) : $tagList;
		
		$fixed = false;
		if(!$new) {
			$fixed = $this->BEObject->isFixed($this->data['id']);
			if($fixed) { // unset pubblication date, TODO: throw exception if pub date is set! 
				unset($this->data['start']);
				unset($this->data['end']);
			}
		}
			
		if(!$beModel->save($this->data)) {
			throw new BeditaException(__("Error saving $name", true), $beModel->validationErrors);
		}

		if(!$fixed) {
			if(!isset($this->data['destination'])) 
				$this->data['destination'] = array() ;
			$this->BeTree->updateTree($beModel->id, $this->data['destination']);
		}

		// update permissions
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
//		$this->Permission->saveFromPOST($beModel->id, $this->data['Permissions'], 
//	 			!empty($this->data['recursiveApplyPermissions']), $name);
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
//			if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
//				throw new BeditaException(__("Error delete permissions", true));
//			}
			$objectsToDel = array($this->data['id']);
		}

		$this->Transaction->begin() ;

		$beObject = ClassRegistry::init("BEObject");
		
		foreach ($objectsToDel as $id) {
//			if(!$this->Permission->verify($id, $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
//				throw new BeditaException(__("Error delete permissions", true));
//			}
			
			if ($beObject->isFixed($id)) {
				throw new BeditaException(__("Error, trying to delete fixed object!", true));
			}

			if ($model != "Stream") {
				if(!$this->{$model}->delete($id))
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

	 /**
	  * Add Link with Ajax...
	  */
	
	public function addLink() {
		$this->layout="empty";
	 	$this->data = $this->params['form'];
		$this->data["status"] = "on";
	 	$this->Transaction->begin() ;
		$linkModel = $this->loadModelByType("Link");
		$url = $this->data['url'];
		$title = $this->data['title'];
		if(!$linkModel->isHttp($url) && !$linkModel->isHttps($url)) {
			$url = "http://" . $url;
			$this->data['url'] = $url;
		}
		$link = $linkModel->find('all',array('conditions' =>array('url' => $url,'title' => $title)));
		if(!empty($link)) {
			$linkModel->id = $link[0]['id'];
		} else {
			if(!$linkModel->save($this->data)) {
				throw new BeditaException(__("Error saving link", true), $linkModel->validationErrors);
			}
		}
 		$this->Transaction->commit() ;
		if(empty($link)) {
			$this->eventInfo("link [". $this->data["title"]."] saved");
		}
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
				
				if ($this->BEObject->isFixed($objects_to_assoc[$i])) {
					throw new BeditaException(__("Error: modifying a fixed object!", true));
				}
				
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
	 * Return preview links for $obj_id in publications
	 * 
	 * @return array of previews - single preview is like array('url'=>'...','desc'=>'...')
	 * @param $sections array of section id
	 * @param $obj_id object id
	 */
	public function previewsForObject($sections,$obj_id,$status) {
		$previews = array();
		if(empty($obj_id) || empty($sections))
			return $previews;
		$pubId = array();
		foreach($sections as $section_id) {
			$a = $this->BeTree->getAreaForSection($section_id);
			if(!empty($a) && !in_array($a['id'], $pubId)) {
				//$desc = $this->BEObject->field('title',array("id=$section_id"));
				$field = ($status=='on') ? 'public_url' : 'staging_url';
				if(!empty($a[$field])) {
					$previews[]=array(
						'url'=>$a[$field]."/$obj_id",
						'desc'=>$a['title'],
						'kurl'=>$field
						);
				}
				$pubId[] = $a['id'];
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
/*
	public function autosave() {
		$this->layout = null;
		if( !empty($this->data['status']) && ( ($this->data['status']=='draft') || ($this->data['status']=='off') ) ) {
			try {
				if(empty($this->data['title'])) {
					$this->data['title'] = 'Draft ' . date("m.d.Y G:i:s");
				}
				$this->save();
			} catch(BeditaException $ex) {
				$errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();
				$this->log($errTrace);
				$this->setResult(self::ERROR);
			}
		}
		$this->render(null, null, VIEWS."common_inc/autosave.tpl");
	}
*/
	protected function viewObject(BEAppModel $beModel, $id = null) {
		if(Configure::read("langOptionsIso") == true) {
			Configure::load('langs.iso') ;
		}
		$obj = null ;
		$parents_id = array();
		$relations = array();
		$relationsCount = array();
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
		
			$previews = $this->previewsForObject($parents_id, $id, $obj['status']);
		}
		
		$property = $this->BeCustomProperty->setupForView($obj, Configure::read("objectTypes." . $name . ".id"));

		$this->set('object',	$obj);
		$this->set('attach', isset($relations['attach']) ? $relations['attach'] : array());
		$this->set('relObjects', $relations);
		$this->set('relationsCount', $relationsCount);
		$this->set('objectProperty', $property);
		$this->set('availabeRelations', $this->getAvailableRelations($name));
		
		$this->set('parents',	$parents_id);
		$this->set('tree', 		$this->BeTree->getSectionsTree($name));
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
		$modulePath = $this->viewVars["currentModule"]["path"];	
		
		// set array of previous and next objects
		if (!empty($objects) && strstr($this->here, $modulePath)) {
			foreach ($objects as $k => $o) {
				$prevNextArr[$o["id"]]["prev"] = (!empty($objects[$k-1]))? $objects[$k-1]["id"] : "";
				$prevNextArr[$o["id"]]["next"] = (!empty($objects[$k+1]))? $objects[$k+1]["id"] : "";
			}
			$this->Session->write("prevNext", $prevNextArr);
		}

		$backURL = $this->Session->read('backFromView');
		
		// set backFromView session vars and reset prevNext if necessary		
		if (!empty($this->here) && strstr($this->here, $modulePath . "/index")) {
			$backURL = (empty($this->params["form"]["searchstring"]))? $this->here : rtrim($this->here,"/") . "/search:" . urlencode($this->params["form"]["searchstring"]);
			$this->Session->write('backFromView', $backURL);
		} elseif (empty($backURL) || !strstr($backURL, $modulePath) || !strstr($this->referer(), $modulePath)) {
			$this->Session->write('backFromView', rtrim($this->base,"/") . "/" . $modulePath);
			$this->Session->write("prevNext", "");
		}
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
	
	/**
	 * return an array of available relations for common object defined form $objectType
	 * relations with "hidden" set to true are excluded from array  
	 * 
	 * @param string $objectType
	 * @return array
	 */
	protected function getAvailableRelations($objectType) {
		$objectTypeId = Configure::read("objectTypes." . $objectType . ".id");
		$allRelations = array_merge(Configure::read("objRelationType"), Configure::read("defaultObjRelationType"));
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
				} elseif (key_exists("left", $rule) 
							&& key_exists("right", $rule)
							&& is_array($rule["left"])
							&& is_array($rule["right"])
							) {
				
					if ( (in_array($objectTypeId, $rule["left"]) || in_array($objectTypeId, $rule["right"])) 
							|| (empty($rule["left"]) || empty($rule["right"]))) {
						$availableRelations[] = $relation;
					}
				} else {
					$availableRelations[] = $relation;
				}
			}
		}
		return $availableRelations;
	}
		
}
?>
