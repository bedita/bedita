<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
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

/**
 * Controller base class for backends+frontends
 */
class AppController extends Controller {

    public $helpers = array('Javascript', 'Html', 'BeForm', 'Beurl', 'Tr', 'Session', 'Perms', 'BeEmbedMedia', 'SessionFilter');

    public $components = array(
        'BeAuth',
        'BeTree',
        'BeCustomProperty',
        'Transaction',
        'Cookie',
        'Session',
        'RequestHandler',
        'ResponseHandler',
        'BeHash',
        'SessionFilter'
    );

    public $uses = array('EventLog');

    public $view = 'Smarty';

    public $ext = '.tpl';

    protected $moduleName = NULL;
    protected $moduleList = NULL;
    protected $modulePerms = NULL;
    /**
     * Result types for methods
     */
    const OK        = 'OK' ;
    const ERROR     = 'ERROR' ;
    const VIEW_FWD = 'view://'; //

    public $result      = 'OK' ;
    protected $skipCheck = false;

    protected static $current = NULL;

    protected $currLang = NULL; // selected UI lang
    protected $currLocale = NULL; // selected UI locale

    protected $profiling = false;
    
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
    protected $fullBaseUrl = '';
    
    /**
     * fields to save in history table
     *
     * @var array, set to null to avoid history insert also with history configure var setted
     */
    protected $historyItem = array();

    /**
     * If object cache is activate by objectCakeCache config param
     * it contains instance of BeObjectCache class in libs
     *
     * @var BeObjectCache
     */
    public $BeObjectCache = null;

    /**
     * Constructor
     * If debugKit is enabled in configuration and it's found in plugins paths
     * then add it to self::componenets array
     */
    public function __construct() {
        BeLib::getObject('BeConfigure')->initConfig();
        if (Configure::read('debugKit') && App::import('Component', 'DebugKit.Toolbar')) {
            $this->components[] = 'DebugKit.Toolbar';
        }
        parent::__construct();
    }

    public static function currentController() {
        return self::$current;
    }

	public static function usedUrl() {
        $url = !empty($_GET['url']) ? $_GET['url'] : (!empty($_POST['url']) ? $_POST['url'] : '');
        if (!empty($url)) {
            $url = ' - url: ' . $url;
        }
        return $url;
    }

    public function handleError($eventMsg, $userMsg, $errTrace = null, $usrMsgParams = array()) {
        $url = self::usedUrl();
        $log = $eventMsg;
        $userid = $this->BeAuth->userid();
        if (!empty($userid)) {
            $log .= ' - ' . $userid;
        }
        $this->log($log . $url);
        if (!empty($errTrace)) {
            $this->log($errTrace, 'exception');
        }
        // end transactions if necessary
        if (isset($this->Transaction)) {
            if ($this->Transaction->started()) {
                $this->Transaction->rollback();
            }
        }

        if (BACKEND_APP) {
            $this->eventError($eventMsg);
            $layout = (!isset($usrMsgParams['layout']))? 'message' : $usrMsgParams['layout'];
            $params = (!isset($usrMsgParams['params']))? array('class' => 'error') : $usrMsgParams['params'];
            $params['detail'] = $eventMsg;
            $this->userErrorMessage($userMsg, $layout, $params);
        }
    }

    public function setResult($r) {
        $this->result=$r;
    }

    protected function initAttributes() {}

    /**
     *  convienience method to do operations before login check
     *  for example you could set AppController::skipCheck to true avoiding user session check
     */
    protected function beforeCheckLogin() {}
    
    
    /**
     * Start profiler
     */
    protected function startProfiler() {
        if (Configure::read('enableProfiling') && function_exists('xhprof_enable')) {
            $this->profiling = true;
            xhprof_enable();
        }
    }
    
    /**
     * Stop profiler and save data
     */
    protected function stopProfiler($save = true) {
        if ($this->profiling) {
            $xhprof_data = xhprof_disable();
            if ($save) {
                App::import('Vendor', 'xhprof_lib', array('file' => 'xhprof'.DS.'xhprof_lib.php'));
                App::import('Vendor', 'xhprof_runs', array('file' => 'xhprof'.DS.'xhprof_runs.php'));
                $xhprof_runs = new XHProfRuns_Default();
                $profileName = str_replace(array('http://', 'https://', '.'), '', $this->fullBaseUrl);
                $profileName .= '-'. $this->name . '-' . $this->action; 
                $run_id = $xhprof_runs->save_run($xhprof_data, $profileName);
                $this->log('Profile run saved: ' . $run_id, 'debug');
            }
            $this->profiling = false;
        }
    }
    
    final function beforeFilter() {
        $this->startProfiler();
	    // if frontend app (not staging) and object cache is active
        if (!BACKEND_APP && Configure::read('objectCakeCache') && !Configure::read('staging')) {
            $this->BeObjectCache = BeLib::getObject('BeObjectCache');
        }
        self::$current = $this;
        $conf = Configure::getInstance();
        $this->set('conf',  $conf);

        // check/setup localization
        $this->setupLocale();

        // only backend
        if (BACKEND_APP) {
            if(isset($this->data['login']) || $this->name === 'Authentications') {
                return;
            }
            // load publications public url
            $publications = ClassRegistry::init('Area')->find('all', array(
                'contain' => array('BEObject')
            ));
            $this->set('publications', $publications);
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
        $dateFormatValidation = preg_replace(array('/%d/', '/%m/', '/%Y/'), array('dd','mm','yyyy'), $dateFormatValidation);
        Configure::write('dateFormatValidation', $dateFormatValidation);
    }

    /**
     * Redirect and class method result.
     * If form contains:
     *      $this->data['OK'] or $this->data['ERROR']
     * redirect to thes values (on OK, on ERROR)
     *
     * If the class defines:
     *      $this->REDIRECT[<method_name>]['OK'] or $this->REDIRECT[<method_name>]['ERROR']
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

        // set up for view and BEDITA js object
        if (BACKEND_APP) {
            $allRelations = BeLib::getObject('BeConfigure')->mergeAllRelations();
            $this->set('allObjectsRelations', $allRelations);
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
        $this->stopProfiler();
    }

    protected function updateHistory() {
        // save history if configured
        if ($this->params['url']['url'] != '/') {
            $historyConf = Configure::read('history');
            if ( !empty($historyConf) && $this->historyItem !== null && !$this->RequestHandler->isAjax() && !$this->RequestHandler->isFlash()) {
                $historyModel = ClassRegistry::init('History');
                $this->historyItem['url'] = ($this->params['url']['url']{0} != '/')? '/' . $this->params['url']['url'] : $this->params['url']['url'];
                $user = $this->BeAuth->getUserSession();
                if (!empty($user)) {
                    $this->historyItem['user_id'] = $user['id'];
                    if (!$historyModel->save($this->historyItem)) {
                        return;
                    }
                    $this->historyItem['id'] = $historyModel->id;
                    $this->BeAuth->updateSessionHistory($this->historyItem, $historyConf);
                } elseif (!empty($historyConf['trackNotLogged'])) {
                    $historyModel->save($this->historyItem);
                }

            }
        }
    }

    protected function forward($action, $outcome) { return false ; }

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
        $u = $this->BeAuth->userid();
        if (empty($u)) {
            $u = '-';
        }
        $event = array('EventLog'=>array('log_level'=>$level,
            'userid'=>$u, 'msg'=>$msg, 'context'=>strtolower($this->name)));
        $this->EventLog->create();
        $this->EventLog->save($event);
    }

    /**
     * User error message (will appear in messages div)
     * @param string $msg
     */
    protected function userErrorMessage($msg, $layout='message', $params=array('class' => 'error')) {
        $this->Session->setFlash($msg, $layout, $params, 'error');
    }

    /**
     * User warning message (will appear in messages div)
     * @param string $msg
     */
    protected function userWarnMessage($msg, $layout='message', $params=array('class' => 'warn')) {
        $this->Session->setFlash($msg, $layout, $params, 'warn');
    }

    /**
     * User info message (will appear in messages div)
     * @param string $msg
     */
    protected function userInfoMessage($msg, $layout='message', $params=array('class' => 'info')) {
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
                throw new BeditaAjaxException(__('Session Expired', true), array('output' => 'reload'));
            }

            if ($this->Session->check('externalLoginRequestFailed')) {
                $msg = 'external login failed';
                $extUserId = $this->Session->read('externalLoginRequestFailed');
                if ($extUserId != true) {
                    $msg .= ': ' . $extUserId;
                }
                $this->eventWarn($msg);
                $this->userWarnMessage($msg);
                $this->Session->delete('externalLoginRequestFailed');
            };

            $this->set('externalAuthServices', $this->BeAuth->getExternalServices());

            if ($this->view == 'Smarty') {
                $viewFile = VIEWS . 'home' . DS . 'login.tpl';
            } elseif ($this->view == 'ThemeSmarty'){
                $viewFile = VIEWS . 'themed' . DS . $this->theme . DS . 'home' . DS . 'login.tpl';
            } elseif ($this->view == 'Theme'){
                $viewFile = VIEWS . 'themed' . DS . $this->theme . DS . 'home' . DS . 'login.ctp';
            } else {
                $viewFile = VIEWS . 'home' . DS . 'login.ctp';
            }

            if (Configure::read('debugKit') && isset($this->Component->_loaded['Toolbar'])) {
                $this->Toolbar->startup($this);
            }
            echo $this->render(null, null, $viewFile);
            $_loginRunning = false;
            exit;
        }

        // module list
        $this->moduleList = ClassRegistry::init('PermissionModule')->getListModules($this->BeAuth->userid());
        $this->set('moduleList', $this->moduleList) ;
        $this->set('moduleListInv', array_reverse($this->moduleList)) ;

        // verify basic access
        if(isset($this->moduleName)) {
            $moduleStatus = ClassRegistry::init('Module')->field('status', array('name' => $this->moduleName));
            if ($moduleStatus != 'on') {
                if ($this->RequestHandler->isAjax()) {
                    throw new BeditaAjaxException(__('Module not available', true));
                }
                $logMsg = 'Module ['. $this->moduleName.  '] status off';
                $this->handleError($logMsg, __('Module not available',true), $logMsg);
                $this->redirect($this->referer());
            }
            foreach ($this->moduleList as $mod) {
                if($this->moduleName == $mod['name']) {
                    $this->modulePerms = $mod['flag'];
                }
            }
            $this->set('module_modify',(isset($this->moduleName) && ($this->modulePerms & BEDITA_PERMS_MODIFY)) ? '1' : '0');
            if(!isset($this->modulePerms) || !($this->modulePerms & BEDITA_PERMS_READ)) {
                if ($this->RequestHandler->isAjax()) {
                    throw new BeditaAjaxException(__("You haven't grants for this operation", true));
                }
                $logMsg = 'Module ['. $this->moduleName.  '] access not authorized';
                $this->handleError($logMsg, __('Module access not authorized',true), $logMsg);
                $this->redirect('/');
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
     *      0   var name
     *      1   type [string] (for the function settype)
     *      2   reference to the variable to modify
     *
     */
    protected function setup_args() {
        $size = func_num_args() ;
        $args = func_get_args() ;

        for($i=0; $i < $size ; $i++) {
            // If parameter is in 'params' or in 'pass', load it
            if(isset($this->params['url'][$args[$i][0]]) && !empty($this->params['url'][$args[$i][0]])) {
                $args[$i][2] = $this->params['url'][$args[$i][0]] ;
                $this->passedArgs[$args[$i][0]] = $this->params['url'][$args[$i][0]] ;

            } elseif(isset($this->params['named'][$args[$i][0]]) && !empty($this->params['named'][$args[$i][0]])) {
                $args[$i][2] = $this->params['named'][$args[$i][0]] ;
                $this->passedArgs[$args[$i][0]] = $this->params['named'][$args[$i][0]] ;

            } elseif(isset($this->passedArgs[$args[$i][0]])) {
                $args[$i][2] = $this->passedArgs[$args[$i][0]] ;
            }

            // If value is not null, define type and insert in 'namedArgs'
            if(!is_null($args[$i][2])) {
                settype($args[$i][2], $args[$i][1]) ;
                $this->params['url'][$args[$i][0]] = $args[$i][2] ;

                $this->namedArgs[$args[$i][0]] = $args[$i][2] ;
            }
        }

    }


    protected function loadModelByObjectTypeId($obj_type_id) {
        $conf  = Configure::getInstance();
        if (isset($conf->objectTypes[$obj_type_id])) {
            $modelClass = $conf->objectTypes[$obj_type_id]['model'];
        } else {
            $modelClass = $conf->objectTypesExt[$obj_type_id]['model'];
        }
        return $this->loadModelByType($modelClass);
    }

    protected function loadModelByType($modelClass) {
        $model = ClassRegistry::init($modelClass);
        if($model === false) {
            throw new BeditaException(__('Object type not found - ', true).$modelClass);
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
     * set model bindings for BEdita object
     *
     * @param string $modelType model name of BE object
     * @param string $level model binding level
     * @return array that contains:
     *              "bindings_used" => multidimensional array of bindings used,
     *              "bindings_list" => one dimensional array with the simple list of bindings ordered using a "natural order" algorithm
     *
     */
    protected function setObjectBindings($modelType, $level = 'frontend') {
        if(!isset($this->{$modelType})) {
            $this->{$modelType} = $this->loadModelByType($modelType);
        }
    
        if (!$this->baseLevel) {
            $bindingsUsed = $this->modelBindings($this->{$modelType}, $level);
        } else {
            $bindingsUsed = array("BEObject" => array("LangText"));
            if ($modelType == "Section") {
                $bindingsUsed[] = "Tree";
            }
            $this->{$modelType}->contain($bindingsUsed);
        }
        $listOfBindings = BeLib::getInstance()->arrayValues($bindingsUsed, true);
        natsort($listOfBindings);
        return array("bindings_used" => $bindingsUsed, "bindings_list" => $listOfBindings);
    }

    
    /**
     * Reorder content objects relations in array where keys are relation names
     *
     * @param array $objectArray
     * @param array $status, default get all objects
     * @param array $options, possible values are
     *                        'mainLanguage' => set fields with "mainLanguage" value
     *                        'user' => in frontend app check frontend access that user
     * @return array
     */
    public function objectRelationArray($objectArray, $status=array(), $options=array()) {
        $conf = Configure::getInstance() ;
        $relationArray = array();
        $beObject = ClassRegistry::init("BEObject");
        $permission = ClassRegistry::init("Permission");
        foreach ($objectArray as $obj) {
            $rel = $obj['switch'];
            $modelClass = $beObject->getType($obj['object_id']);
            $this->{$modelClass} = $this->loadModelByType($modelClass);
            if (BACKEND_APP) {
                // TODO: return $bindings array like in setObjectBindings
                $this->modelBindings($this->{$modelClass}, 'default');
                $bindings = array();
            } else {
                $bindings = $this->setObjectBindings($modelClass);
            }
            
            $objDetail = null;
            if ($this->BeObjectCache) {
                $objDetail = $this->BeObjectCache->read($obj['object_id'], $bindings);
            }
            
            if (empty($objDetail)) {
                $objDetail = $this->{$modelClass}->findById($obj['object_id']);
                if (empty($objDetail)) {
                    continue;
                } elseif ($this->BeObjectCache) {
                    $this->BeObjectCache->write($obj['object_id'], $bindings, $objDetail);
                }
            }

            if (empty($status) || in_array($objDetail["status"],$status)) {
                // if frontend app add object_type and check frontend obj permission
                if (!BACKEND_APP) {
                    $objDetail['object_type'] = $modelClass;
                    if (!$this->skipCheck) {
                        $userdata = (!empty($options['user']))? $options['user'] : array();
                        $frontendAccess = $permission->frontendAccess($objDetail['id'], $userdata);
                        if ($frontendAccess == 'denied' && empty($this->showUnauthorized)) {
                            continue;
                        }
                        if ($frontendAccess == 'free') {
                            $objDetail['free_access'] = true;
                            $objDetail['authorized'] = true;
                        } else {
                            $objDetail['free_access'] = false;
                            $objDetail['authorized'] = ($frontendAccess == 'full') ? true : false;
                        }
                    } else {
                        $objDetail['free_access'] = true;
                        $objDetail['authorized'] = true;
                    }
                }

                $objDetail['priority'] = $obj['priority'];
                $objDetail['params'] = !empty($obj['params']) ? $obj['params'] : array();
                if (isset($objDetail['url'])) {
                    $objDetail['filename'] = substr($objDetail['url'],strripos($objDetail['url'],"/")+1);
                }

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
        $beObject = ClassRegistry::init('BEObject');
        $annotationModel = ClassRegistry::init('Annotation');
        foreach ($objectArray['Annotation'] as $obj) {
            $modelClass = $beObject->getType($obj['id']);
            $this->{$modelClass} = $this->loadModelByType($modelClass);
            $level = (BACKEND_APP)? 'default' : 'frontend';
            $this->modelBindings($this->{$modelClass}, $level);
            if(!($objDetail = $this->{$modelClass}->findById($obj['id']))) {
                continue ;
            }
            if (empty($status) || in_array($objDetail['status'],$status)) {
                // if frontend app add object_type
                if (!BACKEND_APP) {
                    $objDetail['object_type'] = $modelClass;
                }
                if (!isset($objectArray[$modelClass])) {
                    $objectArray[$modelClass] = array();
                }
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
        if (!$permission->isWritable($objectId, $this->BeAuth->getUser())) {
            throw new BeditaException(__('No write permissions on object', true));
        }
    }

    protected function saveObject(BEAppModel $beModel, array $options = array()) {
        if (empty($this->data)) {
            throw new BeditaException( __('No data', true));
        }

        $options += array(
            'handleTagList' => true, // true to handle comma separated tag list creating tags don't exist
            'emptyPermission' => true, // true to remove permission ob object if any is passed
            'saveTree' => true // true to save tree
        );

        $new = (empty($this->data['id'])) ? true : false ;
        if (!$new) {
            $this->checkObjectWritePermission($this->data['id']);
        }

        // Format custom properties
        $this->BeCustomProperty->setupForSave() ;

        $name = Inflector::underscore($beModel->name);

        $categoryModel = ClassRegistry::init("Category");
		$tagList = array();

        if ($options['handleTagList']) {
            if (isset($this->params['form']['tags']) || isset($this->data['Category'])) {
                if (!empty($this->params['form']['tags'])) {
                    $tagList = $categoryModel->saveTagList($this->params['form']['tags']);
                }

                $this->data['Category'] = (!empty($this->data['Category']))? array_merge($this->data['Category'], $tagList) : $tagList;
            }
        }

        $fixed = false;
        if (!$new) {
            $fixed = ClassRegistry::init('BEObject')->isFixed($this->data['id']);
            if($fixed) { // unset pubblication date, TODO: throw exception if pub date is set!
                unset($this->data['start_date']);
                unset($this->data['end_date']);
            }
        }

        if ($options['emptyPermission'] && !isset($this->data['Permission'])) {
            $this->data['Permission'] = array();
        }

        if (isset($this->data['DateItem'])) {
            // reorder array index from 0 to avoid removal
            $this->data['DateItem'] = array_values($this->data['DateItem']);
        }

        if (!$beModel->save($this->data)) {
            throw new BeditaException(__("Error saving $name", true), $beModel->validationErrors);
        }

        // handle tree. Section and Area handled in AreaController
        if ($options['saveTree']) {
            if(!$fixed && isset($this->data['destination']) && $beModel->name != 'Section' &&  $beModel->name != 'Area') {
                if (!$new) {
                    $this->BeTree->setupForSave($beModel->id, $this->data['destination']);
                }
                ClassRegistry::init('Tree')->updateTree($beModel->id, $this->data['destination']);
            }
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
        $objectsListDesc = '';

        if(!empty($this->params['form']['objects_selected'])) {
            $objectsToDel = $this->params['form']['objects_selected'];
        } else {
            if(empty($this->data['id']))
                throw new BeditaException(__('No data', true));
            $objectsToDel = array($this->data['id']);
        }

        $this->Transaction->begin() ;

        $beObject = ClassRegistry::init('BEObject');

        foreach ($objectsToDel as $id) {
            $this->checkObjectWritePermission($id);

            if ($beObject->isFixed($id)) {
                throw new BeditaException(__('Error, trying to delete fixed object!', true));
            }

            if ($model != 'Stream') {
                if(!ClassRegistry::init($model)->delete($id))
                    throw new BeditaException(__('Error deleting object: ', true) . $id);
            } else {
                if(!$this->BeFileHandler->del($id))
                    throw new BeditaException(__('Error deleting object: ', true) . $id);
            }
            $objectsListDesc .= $id . ',';
        }
        $this->Transaction->commit() ;
        return trim($objectsListDesc, ',');
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
            throw new BeditaException(__('Missing object id or revision number', true));
        }
        $versionModel = ClassRegistry::init('Version');
        $nRev = $versionModel->numRevisions($id);
        if($rev < 1 || $rev > $nRev) {
            throw new BeditaException(__('Wrong revision number', true));
        }
        $revisionData = $versionModel->revisionData($id, $rev, $beModel);
        $diffData = $versionModel->diffData($id, $rev);
        $this->set('totRevision',   $nRev);
        $this->set('revision',  $revisionData);
        $this->set('diff',  $diffData);
        $versionRow = $versionModel->find('all', array('conditions' =>
            array('Version.object_id' => $id, 'Version.revision' => $rev)));
        $this->set('version',   $versionRow[0]['Version']);
        $this->set('user',  $versionRow[0]['User']);
        $conf = Configure::getInstance();
        $moduleName = $conf->objectTypes[Inflector::underscore($beModel->alias)]['module_name'];
        $this->set('moduleName', $moduleName);
    }


}

/**
 * Base class for modules
 *
 */
abstract class ModulesController extends AppController {

	/**
	 * Controller-specific categorizable models.
	 *
	 * @var array
	 */
	protected $categorizableModels = array();

    /**
     * Define a relations order by Model
     * The order will be reflected in every module object view detail
     * Example:
     *
     * ```
     * array(
     *     'Image' => array('attached_to', 'poster_of'),
     *     'Video' => array('seealso')
     * )
     * ```
     *
     * @see self::viewObject()
     * @var array
     */
    protected $relationsOrder = array();

    // @todo uncomment once all module plugin will be updated to use BeSecurity
    // public function __construct() {
    //     $this->components[] = 'BeSecurity';
    //     parent::__construct();
    // }

    protected function checkWriteModulePermission() {
        if (isset($this->moduleName) && !($this->modulePerms & BEDITA_PERMS_MODIFY)) {
            throw new BeditaException(__('No write permissions in module', true));
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
            array('id', 'integer', &$id),
            array('page', 'integer', &$page),
            array('dim', 'integer', &$dim),
            array('order', 'string', &$order),
            array('dir', 'boolean', &$dir)
        );

        $sessionFilter = $this->SessionFilter->setFromUrl();
        $filter = array_merge($filter, $sessionFilter);

        // get selected section
        $sectionSel = null;
        $pubSel = null;
        if (isset($id)) {
            $section = $this->loadModelByType('section');
            $section->containLevel('minimum');
            $sectionSel = $section->findById($id);
            $pubSel = $this->BeTree->getAreaForSection($id);
        }

        $afterFilter = array(
            array(
                'className' => 'ObjectProperty',
                'methodName' => 'objectsCustomProperties'
            ),
            array(
                'className' => 'Content',
                'methodName' => 'appendContentFields'
            ),
            array(
                'className' => 'ObjectRelation',
                'methodName' => 'countRelations',
                'options' => array(
                    'relations' => array('attach', 'seealso', 'download')
                )
            ),
            array(
                'className' => 'Tree',
                'methodName' => 'countUbiquity'
            )
        );

        if (!empty($filter['afterFilter'])) {
            if (!isset($filter['afterFilter'][0])) {
                $filter['afterFilter'][] = $filter['afterFilter'];
            }
            $filter['afterFilter'] = array_merge($filter['afterFilter'], $afterFilter);
        } else {
            $filter['afterFilter'] = $afterFilter;
        }

        $filter['count_permission'] = true;

        $objects = $this->BeTree->getChildren($id, null, $filter, $order, $dir, $page, $dim);

        $this->params['toolbar'] = &$objects['toolbar'] ;

        $properties = ClassRegistry::init('Property')->find('all', array(
            'conditions' => array('object_type_id' => $filter['object_type_id']),
            'contain' => array()
        ));

        // get publications
        $user = $this->BeAuth->getUserSession();
        $expandBranch = array();
        if (!empty($filter['parent_id'])) {
            $expandBranch[] = $filter['parent_id'];
        } elseif (!empty($id)) {
            $expandBranch[] = $id;
        }
        $treeModel = ClassRegistry::init("Tree");
        $tree = $treeModel->getAllRoots($user['userid'], null, array('count_permission' => true), $expandBranch);

        // get available relations
        $availableRelations = array();
        if (!empty($filter['object_type_id'])) {
            $objectRelation = ClassRegistry::init('ObjectRelation');
            if (!is_array($filter['object_type_id'])) {
                $filter['object_type_id'] = array($filter['object_type_id']);
            }
            foreach ($filter['object_type_id'] as $objectTypeId) {
                $r = $objectRelation->availableRelations($objectTypeId);
                $availableRelations = array_merge($availableRelations, $r);
            }
        }

        // get Tags
        $listTags = ClassRegistry::init("Category")->getTags(array("cloud" => false));

        // template data
        $this->set('tree', $tree);
        $this->set('sectionSel',$sectionSel);
        $this->set('pubSel',$pubSel);
        $this->set('objects', $objects['items']);
        $this->set('properties', $properties);
        $this->set('availableRelations', $availableRelations);
        $this->set('listTags', $listTags);

        // set prevNext array to session
        $this->setSessionForObjectDetail($objects['items']);
    }


    public function changeStatusObjects($modelName=null) {
        $objectsToModify = array();
        $objectsListDesc = '';
        $beObject = ClassRegistry::init('BEObject');

        if(!empty($this->params['form']['objects_selected'])) {
            $objectsToModify = $this->params['form']['objects_selected'];

            $this->Transaction->begin() ;

            if (empty($modelName))
                $modelName = 'BEObject';

            foreach ($objectsToModify as $id) {
                $this->checkObjectWritePermission($id);
                $model = $this->loadModelByType($modelName);
                if ($beObject->isFixed($id)) {
                    throw new BeditaException(__('Error: changing status to a fixed object!', true));
                }
                $model->id = $id;
                if(!$model->saveField('status',$this->params['form']['newStatus']))
                    throw new BeditaException(__('Error saving status for item: ', true) . $id);
                $objectsListDesc .= $id . ',';
            }

            $this->Transaction->commit() ;
        }
        return trim($objectsListDesc, ',');
    }

    public function assocCategory() {
        $this->checkWriteModulePermission();
        if(!empty($this->params['form']['objects_selected'])) {
            $objects_to_assoc = $this->params['form']['objects_selected'];
            $category_id = $this->data['category'];
            $category = ClassRegistry::init('Category');
            $this->Transaction->begin() ;
            foreach($objects_to_assoc as $k => $id) {
                if(!$category->addObjectCategory($category_id, $id)) {
                    throw new BeditaException(__('Error saving object category', true) .
                              ' category: ' . $category_id, ' object: ' . $id);
                }
            }
            $this->Transaction->commit() ;
            $this->userInfoMessage(__('Added items association to category', true) . ' - ' . $category_id);
            $this->eventInfo('added items association to category ' . $category_id);
        }
    }

    public function disassocCategory() {
        $this->checkWriteModulePermission();
        if(!empty($this->params['form']['objects_selected'])) {
            $objects_to_assoc = $this->params['form']['objects_selected'];
            $category_id = $this->data['category'];
            $beObject = ClassRegistry::init('BEObject');
            $categories = array();
            $this->Transaction->begin() ;
            foreach($objects_to_assoc as $k => $id) {
                $object_type_id = $beObject->findObjectTypeId($id);
                $modelLoaded = $this->loadModelByObjectTypeId($object_type_id);
                $modelLoaded->contain(array('BEObject'=>array('Category')));
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
            $this->userInfoMessage(__('Removed items association to category', true) . ' - ' . $category_id);
            $this->eventInfo('removed items association to category ' . $category_id);
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
            $modelTree = ClassRegistry::init('Tree');
            $beObject = ClassRegistry::init('BEObject');
            $object_type_id = $beObject->findObjectTypeId($area_section_id);
            $modelLoaded = $this->loadModelByObjectTypeId($object_type_id);
            $modelLoaded->contain('BEObject');
            if(!($area_section = $modelLoaded->findById($area_section_id))) {
                throw new BeditaException(sprintf(__('Error loading section: %d', true), $area_section_id));
            }
            $this->Transaction->begin() ;
            for($i=0; $i < count($objects_to_assoc) ; $i++) {
                if ($beObject->isFixed($objects_to_assoc[$i])) {
                    throw new BeditaException(__('Error: modifying a fixed object!', true));
                }
                $parents = $this->BeTree->getParents($objects_to_assoc[$i]);
                $is_already_present = in_array($area_section_id, $parents);
                if ($op=='add' && !$is_already_present) {
                    if(!$modelTree->appendChild($objects_to_assoc[$i],$area_section_id)) {
                        throw new BeditaException( __('Error during append child', true));
                    }
                } else if ($op=='del' && $is_already_present) {
                    if(!$modelTree->removeChild($objects_to_assoc[$i],$area_section_id)) {
                        throw new BeditaException( __('Error during remove child', true));
                    }
                }
            }
            $this->Transaction->commit() ;
            $op_str = ($op=='add') ? 'associated to' : 'removed from';
            if($user_info) {
                $this->userInfoMessage(__("Items $op_str area/section", true) . ' - ' . $area_section['title']);
            }
            $this->eventInfo("items $op_str area/section " . $area_section_id);
        }
    }

    /**
     * Return preview links for $obj_nick in publications
     *
     * @param $sections array of section/area id
     * @param $obj_nick object nickname
     * @return array of previews divided by publications, all object's url are in 'object_url' => array
     */
    public function previewsForObject($sections,$obj_nick) {
        $previews = array();
        if(empty($obj_nick) || empty($sections)) {
            return $previews;
        }

        $treeModel = ClassRegistry::init('Tree');
        $beObjectModel = ClassRegistry::init('BEObject');
        $areaModel = ClassRegistry::init('Area');

        $pubId = array();
        foreach($sections as $key => $section_id) {
            $path = $treeModel->field('parent_path', array('id' => $section_id));
            $path = trim($path,'/');
            $parents = array();
            $nickPath = '';
            if (!empty($path)) {
                $parents = explode('/', $path);
                $area_id = array_shift($parents);
                if (!empty($parents)) {
                    foreach ($parents as $val) {
                        $nickPath .= '/' . $beObjectModel->getNicknameFromId($val);
                    }
                }
                $nickPath .= '/' . $beObjectModel->getNicknameFromId($section_id);
            } else {
                $area_id = $section_id;
            }

            $nickPath .= '/' . $obj_nick;

            if (empty($previews[$area_id])) {
                $previews[$area_id] = $areaModel->find('first', array(
                    'conditions' => array('Area.id' => $area_id),
                    'contain' => array('BEObject')
                    )
                );
                $previews[$area_id]['object_url'] = array();
            }

            $obj_url = array();
            if (!empty($previews[$area_id]['public_url'])) {
                $obj_url['public_url'] = $previews[$area_id]['public_url'] . $nickPath;
            }
            if (!empty($previews[$area_id]['staging_url'])) {
                $obj_url['staging_url'] = $previews[$area_id]['staging_url'] . $nickPath;
            }

            $previews[$area_id]['object_url'][] = $obj_url;
        }

        return $previews;
    }

    public function cloneObject() {
        unset($this->data['id']);
        unset($this->data['nickname']);
        $this->data['status'] = 'draft';
        $this->data['fixed'] = 0;
        $this->save();
    }

    protected function checkAutoSave() {
        $new = (empty($this->data['id'])) ? true : false ;
        $beObject = ClassRegistry::init('BEObject');
        if(!$new) {
            $objectId = $this->data['id'];
            $status = $beObject->field('status', "id = $objectId");
            if($status == 'on') {
                throw new BeditaException(__('Autosave: bad status', true));
            }

            // check perms on object/module
            $user = $this->Session->read('BEAuthUser');
            $permission = ClassRegistry::init('Permission');
            if(!$permission->isWritable($objectId, $user)) {
                throw new BeditaException(__('Autosave: no write permission', true));
            }

            // check editors
            $objectEditor = ClassRegistry::init('ObjectEditor');
            $objectEditor->cleanup($objectId);
            $res = $objectEditor->loadEditors($objectId);
            if(count($res) > 1) {
                throw new BeditaException(__('Autosave: other editors present', true));
            }
        }

        if(!($this->modulePerms & BEDITA_PERMS_MODIFY)) {
            throw new BeditaException(__('Autosave: no module permission', true));
        }
    }

    public function autoSaveObject(BEAppObjectModel $model) {
        $this->checkAutoSave();
        // disable behaviors
        $disableBhv = array('RevisionObject', 'Notify');
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
        if (Configure::read('langOptionsIso') == true) {
            Configure::load('langs.iso') ;
        }
        $obj = null ;
        $parents_id = array();
        $relations = array();
        $relationsCount = array();
        $previews = array();
        $name = Inflector::underscore($beModel->name);
        $treeModel = ClassRegistry::init('Tree');
        if (isset($id)) {
            // check if object is forbidden for user
            $user = $this->Session->read('BEAuthUser');
            $permission = ClassRegistry::init('Permission');
            if ($permission->isForbidden($id, $user)) {
                throw new BeditaException(__('Access forbidden to object', true) . " $id");
            }

            $id = ClassRegistry::init('BEObject')->objectId($id);
            $objEditor = ClassRegistry::init('ObjectEditor');
            $objEditor->cleanup($id);

            $beModel->containLevel('detailed');
            if (!($obj = $beModel->findById($id))) {
                throw new BeditaException(__("Error loading $name: ", true).$id);
            }
            if (!$beModel->checkType($obj['object_type_id'])) {
               throw new BeditaException(__('Wrong content type: ', true).$id);
            }
            if (!empty($obj['RelatedObject'])) {
                $relations = $this->objectRelationArray($obj['RelatedObject']);
                $obj['relations'] = $relations;
            }
            foreach ($relations as $k=>$v) {
                $relationsCount[$k] = count($v);
            }
            if (!empty($obj['Annotation'])) {
                $this->setupAnnotations($obj);
            }
            unset($obj['Annotation']);
            // build array of id's categories associated
            $obj['assocCategory'] = array();
            if (isset($obj['Category'])) {
                $objCat = array();
                foreach ($obj['Category'] as $oc) {
                    $objCat[] = $oc['id'];
                }
                $obj['assocCategory'] = $objCat;
            }
            $parents_id = $treeModel->getParents($id) ;

            $previews = $this->previewsForObject($parents_id, $obj['nickname']);

            $this->historyItem['object_id'] = $id;
        }

        $property = $this->BeCustomProperty->setupForView($obj, Configure::read('objectTypes.' . $name . '.id'));
        $relationsOrder = !empty($this->relationsOrder[$beModel->alias]) ? $this->relationsOrder[$beModel->alias] : array();
        $availabeRelations = ClassRegistry::init('ObjectRelation')->availableRelations($name, $relationsOrder);

        $this->set('object',    $obj);
        $this->set('attach', isset($relations['attach']) ? $relations['attach'] : array());
        $this->set('relObjects', $relations);
        $this->set('relationsCount', $relationsCount);
        $this->set('objectProperty', $property);
        $this->set('availabeRelations', $availabeRelations);

        // get publications
        $user = $this->BeAuth->getUserSession();
        $tree = $treeModel->getAllRoots($user['userid'], null, array('count_permission' => true), $parents_id);

        $this->set('tree', $tree);
        $this->set('parents', $parents_id);
        $this->set('previews', $previews);
        $categoryModel = ClassRegistry::init('Category');
        $areaCategory = $categoryModel->getCategoriesByArea(Configure::read('objectTypes.'.$name.'.id'));
        $this->set('areaCategory', $areaCategory);
        $this->setSessionForObjectDetail();
    }


    /**
     * set session vars to use in objects detail:
     *      - backFromView
     *      - array with prev and next
     *
     * @param array $objects if it's defined prepare prevNext array for session
     */
    protected function setSessionForObjectDetail($objects=null) {
        $modulePath = $this->viewVars['currentModule']['url'];

        // set array of previous and next objects
        if (!empty($objects) && strstr($this->here, $modulePath)) {
            foreach ($objects as $k => $o) {
                $prevNextArr[$o['id']]['prev'] = (!empty($objects[$k-1]))? $objects[$k-1]['id'] : '';
                $prevNextArr[$o['id']]['next'] = (!empty($objects[$k+1]))? $objects[$k+1]['id'] : '';
            }
            $this->Session->write('prevNext', $prevNextArr);
        }

        $backURL = $this->Session->read('backFromView');

        $baseModuleUrl = rtrim($this->base,'/') . '/' . $modulePath;

        // set backFromView session vars and reset prevNext if necessary
        if (!empty($this->here) && (strstr($this->here, $modulePath . '/index') || rtrim($this->here, '/') === $baseModuleUrl)) {
            $backURL = (empty($this->params['form']['searchstring']))? $this->here : rtrim($this->here,'/') . '/query:' . urlencode($this->params['form']['searchstring']);
            $this->Session->write('backFromView', $backURL);
        } elseif (empty($backURL) || !strstr($backURL, $modulePath) || !strstr($this->referer(), $modulePath)) {
            $this->Session->write('backFromView', $baseModuleUrl);
            $this->Session->write('prevNext', '');
        }
    }

    protected function loadCategories($objectTypes = array()) {
        $categoryModel = ClassRegistry::init('Category');
        $categoryModel->Behaviors->disable('CompactResult');
        $categories = $categoryModel->find('list', array(
            'fields' => array('id','label'),
            'conditions' => array('object_type_id' => $objectTypes),
            'order' => 'label'
        ));
        $categoryModel->Behaviors->enable('CompactResult');
        $this->set('categories',$categories);
    }

    protected function showCategories(BEAppModel $beModel) {
        $type = Configure::read('objectTypes.' . Inflector::underscore($beModel->name) . '.id');
        if (is_null($type)) {
            return;
        }
        $categoryModel = ClassRegistry::init('Category');
        $this->set('categories', $categoryModel->find('all', array(
            'conditions' => array('Category.object_type_id' => $type), 'order' => 'label'
        )));
        $this->set('object_type_id', $type);
        $this->set('areasList', ClassRegistry::init('BEObject')->find('list', array(
                                        'conditions' => 'object_type_id=' . Configure::read('objectTypes.area.id'),
                                        'order' => 'title',
                                        'fields' => 'BEObject.title'
                                        )
                                    )
                                );
	}

    /**
	 * Saves a category. Controllers should specify the list of categorizable models in $categorizableModels property.
	 */
	public function saveCategories() {
		$this->checkWriteModulePermission();

		$Category = ClassRegistry::init('Category');
        $ExceptionClass = $this->params['isAjax'] ? 'BeditaAjaxException' : 'BeditaException';
        $exceptionOptions = $this->params['isAjax'] ? array('output' => 'json') : array();

		if (empty($this->data['label'])) {
			throw new $ExceptionClass(__('No data', true), $exceptionOptions);
		}

		// Object type ID checks.
		if (!in_array(Configure::read('objectTypes.' . $this->data['object_type_id'] . '.model'), $this->categorizableModels)) {
			// Object type not categorizable in current controller.
			throw new $ExceptionClass(__('Object type not allowed', true), $exceptionOptions);
		}
		if (array_key_exists('id', $this->data)) {
			// Existing category.
			$cat = $Category->findById($this->data['id']);
			if ($cat['object_type_id'] != $this->data['object_type_id']) {
				// Trying to change object_type_id of category.
				throw new $ExceptionClass(__('Cannot change object type for category', true), $exceptionOptions);
			}
		}

		$this->Transaction->begin();
		if (!$Category->save($this->data)) {
			throw new $ExceptionClass(__('Error saving tag', true), $exceptionOptions + $Category->validationErrors);
		}
		$this->Transaction->commit();

        $this->eventInfo('Category [' .$this->data['label'] . '] saved');

        $info = __('Category saved', true) . ' - ' . $this->data['label'];
        if ($this->params['isAjax']) {
            $this->ResponseHandler->setType('json');
            $this->set(compact('info'));
            $this->set('_serialize', array('info'));
        } else {
            $this->userInfoMessage($info);
            $this->redirect('/'. $this->moduleName . '/categories');
        }
	}

	/**
	 * Deletes a category. Controllers should specify the list of categorizable models in $categorizableModels property.
     *
     * @deprecated
	 */
	public function deleteCategories() {
		$this->checkWriteModulePermission();

		$Category = ClassRegistry::init('Category');

		if (empty($this->data['ids'])) {
			throw new BeditaException(__('No data', true));
		}

		// Object type ID checks.
		$cat = $Category->findById($this->data['id']);
		if (!in_array(Configure::read('objectTypes.' . $cat['object_type_id'] . '.model'), $this->categorizableModels)) {
			// Object type not categorizable in current controller.
			throw new BeditaException(__('Object type not allowed', true));
		}

		$this->Transaction->begin();
		if (!$Category->delete($this->data['id'])) {
			throw new BeditaException(__('Error saving tag', true), $Category->validationErrors);
		}
		$this->Transaction->commit();

		$this->userInfoMessage(__('Category deleted', true) . ' - ' . $cat['label']);
		$this->eventInfo('Category ' . $this->data['id'] . '-' . $cat['label'] . ' deleted');
	}

    /**
     * Performs bulk actions on categories.
     */
    public function bulkCategories() {
        $this->checkWriteModulePermission();

        $Category = ClassRegistry::init('Category');

        $action = null;
        if (array_key_exists('merge', $this->data)) {
            $action = 'merge';
        } elseif (array_key_exists('delete', $this->data)) {
            $action = 'delete';
        } else {
            throw new BeditaException(__('Unknown action'), array_keys($this->data));
        }
        if (empty($this->data['ids'])) {
            throw new BeditaException(__('No data', true));
        }

        // Object type ID checks.
        $objectTypeIds = Set::classicExtract($Category->find('all', array(
            'contain' => array(),
            'fields' => array('object_type_id'),
            'conditions' => array('Category.id' => $this->data['ids'])
        )), '{n}.object_type_id');
        foreach (array_unique($objectTypeIds) as $otid) {
            if (!in_array(Configure::read('objectTypes.' . $otid . '.model'), $this->categorizableModels)) {
                // Object type not categorizable in current controller.
                throw new BeditaException(__('Object type not allowed', true));
            }
        }

        $mergeInfo;
        $this->Transaction->begin();
        if ($action == 'merge') {
            // Merge.
            $mergeId = array_shift($this->data['ids']);  // Get first category and remove from array.
            $objectIds = ClassRegistry::init('ObjectCategory')->find('list', array(
                'fields' => array('ObjectCategory.object_id'),
                'conditions' => array(
                    'ObjectCategory.category_id' => $this->data['ids'],
                ),
            ));  // Find objects in other categories.
            foreach ($objectIds as $id) {
                // Merge to elected category.
                if (!$Category->addObjectCategory($mergeId, $id)) {
                    throw new BeditaException(__('Error merging categories'), true);
                }
            }
            $mergeInfo = $Category->read('name', $mergeId);
        }
        //*/
        foreach ($this->data['ids'] as $id) {
            // Delete.
            if (!$Category->delete($id)) {
                throw new BeditaException(__('Error deleting categories'), true);
            }
        }
        /*/  // The following should be way faster but ain't working. Don't know why...
        if (!$Category->deleteAll(array('Category.id' => $this->data['ids']), true, false)) {
            // Delete.
            throw new BeditaException(__('Error deleting categories'), true);
        }
        //*/
        $this->Transaction->commit();

        $msg = $action == 'merge' ? "merged to {$mergeInfo['name']}" : 'deleted';
        $ids = implode(', ', $this->data['ids']);
        $this->userInfoMessage(__('Categories ' . $msg, true) . ' - ' . $ids);
        $this->eventInfo('Categories ' . $ids . ' ' . $msg);
    }

    /**
     * return array of object types belong to module
     *
     * @param string $moduleName
     * @return array
     */
    protected function getModuleObjectTypes($moduleName) {
        $otModel = ClassRegistry::init('ObjectType');
        $ot = $otModel->find('all', array(
                'conditions' => array('module_name' => $moduleName),
                'fields' => 'id',
                'contain' => array()
            )
        );
        $objectTypes = array();
        if (!empty($ot)) {
            foreach ($ot as $o) {
                $objectTypes[] = $o['ObjectType']['id'];
            }
        }
        return $objectTypes;
    }

    protected function loadFilters($filterType = 'export') {
        $ff = array();
        $filters = Configure::read('filters.' . $filterType);
        if (!empty($filters)) {
            foreach($filters as $filter => $className) {
                $filterModel = ClassRegistry::init($className);
                if (!empty($filterModel)) {
                    if (!empty($filterModel->label)) {
                        $ff[$className]['label'] = $filterModel->label;
                    }
                    if (!empty($filterModel->options)) {
                        $ff[$className]['options'] = $filterModel->options;
                    }
                    if (!empty($filterModel->defaultExtension)) {
                        $ff[$className]['defaultExtension'] = $filterModel->defaultExtension;
                    }
                }
                if (empty($ff[$className]['label'])) {
                    $ff[$className]['label'] = $filter;
                }
                if (empty($ff[$className]['options'])) {
                    $ff[$className]['options'] = array();
                }
                if (empty($ff[$className]['defaultExtension'])) {
                    $ff[$className]['defaultExtension'] = '';
                }
            }
        }
        return $ff;
    }

    /**
     * Generic view method: to override in real modules or create specific view methods
     * if more types are handled by this module, like view[ModelName] (e.g. viewDocument, viewEvent...)
     * This methods will be called automagically....
     *
     * @param integer $id object id to view
     */
    public function view($id) {
        $modelName = ClassRegistry::init('BEObject')->getType($id);
        $method = 'view' . $modelName;
        if (!method_exists($this, $method)) {
            throw new BeditaException(__('Missing view method', true).' - '.$method);
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
        $modelName = ClassRegistry::init('BEObject')->getType($this->data['id']);
        $method = 'delete' . $modelName;
        if (!method_exists($this, $method)) {
            throw new BeditaException(__('Missing delete method', true).' - '.$method);
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
        if(!empty($this->data['id'])) {
            $modelName = ClassRegistry::init('BEObject')->getType($this->data['id']);
        } else {
            $objTypeId = $this->data['object_type_id'];
            $modelName = Configure::read("objectTypes.$objTypeId.model");
        }
        $method = 'save' . $modelName;
        if (!method_exists($this, $method)) {
            throw new BeditaException(__('Missing save method', true).' - '.$method);
        }
        $this->action = $method;
        $this->{$method}();
    }

    /**
     * Default module controller forward given $action and $result.
     * Default rules are used, you may pass custom rules in $moduleRedirect array
     * 
     * @param string $action
     * @param string $result
     * @param string $moduleRedirect
     * @return mixed, redirect url or false if no redirect is found
     */
    public function moduleForward($action, $result, $moduleRedirect = array()) {
        $referer = $this->referer();
        if (!empty($this->uses)) {
            $modelName = $this->uses[0];
            $viewUrl = '/' . $this->moduleName . '/view/' . @$this->{$modelName}->id;
        } else {
            $viewUrl = $referer;
        }
        $categoriesUrl = '/'. $this->moduleName . '/categories';
        
        $defaultRedirect = array(
                'addItemsToAreaSection' =>  array(
                        'OK'    => $referer,
                        'ERROR' => $referer
                ),
                'assocCategory' =>  array(
                        'OK'    => $this->referer(),
                        'ERROR' => $this->referer()
                ),
                'bulkCategories'    => array(
                        'OK'    => $categoriesUrl,
                        'ERROR' => $categoriesUrl
                ),
                'changeStatusObjects'   =>  array(
                        'OK'    => $this->referer(),
                        'ERROR' => $this->referer()
                ),
                'cloneObject'   =>  array(
                        'OK'    => $viewUrl,
                        'ERROR' => $viewUrl
                ),
                'delete' => array(
                        'OK'    => $this->fullBaseUrl . $this->Session->read('backFromView'),
                        'ERROR' => $referer
                ),
                'deleteCategories'  => array(
                        'OK'    => $categoriesUrl,
                        'ERROR' => $categoriesUrl
                ),
                'deleteSelected' => array(
                        'OK'    => $referer,
                        'ERROR' => $referer
                ),
                'disassocCategory'  =>  array(
                        'OK'    => $this->referer(),
                        'ERROR' => $this->referer()
                ),
                'moveItemsToAreaSection'    =>  array(
                        'OK'    => $this->referer(),
                        'ERROR' => $this->referer()
                ),
                'removeItemsFromAreaSection'    =>  array(
                        'OK'    => $this->referer(),
                        'ERROR' => $this->referer()
                ),
                'save'  =>  array(
                        'OK'    => $viewUrl,
                        'ERROR' => $referer
                ),
                'view'  =>  array(
                        'ERROR' => '/'.$this->moduleName
                ),
        );
        $redirect = array_merge($defaultRedirect, $moduleRedirect);
        if (isset($redirect[$action][$result])) {
            return $redirect[$action][$result] ;
        }
        return false ;
    }

    /** 
     * Default forward for BEdita modules - to overrider in module controllers if needed
     * @see AppController::forward()
     */
    protected function forward($action, $result) {
        return $this->moduleForward($action, $result);
    }

}
