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

/**
 * Frontend base class (Frontend API)
 */
if (defined('BEDITA_CORE_PATH')) {
	require_once (BEDITA_CORE_PATH . DS . 'bedita_exception.php');
}

abstract class FrontendController extends AppController {

	/**
	 * object status used to filter
	 *
	 * @var array
	 */
	private $status = array('on');

	/**
	 * define which publication date has to be checked
	 * "start" = true to check Content.start_date <= now
	 * "end" = true to check Content.end_date >= now
	 *
	 * @var array
	 */
	protected $checkPubDate = array("start" => true, "end" => true);

	/**
	 * true to load objects in base level mode (BEObject without relations and LangText model are loaded)
	 *
	 * @var boolean
	 */
	protected $baseLevel = false;

	/**
	 * default options used to find sections' children
	 * 		"showAllContents" => true to get all sections' children when a content is selected
	 * 							 false to get only content selected
	 * 		"itemsByType" => true to divide children by type (i.e. Document, Event,....)
	 * 						 false to put all content type children in 'childContents' array and section type children in 'sectionChilds'
	 * 		"childrenParams" => array to define special filters ('filter' array) and pagination options ("order", "dir", "dim", "page")
	 * 							detail level ("detailed" => true, default false used only if "showAllContents" => true)
	 *
	 * @var array
	 */
	protected $sectionOptions = array("showAllContents" => true, "itemsByType" => false, "childrenParams" => array());

	/**
	 * set the XML format to display, possible values "tags", "attributes"
	 *
	 * @var string
	 */
	protected $xmlFormat = "attributes";

	/**
	 * current publication
	 *
	 * @var string
	 */
	protected $publication = "";

	/**
	 * default defined in captcha component
	 *
	 * @var array
	 */
	protected $captchaOptions = array();

	/**
	 * annotation options
	 * 		object type => find options (filter and pagination)
	 *
	 * @var array
	 */
	protected $annotationOptions = array("comment" => array());

	/**
	 * BE obj internal cache
	 * id => array(...)
	 *
	 * @var array
	 */
	protected $objectCache = array();

	/**
	 * tag and category options
	 *
	 * @var array
	 */
	protected $tagOptions = array();

	/**
	 * search options, attribute used on search
	 *
	 * @var array
	 */
	protected $searchOptions = array("order" => false, "dir" => 1, "dim" => 50, "page" => 1, "filter" => false);

	/**
	 * user logged in or not
	 *
	 * @var bool
	 */
	protected $logged = false;

	/**
	 * path to redirect after login action
	 *
	 * @var string
	 */
	protected $loginRedirect = "/";

	/**
	 * path to redirect after logout action
	 *
	 * @var string
	 */
	protected $logoutRedirect = "/";

	/**
	 * if it's true show unauthorized objects for user in list setting "authorized" => false in object array
	 * else the unauthorized objects aren't in list (default)
	 *
	 * main objects requested are always blocked if user is not authorized to see them
	 *
	 * @var bool
	 */
	protected $showUnauthorized = false;

	const UNLOGGED = "unlogged";
	const UNAUTHORIZED = "unauthorized";

	/**
	 * set FrontendController::logged private attribute
	 *
	 * check if there's an active session and try to login if user not logged
	 *  - if "authorizedGroups" array defined in frontend.ini.php, user has to be in one of those groups
	 *  - if "staging" is defined only backend authorized groups are permitted
	 *	- otherwise any group is accepted
	 *
	 * @return mixed
	 */
	protected function checkLogin() {
		if ($this->skipCheck || $this->logged) {
			return;
		}
		if(!$this->BeAuth->isLogged()) {
			if(Configure::read("staging") === true) {
				$frontendGroupsCanLogin = array(); // only backend authorized groups
			} else {
				// frontend only authorized groups (default empty)
				$confGroups = Configure::read("authorizedGroups");
				// which groups? authorized groups if defined, or any group
				$frontendGroupsCanLogin = (!empty($confGroups))? $confGroups :
					ClassRegistry::init("Group")->getList(array("backend_auth" => 0));
			}
			// try to login user if POST data are corrected
			if (!empty($this->params["form"]["login"])) {
				$userid = null;
				$password = null;

				if (isset($this->params["form"]["login"]["userid"])) {
					$userid = $this->params["form"]["login"]["userid"];
				} elseif (isset($this->params["form"]["login"]["username"])) {
					$userid = $this->params["form"]["login"]["username"];
				}

				if (isset($this->params["form"]["login"]["passwd"])) {
					$password = $this->params["form"]["login"]["passwd"];
				} elseif (isset($this->params["form"]["login"]["password"])) {
					$password = $this->params["form"]["login"]["password"];
				}

				$authType 	= (isset($this->params["form"]["login"]["auth_type"])) ? $this->params["form"]["login"]["auth_type"] : "bedita" ;
				$redirect 	= (!empty($this->params["form"]["backURL"]))? $this->params["form"]["backURL"] : $this->loginRedirect;

				if(!$this->BeAuth->login($userid, $password, null, $frontendGroupsCanLogin, $authType)) {
					//$this->loginEvent('warn', $userid, "login not authorized");
					$this->userErrorMessage(__("Wrong username/password or session expired", true));
					$this->logged = false;
				} else {
					$this->eventInfo("FRONTEND logged in publication");
				}

				if (!empty($redirect)) {
					$this->redirect($redirect);
				}
				$this->logged = true;
				return true;
			}
			$this->logged = false;
		} else {
			$this->logged = true;
		}


	}

	/**
	 * show login form or redirect if user is already logged
	 *
	 * @param string $backName nickname or id of section to go after login
	 */
	public function login($backName=null) {
		$urlToGo = (!empty($backName))? Router::url('/'. $backName, true) : $this->loginRedirect;
		if ($this->isLogged()) {
			$this->redirect($urlToGo);
		}
		$this->accessDenied(self::UNLOGGED);
	}

	/**
	 * perform logout operation
	 *
	 * @param boolean $autoRedirect
	 */
	public function logout($autoRedirect=true) {
		$this->BeAuth->logout();
		$this->eventInfo("FRONTEND logged out: publication " . $this->publication["title"]);
		if ($autoRedirect) {
			$this->redirect($this->logoutRedirect);
		}
	}

	/**
	 * manage access denied. If you want another behavior override it in pages_controller
	 *
	 * user unlogged: render login view (if user doesn't arrive from login page set info message)
	 * user unauthorized to access that item: render unauthorized view (set error message)
	 *
	 * for other custom type will try to render a view with $type name
	 * 		(i.e. $type="access_denied" render views/pages/access_denied.[tpl|ctp] template)
	 *
	 * @param string $type, which type of access denied
	 * @throws BeditaFrontAccessException
	 */
	protected function accessDenied($type) {
		$headers = array();
		if ($type == self::UNLOGGED && !strstr($this->here, '/login')) { // 401
			throw new BeditaUnauthorizedException(__('You have to be logged to access that item', true));
		} elseif ($type == self::UNAUTHORIZED) { // 403
			throw new BeditaForbiddenException(__('You are not authorized to access that item', true));
		}
	}

	/**
	 * get private logged var
	 *
	 * @return boolean
	 */
	protected function isLogged() {
		return $this->logged;
	}

	/**
	 * called before action to initialize
	 * $uses & $components array don't work... (abstract class ??)
	 *
	 * @throws BeditaPublicationException
	 * @see bedita-app/AppController#initAttributes()
	 */
	final protected function initAttributes() {
		if(!isset($this->BEObject)) {
			$this->BEObject = $this->loadModelByType('BEObject');
		}
		if(!isset($this->Section)) {
			$this->Section = $this->loadModelByType('Section');
		}
		if(!isset($this->Stream)) {
			$this->Stream = $this->loadModelByType('Stream');
		}
		if(!isset($this->BeLangText)) {
			App::import('Component', 'BeLangText');
			$this->BeLangText = new BeLangTextComponent();
		}
		if(!isset($this->Tree)) {
			$this->Tree = $this->loadModelByType('Tree');
		}
		$conf = Configure::getInstance() ;
		if (!empty($conf->draft))
			$this->status[] = "draft";

		// check publication status
		$pubStatus = $this->BEObject->field("status", array("id" => Configure::read("frontendAreaId")));

		if ($pubStatus != "on") {
			$statusSaved = $this->status;
			$this->status = array('on', 'off', 'draft');
			$this->publication = $this->loadObj(Configure::read("frontendAreaId"), false);
			$this->status = $statusSaved;
			$this->set('publication', $this->publication);
			if (Configure::read("draft") == false || $pubStatus == "off") {
				$this->publicationDisabled($pubStatus);
			}
		}
		$this->publication = $this->loadObj(Configure::read("frontendAreaId"),false);

		// set publication data for template
		$this->set('publication', $this->publication);

		// set filterPublicationDate
		$filterPubDate = Configure::read("filterPublicationDate");
		if (isset($filterPubDate)) {
			if (is_array($filterPubDate)) {
				$this->checkPubDate = $filterPubDate;
			} elseif ($filterPubDate === true) {
				$this->checkPubDate = array("start" => true, "end" => true);
			} elseif ($filterPubDate === false) {
				$this->checkPubDate = array("start" => false, "end" => false);
			}
		}

		$this->historyItem["area_id"] = $this->publication["id"];

		$this->checkPublicationPermissions();
	}

	/**
	 * Render off.tpl (or draft.tpl) layout when publication is disabled (off or draft)
	 *
	 * @param string $status the status to render
	 * @return void
	 */
	protected function publicationDisabled($status) {
		$this->set('_serialize', array('publication'));
		$this->render(false, $status);
		echo $this->output;
		$this->_stop();
	}

    protected function checkPublicationPermissions() {
        /*
         * if user is unlogged and it's a staging site OR
        * if user hasn't permissions to access at the publication
        * throws exception
        */
        if ( (!$this->logged && Configure::read("staging") === true) || 
            ((empty($this->params["pass"][0]) || $this->params["pass"][0] != "logout") 
                    && !$this->publication["authorized"])) {
            $errorType = (!$this->logged)? self::UNLOGGED : self::UNAUTHORIZED;
            $this->accessDenied($errorType);
         }
    }
	
	/**
	 * override AppController::setupLocale. Used setup specific locale
	 *
	 * @see bedita-app/AppController#setupLocale()
	 */
	protected function setupLocale() {

		$this->currLang = $this->Session->read('Config.language');
		$conf = Configure::getInstance();
		if($this->currLang === null || empty($this->currLang)) {
			if (isset($conf->cookieName["langSelect"])) {
				$lang = $this->Cookie->read($conf->cookieName["langSelect"]);
			}
			if(!empty($lang) && array_key_exists($lang, $conf->frontendLangs)) {
				$this->currLang = $lang;
			} else {
				// HTTP autodetect
				$l10n = new L10n();
				$l10n->get();
				$lang = $l10n->lang;
				if(!empty($lang)) {
					if(array_key_exists($lang, $conf->frontendLangs)) {
						$this->currLang = $lang;
					} else if (!empty($conf->frontendLangsMap[$lang])) {
						$lang = $conf->frontendLangsMap[$lang];
						if(array_key_exists($lang, $conf->frontendLangs)) {
							$this->currLang = $lang;
						}
					}
				}
				if(empty($this->currLang)) {
					$this->currLang = $conf->frontendLang;
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
	 * change language
	 *
	 * @param string $lang
	 * @param string $forward redirect action after changing language. If it's null redirect to refere
	 * @return string
	 * @throws BeditaException
	 */
	public function lang($lang, $forward = null) {

		if (empty($lang)) {
			throw new BeditaBadRequestException("No lang selected");
		}

		$conf = Configure::getInstance();
		if (!array_key_exists($lang, $conf->frontendLangs)) {
			throw new BeditaException("wrong lang selected: ".$lang);
		}
		$this->Session->write('Config.language', $lang);
		$this->Cookie->write($conf->cookieName["langSelect"], $lang, false, '+350 day');
		$this->currLang = $lang;

		if(!empty($forward)) {
			if (substr($forward, 0, 5) != "http:") {
				if (strpos("/", $forward) != 1)
					$forward = "/" . $forward;

				if (!empty($this->params["pass"][2])) {
					$forward .= "/" . implode("/", array_slice($this->params["pass"],2));
				}
			}

			$this->redirect($forward);
		} else {
			$this->redirect($this->referer());
		}

	}


	/**
	 * check if current date is compatible with required pubblication dates (start/end date)
	 *
	 * @param array $obj
	 * @return boolean, true if content may be published, false otherwise
	 */
	protected function checkPubblicationDate(array $obj) {
		$currDate = strftime("%Y-%m-%d");
		if(isset($obj["start_date"]) && $this->checkPubDate["start"]) {
			if(strncmp($currDate, $obj["start_date"], 10) < 0)
				return false;
		}
		if(isset($obj["end_date"]) && $this->checkPubDate["end"]) {
			if(strncmp($currDate, $obj["end_date"], 10) > 0)
				return false;
		}
		return true;
	}

	/**
	 * handle Exceptions
	 *
	 * @deprecated
	 * @param Exception $ex
	 * @return AppError
	 */
	public static function handleExceptions(Exception $ex) {

		if ($ex instanceof BeditaPublicationException) {
			$currentController = AppController::currentController();
			echo $currentController->render(false, $ex->getLayout());
		} elseif ($ex instanceof BeditaUnauthorizedException) {
			$params = array(
				'details' => $ex->getDetails(),
				'msg' => $ex->getMessage(),
				'result' => $ex->result,
				'errorType' => self::UNLOGGED,
				'headers' => array('HTTP/1.1 401 Unauthorized')
			);
			include_once (APP . 'app_error.php');
			return new AppError('handleExceptionFrontAccess', $params, $ex);
		} elseif ($ex instanceof BeditaForbiddenException) {
			$params = array(
				'details' => $ex->getDetails(),
				'msg' => $ex->getMessage(),
				'result' => $ex->result,
				'errorType' => self::UNAUTHORIZED,
				'headers' => array('HTTP/1.1 403 Unauthorized')
			);
			include_once (APP . 'app_error.php');
			return new AppError('handleExceptionFrontAccess', $params, $ex);
		} elseif ($ex instanceof BeditaFrontAccessException) {
			$errorType = $ex->getErrorType();
			$params = array(
				'details' => $ex->getDetails(),
				'msg' => $ex->getMessage(),
				'result' => $ex->result,
				'errorType' => $ex->getErrorType(),
				'headers' => $ex->getHeaders()
			);

			include_once (APP . 'app_error.php');
			return new AppError('handleExceptionFrontAccess', $params, $ex);
		} elseif ($ex instanceof BeditaRuntimeException) {
			include_once (APP . 'app_error.php');
			return new AppError('handleExceptionRuntime',
					array('details' => $ex->getDetails(), 'msg' => $ex->getMessage(),
					'result' => $ex->result), $ex);
		} elseif ($ex instanceof SmartyException) {
			include_once (APP . 'app_error.php');
			$trace = $ex->getFile()." - line: ". $ex->getLine()." \nTrace:\n". $ex->getTraceAsString();
			return new AppError('handleExceptionRuntime', array('msg' => $ex->getMessage(), 'details' => ''), $ex);
		} elseif ($ex instanceof BeditaAjaxException) {
			include_once (APP . 'app_error.php');
			$params = array(
				'details' => $ex->getDetails(),
				'msg' => $ex->getMessage(),
				'result' => $ex->result,
				'output' => $ex->getOutputType(),
				'headers' => $ex->getHeaders()
			);
			// set error 500 as default
			if ($params['headers'] === null) {
				$params['headers'] = array("HTTP/1.1 500 Internal Server Error");
			}
			return new AppError("handleBeditaAjaxException", $params, $ex);
		} else {

			if($ex instanceof BeditaException) {
				$errTrace =  $ex->errorTrace();
				$details = $ex->getDetails();
				$result = $ex->result;
			} else {
				$errTrace =  get_class($ex)." -  ". $ex->getMessage().
					"\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();
				$details = "";
				$result = "";
			}
			include_once (APP . 'app_error.php');
			return new AppError('handleExceptionFrontend',
					array('details' => $details, 'msg' => $ex->getMessage(),
					'result' => $result), $ex);

		}
	}

	/**
	* Get tree starting from specified section or area
	*
	* @param string|int $parentName		parent nickname or id
	* @param boolean $loadContents			if it's true load all contents too. Default false
	* @param array $exclude_nicknames	list exclude sections
	* @param int $depth				tree's depth level (default=null => all levels)
	* @return array
	* */
	protected function loadSectionsTree($parentName, $loadContents = false, $exclude_nicknames = array(), $depth = null, $flatMode = false) {

		$conf = Configure::getInstance();
		$parent_id = is_numeric($parentName) ? $parentName: $this->BEObject->getIdFromNickname($parentName);
		$result = array();
		$filter["object_type_id"] = $conf->objectTypes['section']["id"];
		if (empty($parent_id)) {
			throw new BeditaBadRequestException(__('Error loading sections tree. Missing parent', true)  . ': ' . $parentName);
		}

        $sections = array();
        $cacheOpts = array();
        if ($this->BeObjectCache) {
            $cacheOpts = array($parent_id, $this->status, $filter, "priority");
            $sections = $this->BeObjectCache->read($parent_id, $cacheOpts, 'children');
        }

        if (empty($sections)) {
            $sections = $this->BeTree->getChildren($parent_id, $this->status, $filter, "priority");
            if ($this->BeObjectCache) {
                $this->BeObjectCache->write($parent_id, $cacheOpts, $sections, 'children');
            }
        }

		foreach ($sections['items'] as $s) {

			if(!empty($exclude_nicknames) && in_array($s['nickname'], $exclude_nicknames)) {
				continue;
			}

			$sectionObject = $this->loadObj($s['id']);

			if ($sectionObject !== self::UNLOGGED && $sectionObject !== self::UNAUTHORIZED) {

				$resultSections = array();
				$resultObjects = array();
				$this->setCanonicalPath($sectionObject);
				if($loadContents) {
					$option = array("filter" => array("object_type_id" => Configure::read("objectTypes.leafs.id")));
					$objs = $this->loadSectionObjects($s['id'], $option);
					$resultObjects = (!$this->sectionOptions["itemsByType"] && !empty($objs["childContents"]))? $objs["childContents"] : $objs;
				}
				if ($depth === null || $depth > 1) {
					$innerDepth = ($depth === null) ? null : $depth-1;
					$resultSections = $this->loadSectionsTree($s['id'], $loadContents, $exclude_nicknames, $innerDepth, $flatMode);
				}
				if(!$flatMode) {
					if(!empty($resultObjects)) {
						$sectionObject['objects'] = $resultObjects;
					}
					if(!empty($resultSections)) {
						$sectionObject['sections'] = $resultSections;
					}
					$result[] = $sectionObject;
				} else {

					$result[] = $sectionObject;
					if(!empty($resultSections)) {
						$result = array_merge($result, $resultSections);
					}
					if(!empty($resultObjects)) {
						$result = array_merge($result, $resultObjects);
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Set canonical path in object data array, check parent authorization
	 *  - $obj["canonicalPath"] will contain new caclulated canonical path
	 *  - update  $this->objectCache
	 *  - setup $obj["parentAuthorized"]
	 *  - setup $obj["pathSection"] for sections
	 *
	 * @param array $obj, containing at least "id" and "nickname"
	 */
	protected function setCanonicalPath(array &$obj) {

		$objectId = $obj["id"];

		if(isset($this->objectCache[$objectId]["canonicalPath"]) &&
			isset($this->objectCache[$objectId]["parentAuthorized"])) {
			$obj["canonicalPath"] = $this->objectCache[$objectId]["canonicalPath"];
			$obj["parentAuthorized"] = $this->objectCache[$objectId]["parentAuthorized"];
			return;
		}

		if($obj["object_type_id"] == Configure::read("objectTypes.area.id")) {
			$obj["canonicalPath"] = "/";
			$obj["parentAuthorized"] = $obj["authorized"];
			return;
		}
		$pathSection = $this->getPath($objectId);
		if($obj["object_type_id"] == Configure::read("objectTypes.section.id")) {
			$obj["pathSection"] = $pathSection;
		}

		$parentAuthorized = true;
		foreach ($pathSection as $ps) {
			if ($parentAuthorized && isset($ps["authorized"]) && !$ps["authorized"]) {
				$parentAuthorized = false;
			}
		}
		$obj["parentAuthorized"] = $parentAuthorized;

		$canPath = "";
		if(!empty($pathSection)) {
			$parentSec = end($pathSection);
			$canPath = $parentSec["canonicalPath"];
		}

		$canPath .= (($canPath === "/") ? "" : "/");
		$menu = true;
		if(!empty($obj["object_type_id"]) && ($obj["object_type_id"] == Configure::read("objectTypes.section.id"))) {
			$menu = !isset($obj["menu"]) ? true : (($obj["menu"] === '0') ? false : true);
		}
		if($menu && !empty($obj["nickname"])) {
			$canPath .= $obj["nickname"];
		}
		$obj["canonicalPath"] = $canPath;
		if(isset($this->objectCache[$objectId])) {
			$this->objectCache[$objectId]["canonicalPath"] = $canPath;
			$this->objectCache[$objectId]["parentAuthorized"] = $parentAuthorized;
		}
		return;
	}

	/**
	 * Get sections levels
	 *
	 * Find all ancestors from secName and build an array of levels
	 * Each key in array returned is a level:
	 * 	0 is the first level
	 * 	1 is the second level
	 * 	etc...
	 *
	 * set selected = true in a section if it's an ancestor (parent) of $secName
	 *
	 * @param string $secName					nickname or section id
	 * @param boolean $loadContents		true meaning it loads all contents of each section
	 * @param array $exclude_nicknames	list exclude sections
	 *
	 * @return array of level selected
	 */
	protected function loadSectionsLevels($secName, $loadContents=false, array $exclude_nicknames=null) {
		$conf = Configure::getInstance();
		$result = array();

		$section_id = is_numeric($secName) ? $secName : $this->BEObject->getIdFromNickname($secName);

		$path = $this->Tree->field("object_path", array("id" => $section_id));
		$parents = explode("/", trim($path,"/"));

		$level = 0;
		$filter["object_type_id"] = $conf->objectTypes['section']["id"];
		foreach ($parents as $p_id) {
			$sections = $this->BeTree->getChildren($p_id, $this->status, $filter, "priority");

			foreach ($sections["items"] as $s) {

				if(!empty($exclude_nicknames) && in_array($s['nickname'], $exclude_nicknames))
					continue ;

				$sectionObject = $this->loadObj($s['id']);
				if ($sectionObject !== self::UNLOGGED && $sectionObject !== self::UNAUTHORIZED) {
					if (in_array($s["id"], $parents)) {
					 	$sectionObject["selected"] = true;
					}

					if($loadContents) {
						$option = array("filter" => array("object_type_id" => Configure::read("objectTypes.leafs.id")));
						$objs = $this->loadSectionObjects($s['id'], $option);
						$sectionObject['objects'] = (!$this->sectionOptions["itemsByType"] && !empty($objs["childContents"]))? $objs["childContents"] : $objs;
					}
					$result[$level][] = $sectionObject;
				}

			}

			$level++;
		}
		return $result;
	}

	/**
	 * load all publications
	 *
	 * @param string $tplVar, var name for template.
	 * 		  If not defined result will be set to "publicationsList" var
	 *
	 */
	protected function loadPublications($tplVar=null) {
		$publications = array();
		$filter = array("object_type_id" => Configure::read("objectTypes.area.id"));
		$res = $this->BEObject->findObjects(null, null, $this->status, $filter);
		if (!empty($res["items"])) {
			foreach ($res["items"] as $pub) {
				$obj = $this->loadObj($pub["id"]);
				if ($obj !== self::UNLOGGED && $obj !== self::UNAUTHORIZED)
					$publications[] = $obj;
			}
		}
		$tplVar = (!empty($tplVar))? $tplVar : "publicationsList";
		$this->set($tplVar, $publications);
	}

    /**
     * Gets the ID of the first section in the current publication.
     *
     * @return int First section's ID.
     */
    private function getFirstSection() {
        $filter = array('object_type_id' => Configure::read('objectTypes.section.id'));
        $child = $this->BeTree->getChildren($this->publication['id'], $this->getStatus(), $filter, null, true, 1, 1);
        return (empty($child['items'])) ? $this->publication['id'] : $child['items'][0]['id'];
    }

    /**
     * find first active section and load it as home page section
     * if any section was found load publication as home page section
     */
    public function homePage() {
        $homePageSectionId = $this->getFirstSection();
        $this->action = 'section';
        $this->section($homePageSectionId);

        if (file_exists(VIEWS . 'pages' . DS . 'home_page.tpl')) {
            $this->render('home_page');
        }
	}

	/**
	 * prepare an XML containing sitemap specification
	 * view in bedita-app/views/pages/sitemap_xml.tpl
	 */
	public function sitemapXml() {
		$this->sitemap(true);
		$this->layout = null;
		$this->view = 'Smarty';
		$this->ResponseHandler->enabled = false;
		$this->RequestHandler->respondAs('xml');
	}

	/**
	 * build array for sitemap
	 *
	 * @param boolean $xml_out
	 * @return array
	 */
	public function sitemap($xml_out = false) {
		$conf = Configure::getInstance() ;
		$extract_all = (!empty($conf->sitemapAllContent)) ? $conf->sitemapAllContent : false;

		$itemsByType = $this->sectionOptions["itemsByType"];
		$this->sectionOptions["itemsByType"] = false;
		$flatMode = $xml_out? true : false;
		$sectionsTree = $this->loadSectionsTree($conf->frontendAreaId,$extract_all, null, 10000, $flatMode) ;
		$this->sectionOptions["itemsByType"] = $itemsByType;

		if($xml_out) {

			$pubMap = array();
			$pubMap['loc'] = $this->publication["public_url"];
			$pubMap['lastmod'] =  !empty($this->publication['last_modified']) ?
				substr($this->publication["last_modified"], 0, 10) : substr($this->publication["modified"], 0, 10);
			$pubMap['priority'] = !empty($this->publication['map_priority']) ?
				$this->publication['map_priority'] : null;
			$pubMap['changefreq'] = !empty($this->publication['map_changefreq']) ?
				$this->publication['map_changefreq'] : null;
			$urlset[] = $pubMap;
			if($extract_all) {
				$option = array("filter" => array("object_type_id" => Configure::read("objectTypes.leafs.id")),
						"sectionPath" => "");
				$objs = $this->loadSectionObjects($conf->frontendAreaId, $option);
				$resultObjects = (!$this->sectionOptions["itemsByType"] && !empty($objs["childContents"]))? $objs["childContents"] : $objs;
				if(!empty($resultObjects)) {
					$sectionsTree = array_merge($resultObjects, $sectionsTree);
				}
			}

			$i = count($urlset);
			$sectionModel = ClassRegistry::init("Section");
			foreach($sectionsTree as $v) {
				$urlset[$i] = array();
				$urlset[$i]['loc'] = $this->publication["public_url"]. $v['canonicalPath'];

				if($v['object_type_id'] == $conf->objectTypes["section"]["id"]) {

					$secFields = $sectionModel->find("first",
						array("conditions" => array("id"=>$v["id"]), "contain" => array()));
					if(!empty($secFields['last_modified'])) {
						$urlset[$i]['lastmod'] = substr($secFields['last_modified'], 0, 10);
					} else {
						$urlset[$i]['lastmod'] = substr($v["modified"], 0, 10);
					}
					if(!empty($secFields['map_priority'])) {
						$urlset[$i]['priority'] = $secFields['map_priority'];
					}
					if(!empty($secFields['map_changefreq'])) {
						$urlset[$i]['changefreq'] = $secFields['map_changefreq'];
					}

				} else {
					$urlset[$i]['lastmod'] = substr($v["modified"], 0, 10);

				}
				if (isset($v["menu"])) {
					$urlset[$i]['menu'] = $v["menu"];
				}
				$i++;
			}
			$this->set('urlset',$urlset);
		} else {
			if(!in_array('BeTree', $this->helpers)) {
				$this->helpers[] = 'BeTree';
			}
		}

		$this->set('sections_tree',$sectionsTree);
	}


	/**
	 * Publish RSS feed with contents inside section $sectionName
	 * Use callback controller methods (if defined):
	 * 	- $sectionName."RssChannel" to fetch channel data
	 *  - $sectionName."RssItems" to fetch rss items
	 *
	 * If callbacks methods are not defined (default) load section and object data
	 * and build rss data with defaults
	 *
	 * @param string $sectionName, section's nickname/unique name
	 */
	public function rss($sectionName) {

		$channel = array();
		$s = array();
		// fetch channel data, use $sectionName."RssChannel" method if exists
		$methodName = $sectionName . "RssChannel";
		if (method_exists($this, $methodName)) {
			$channel = $this->{$methodName}();
		} else {
			// build channel data from section
			$s = $this->loadObjByNick($sectionName);
			if ($s === self::UNLOGGED || $s === self::UNAUTHORIZED) {
				$this->accessDenied($s);
			}
			if ($s['syndicate'] === "off") {
				throw new BeditaNotFoundException(__("Content not found", true));
			}

			$this->setCanonicalPath($s);
			//App::import("Sanitize");
			$title = h($this->publication["public_name"] . " | " . $s['title']);
			$channel = array( 'title' => $title,
				'link' => $s["canonicalPath"],
				//'description' => Sanitize::html($s['description']),
				'description' => h($s['description']),
				'language' => $s['lang'],
			);
		}
		$this->set('channelData', $channel);

		// fetch rss items, use
		$rssItems = array();
		$methodName = $sectionName . "RssItems";
		// check before filter method
		if (method_exists($this, $methodName)) {
			$rssItems = $this->{$methodName}();
		} else {
			if (empty($s)) { // if channel data has been redefined
				$s = $this->loadObjByNick($sectionName);
				if ($s === self::UNLOGGED || $s === self::UNAUTHORIZED) {
					$this->accessDenied($s);
				}
				$this->setCanonicalPath($s);
			}
			$options = array('dim' => 40);
			$items = $this->loadSectionObjects($s['id'], $options);
			if (!empty($items['childContents'])) {
				foreach ($items['childContents'] as $index => $item) {
					$obj = $this->loadObj($item['id']);
					if ($obj !== self::UNLOGGED && $obj !== self::UNAUTHORIZED) {
						$rssItems[] = $this->buildRssItem($obj, $s['canonicalPath']);
					}
				}
			}
		}
		$this->set('items', $rssItems);

		$this->view = 'View';
		// add RSS helper if not present
		if (!in_array('Rss', $this->helpers)) {
			$this->helpers[] = 'Rss';
		}
		$this->layout = NULL;
		header("Content-type: text/xml; charset=utf-8");
	}

	/**
	 * Build a single RSS item from a BE object array
	 * If section "canonicalPath" is set, links are created with it
	 * If not: use object canonicalPath if present, otherwise object unique name (nickname)
	 *
	 * @param array $obj
	 * @param string $canonicalPath
	 * @return array
	 */
	protected function buildRssItem(array &$obj, $canonicalPath = null) {
		$description = $obj['description'];
		if (!empty($obj['abstract'])) {
			$description .= "<hr/>" .  $obj['abstract'];
		}
		if (!empty($obj['body'])) {
			$description .= "<hr/>" .  $obj['body'];
		}
		$link = !empty($canonicalPath) ? ($canonicalPath ."/". $obj['nickname']) :
			(!empty($obj['canonicalPath']) ? $obj['canonicalPath'] : $obj['nickname']);
		return array( 'title' => $obj['title'], 'description' => $description,
						'pubDate' => $obj['created'], 'link' => $link);
	}


	/**
	 * output a kml defined by a section
	 * @param string $sectionName
	 */
	public function kml($sectionName) {
		$this->section($sectionName);
		$this->layout = 'ajax';
		header("Content-type: application/vnd.google-earth.kml+xml");
		header("Content-Disposition: attachment; filename=" . $sectionName . ".kml");
	}

	/**
	 * output a georss atom representation of section
	 * @param string $sectionName
	 */
	public function georssatom($sectionName) {
		$this->section($sectionName);
		$this->layout = 'ajax';
		header("Content-type: application/atom+xml; charset=utf-8");
	}

	/**
	 * output a georss representation of section
	 * @param string $sectionName
	 */
	public function georss($sectionName) {
		$gml = (!empty($this->params['named']['gml']));
		$this->section($sectionName);
		$s = $this->viewVars["section"];
		$channel = array( 'title' => $this->publication["public_name"] . " - " . $s['title'] ,
			'link' => "/section/".$sectionName,
			'description' => $s['description'],
			'language' => $s['lang'],
		);

		$this->set('channelData', $channel);
		$rssItems = array();
		$items = $s['childContents'];
		if(!empty($items)) {
			foreach($items as $index => $obj) {
				$description = $obj['description'];
				$description .= (!empty($obj['abstract']) && !empty($description))? "<hr/>" .  $obj['abstract'] : $obj['abstract'];
				$description .= (!empty($obj['body']) && !empty($description))? "<hr/>" .  $obj['body'] : $obj['body'];
				if(!empty($obj['GeoTag'][0]['latitude']) && !empty($obj['GeoTag'][0]['longitude'])) {
					$position = $obj['GeoTag'][0]['latitude'] . ' ' . $obj['GeoTag'][0]['longitude'];
					$item = array('item' => array(
						'title' => $obj['title'],
						'description' => $description,
						'pubDate' => $obj['created'],
						'link' => $s['canonicalPath']."/".$obj['nickname']
					));
					if($gml) { // geoRss GML
						$item['georss:where'] = array(
							0 => array(
								'gml:Point' => array(
									0 => array(
										'gml:pos' => $position
									)
								)
							)
						);
					} else { // geoRss simple
						$item['georss:point'] = $position;
					}
					$rssItems[] = $item;
				}
			}
		}
		$this->set('items', array($rssItems));
		$attrib = array(
			"version" => "2.0",
			"xmlns:georss" => "http://www.georss.org/georss",
			"xmlns:gml" => "http://www.opengis.net/gml"
		);
		$this->set('attrib',$attrib);
		$this->view = 'View';
		// add RSS helper if not present
		if(!in_array('Rss', $this->helpers)) {
			$this->helpers[] = 'Rss';
		}
		$this->layout = NULL;
		header("Content-type: application/text+xml; charset=utf-8");
	}

	/**
	 * output a json object of returned array by section or content method
	 * @param string $name
	 * @return string|int $name, nickname or id
	 */
	public function json($name) {
		$this->ResponseHandler->setType('json');
		$this->route($name);
		$this->set('_serialize', 'section');
	}

	/**
	 * output an xml of returned array by section or content method
	 *
	 * passing a "format" named parameters in the url obtain an xml "attributes" format or an xml "tags" format
	 * i.e. http://www.example.com/xml/nickname/format:tags output a tag style xml
	 * default is defined by class attribute xmlFormat
	 *
	 * @param string|int $name, nickname or id
	 */
	public function xml($name) {
		$this->outputXML();
		$this->route($name);
		$this->set(array(
			'_serialize' => 'section',
			'_rootNode' => 'section'
		));
	}

	/**
	 * output an xml of returned array by loadObj/loadObjByNick method
	 *
	 * passing a "format" named parameters in the url obtain an xml "attributes" format or an xml "tags" format
	 * i.e. http://www.example.com/xmlobject/nickname/format:tags output a tag style xml
	 * default is defined by class attribute xmlFormat
	 *
	 * @param string|int $name, nickname or id
	 */
	public function xmlobject($name) {
		$this->outputXML();
		$object = (is_numeric($name))? $this->loadObj($name) : $this->loadObjByNick($name);
		if ($object === self::UNLOGGED || $object === self::UNAUTHORIZED) {
			$this->ResponseHandler->setType('xml');
			$this->accessDenied($object);
		}
		$this->set(array(
			'object' => $object,
			'_serialize' => 'object',
			'_rootNode' => 'object'
		));
	}

	/**
	 * prepare to XML output
	 *
	 */
	private function outputXML() {
		$availableFormat = array('attributes', 'tags');
		if (!empty($this->passedArgs['format']) && in_array($this->passedArgs['format'], $availableFormat)) {
			$format = $this->passedArgs['format'];
		} else {
			$format = $this->xmlFormat;
		}
		$this->ResponseHandler->setType('xml');
		$this->ResponseHandler->xmlFormat = $format;
	}

	/**
	 * Like loadObj using nickname
	 *
	 * @param string $obj_nick
	 * @param boolean $blockAccess see FrontendController::loadObj()
	 * @return array
	 */
	public function loadObjByNick($obj_nick, $blockAccess = true) {
		return $this->loadObj($this->BEObject->getIdFromNickname($obj_nick), $blockAccess);
	}

	/**
	 * Like loadAndSetObj using nickname
	 *
	 * @param string $obj_nick
	 * @param string $var_name view var name
	 * @param boolean $blockAccess see FrontendController::loadObj()
	 * @return array
	 */
	protected function loadAndSetObjByNick($obj_nick, $var_name = null, $blockAccess = true) {
		return $this->loadAndSetObj($this->BEObject->getIdFromNickname($obj_nick) , $var_name, $blockAccess);
	}

	/**
	 * Load bedita Object and set view var with $var_name or object type (e.g. "Document", "Event"..)
	 * Returns object loaded
	 * Throws Exception on errors
	 *
	 * @param int $obj_id
	 * @param string $var_name view var name
	 * @param boolean $blockAccess see FrontendController::loadObj()
	 * @return array
	 */
	protected function loadAndSetObj($obj_id, $var_name = null, $blockAccess = true) {
		$obj = $this->loadObj($obj_id, $blockAccess);
		if ($obj === self::UNLOGGED || $obj == self::UNAUTHORIZED) {
			return $obj;
		}
		$this->set((isset($var_name)? $var_name: $obj['object_type']),$obj);
		return true;
	}

	/**
	 * Returns bedita Object
	 * Throws Exception on errors
	 *
	 * @param int $obj_id
	 * @param boolean $blockAccess
	 *				if it's set a "frontend_access_without_block" permission on the object this param is ignored
	 *					and the object returned (array) will have a key named "authorized" set to true or false
	 *					depending on whether the user has permission to access at the object
	 *				else if it's set a "frontend_access_with_block" permission on the object
	 *					true => if user is unlogged return UNLOGGED constant
	 *							if user is logged and he hasn't permission to access at the object return UNAUTHORIZED constant
	 *					false => if user unlogged dosen't block the action and the object returned (array)
	 *							will have a key named "authorized" set to false
	 *							if user is logged but not authorized the object returned (array)
	 * @param array $options
	 *				a set of options for the method:
	 *				- bindingLevel: the requested model binding level to use
	 *
	 *	note: if FrontendController::showUnauthorized is set to true and the user is logged
	 *			then all unauthorized object will have set "authorized" to false regardless object permission
	 *
	 * @return array object detail
	 */
	public function loadObj($obj_id, $blockAccess=true, $options = array()) {
		if ($obj_id === null) {
			throw new BeditaInternalErrorException(
				__('Missing object id', true),
				'FrontendController::loadObj() require an object id'
			);
		}

		// use object cache
		if(isset($this->objectCache[$obj_id])) {
			$modelType = $this->objectCache[$obj_id]["object_type"];
			if (!empty($options['bindingLevel'])) {
				$bindings = $this->setObjectBindings($modelType, $options['bindingLevel']);
			} else {
				$bindings = $this->setObjectBindings($modelType);
			}
			$bindingsDiff = array_diff($this->objectCache[$obj_id]["bindings"]["bindings_list"], $bindings["bindings_list"]);
			// cached object is used only if its bindings contain more data or equal than those of the request
			if (!empty($bindingsDiff) || ($this->objectCache[$obj_id]["bindings"]["bindings_list"] == $bindings["bindings_list"])) {
				return $this->objectCache[$obj_id];
			}
		}

		// check permissions and set $authorized true/false
		if (!$this->skipCheck) {

			// get permissions set on this object
			$permissionModel = ClassRegistry::init("Permission");
			$perms = $permissionModel->isPermissionSet($obj_id, array(
				Configure::read("objectPermissions.frontend_access_with_block"),
				Configure::read("objectPermissions.frontend_access_without_block")
			));


			// authorization defaults to false
			$authorized = false;

			if (!$perms) {

				// even with check no perms found, set auth true
				$authorized = true;
				$freeAccess = true;

			} else {
				// perms are set (no free object)
				$freeAccess = false;

				// divide perms by type (blocking or not)
				$permsWithBlock = array();
				$permsWithoutBlock = array();
				foreach ($perms as $p) {
					if ($p["Permission"]["flag"] == Configure::read("objectPermissions.frontend_access_without_block")) {
						$permsWithoutBlock[] = $p;
					} else {
						$permsWithBlock[] = $p;
					}
				}


				// if user is not logged
				if (!$this->logged) {
					if (!empty($permsWithBlock)) {
						if (!$this->showUnauthorized) {
							if($blockAccess) {
								return self::UNLOGGED;
							}
						}
					}
				} else {
					if ($permissionModel->checkPermissionByUser($perms, $this->BeAuth->user)) {
						$authorized = true;
					} else {
						if (!empty($permsWithBlock)) {
							if (!$this->showUnauthorized) {
								if($blockAccess) {
									return self::UNAUTHORIZED;
								}
							}
						}
					}
				}
			}

		} else {
			$authorized = true;
			$freeAccess = true;
		}

		if (!isset($this->objectCache[$obj_id])) {
			$modelType = $this->BEObject->getType($obj_id);
			if (!empty($options['bindingLevel'])) {
				$bindings = $this->setObjectBindings($modelType, $options['bindingLevel']);
			} else {
				$bindings = $this->setObjectBindings($modelType);
			}
		}

        $obj = null;
        if ($this->BeObjectCache) {
            $obj = $this->BeObjectCache->read($obj_id, $bindings);
        }

        if (empty($obj)) {
    		$obj = $this->{$modelType}->find("first", array(
    								"conditions" => array(
    									"BEObject.id" => $obj_id,
    									"BEObject.status" => $this->status
    									)
    								)
    							);
    		
    		if (empty($obj)) {
    			throw new BeditaNotFoundException(__("Content not found", true) . ' id: ' . $obj_id);
    		}
    		// #304 status filter for Category and Tag
    		if(!empty($obj['Category'])) {
    			$cc = array();
    			foreach($obj['Category'] as $k => $v) {
    				if(in_array($v['status'],$this->status)) {
    					$cc[] = $v;
    				}
    			}
    			unset($obj['Category']);
    			$obj['Category'] = $cc;
    		}
    		if(!empty($obj['Tag'])) {
    			$tt = array();
    			foreach($obj['Tag'] as $k => $v) {
    				if(in_array($v['status'],$this->status)) {
    					$tt[] = $v;
    				}
    			}
    			unset($obj['Tag']);
    			$obj['Tag'] = $tt;
    		}

    		$obj["publication_date"] = (!empty($obj["start_date"]))? $obj["start_date"] : $obj["created"];

    		if ($this->BeObjectCache) {
    		    $this->BeObjectCache->write($obj_id, $bindings, $obj);
    		}
        }

        if (!$this->checkPubblicationDate($obj)) {
			throw new BeditaNotFoundException(__("Content not found", true) . ' id: ' . $obj_id);
		}

		$this->BeLangText->setObjectLang($obj, $this->currLang, $this->status);

		if(!empty($obj["RelatedObject"])) {
			$userdata = (!$this->logged) ? array() : $this->Session->read($this->BeAuth->sessionKey);
			$relOptions = array("mainLanguage" => $this->currLang, "user" => $userdata);
			$obj['relations'] = $this->objectRelationArray($obj['RelatedObject'], $this->status, $relOptions);

			unset($obj['RelatedObject']);
			$obj['relations_count'] = array();
			$secondaryRel = Configure::read('frontendSecondaryRelations');
			foreach ($obj['relations'] as $k=>$v) {
				$obj['relations_count'][$k] = count($v);
			    // load secondary relations
			    if (!empty($secondaryRel) && !empty($secondaryRel[$k])) {
			        foreach ($obj['relations'][$k] as &$related) {
                        $secondaryObj = array();
			            if (!empty($related['RelatedObject'])) {
			                foreach ($related['RelatedObject'] as $secondRelated) {
			                    if (in_array($secondRelated['switch'], $secondaryRel[$k])) {
			                        $secondaryObj[] = $secondRelated;
			                    }
			                }
			                if (!empty($secondaryObj)) {
			                    $related['relations'] = $this->objectRelationArray($secondaryObj, $this->status, $relOptions);
			                }
			            }
			        }
			    }
			}

			// if not empty attach relations check if attached object have 'mediamap' relations
			// if so explicit mediamap objects
/*			if (!empty($obj['relations']['attach'])) {
				foreach ($obj['relations']['attach'] as &$attach) {
					$mediamap = array();
					if (!empty($attach['RelatedObject'])) {
						foreach ($attach['RelatedObject'] as $relObj) {
							if ($relObj['switch'] == 'mediamap') {
								$mediamap[] = $relObj;
							}
						}
						$attach['relations'] = $this->objectRelationArray($mediamap, $this->status, $relOptions);
					}
				}
			}
*/		}
		
		if (!empty($obj['Annotation'])) {
			$this->setupAnnotations($obj, $this->status);
		}
		unset($obj['Annotation']);

		/**
		 * @deprecated block. Kept for backward compatibility with 3.1 version
		 * use "customProperties" for a more readable array of custom properties
		 **/
		if (!empty($obj['ObjectProperty'])) {
			foreach ($obj['ObjectProperty'] as $prop) {
				$properties[$prop["name"]] = $prop;
			}
			$obj['ObjectProperty'] = $properties;
		}
		/**
		 * end @deprecated block
		 **/

		$obj['object_type'] = $modelType;
		$obj['authorized'] = $authorized;
		// object with/without permission set on it
		$obj['free_access'] = $freeAccess;

		// add bindings used
		$obj['bindings'] = $bindings;

		$this->objectCache[$obj_id] = $obj;
		return $obj;
	}

	/**
	 * Load objects in section $parent_id and set in view vars an array for each object type
	 * (e.g. in view you will have
	 * 		$Document => array(0 => ..., 1 => ...)
	 * 		$Event"  => array(0 => ..., 1 => ...)
	 * )
	 *
	 * @param int $parent_id
	 * @param array $options, filter and pagination options
	 */
	protected function loadAndSetSectionObjects($parent_id, $options=array()) {
		$sectionItems = $this->loadSectionObjects($parent_id);
		foreach($sectionItems as $key => $objs) {
			$this->set($key, $objs);
		}
	}

	/**
	 * Load objects in section $parentNick and set in view vars an array for each object type
	 *
	 * @param string $parentNick
	 * @param array $options, filter and pagination options
	 */
	protected function loadAndSetSectionObjectsByNick($parentNick, $options=array()) {
		$sectionItems = $this->loadSectionObjectsByNick($parentNick, $options);
		foreach($sectionItems as $key => $objs) {
			$this->set($key, $objs);
		}
	}

	/**
	 * Load objects in section $parentNick
	 *
	 * @param string $parentNick
	 * @param array $options, filter and pagination options
	 *
	 * @return array
	 */
	public function loadSectionObjectsByNick($parentNick, $options=array()) {
		return $this->loadSectionObjects($this->BEObject->getIdFromNickname($parentNick), $options);
	}

	/**
	 * Load objects in section $parent_id
	 *
	 * @param int $parent_id
	 * @param array $options, filter, pagination and other options
	 *
	 * @return array
	 */
	public function loadSectionObjects($parent_id, $options=array()) {

		if (empty($parent_id)) {
			throw new BeditaInternalErrorException(
				__('Missing parent_id', true),
				'FrontendController::loadSectionObjects() requires a parent id'
			);
		}

		$this->checkParentStatus($parent_id);
		if(isset($this->objectCache[$parent_id]["menu"])){
			$menu = $this->objectCache[$parent_id]["menu"];
			$priorityOrder = $this->objectCache[$parent_id]["priority_order"];
		} else {
			$menu = $this->Tree->field("menu", array("id" => $parent_id));
			$priorityOrder = $this->Section->field("priority_order", array("id" => $parent_id));
			if(isset($this->objectCache[$parent_id])) {
				$this->objectCache[$parent_id]["menu"] = $menu;
				$this->objectCache[$parent_id]["priority_order"] = $priorityOrder;
			}
		}
		$findAltPath = ($menu === '0');
		if(empty($priorityOrder)) {
			$priorityOrder = "asc";
		}
		$sectionItems = array();

		$filter = (!empty($options["filter"]))? $options["filter"] : false;
		$order = (!empty($options["order"]))? $options["order"] : "priority";
		$dir = (isset($options["dir"]))? $options["dir"] : ($priorityOrder == "asc");
		$page = (!empty($options["page"]))? $options["page"] : 1;
		$dim = (!empty($options["dim"]))? $options["dim"] : null;

		$s = $this->BEObject->getStartQuote();
		$e = $this->BEObject->getEndQuote();
		// add rules for start and end pubblication date
		if ($this->checkPubDate['start'] == true && empty($filter['Content.start_date'])) {
			$filter['Content.*'] = '';
			$filter['AND'][] = array(
				'OR' => array(
					'Content.start_date <=' => date('Y-m-d'),
					'Content.start_date' => null
				)
			);
		}
		if ($this->checkPubDate['end'] == true && empty($filter['Content.end_date'])) {
			$filter['Content.*'] = '';
			$filter['AND'][] = array(
				'OR' => array(
					'Content.end_date >=' => date('Y-m-d'),
					'Content.end_date' => null
				)
			);
		}

        $items = null;
        $cacheOpts = array();
        if ($this->BeObjectCache) {
            $cacheOpts = array($parent_id, $this->status, $filter, $order, $dir, $page, $dim);
            $items = $this->BeObjectCache->read($parent_id, $cacheOpts, 'children');
        }
        
        if (empty($items)) {
            $items = $this->BeTree->getChildren($parent_id, $this->status, $filter, $order, $dir, $page, $dim);
            if ($this->BeObjectCache) {
                $this->BeObjectCache->write($parent_id, $cacheOpts, $items, 'children');
            }
        }

		if(!empty($items) && !empty($items['items'])) {
			foreach($items['items'] as $index => $item) {
				$obj = $this->loadObj($item['id']);
				if ($obj !== self::UNAUTHORIZED && $obj !== self::UNLOGGED) {
					if(empty($obj["canonicalPath"])) {
						if(empty($options["sectionPath"])) {
							if($findAltPath) {
								$this->setCanonicalPath($obj);
							} else {
								$s = $this->loadObj($parent_id);
								if ($s === self::UNAUTHORIZED || $s === self::UNLOGGED) {
									return array();
								}
								$this->setCanonicalPath($s);
								$obj["canonicalPath"] = (($s["canonicalPath"] != "/") ? $s["canonicalPath"] : "")
									. "/" . $obj["nickname"];
							}
						} else {
							$obj["canonicalPath"] = (($options["sectionPath"] != "/") ? $options["sectionPath"] : "")
								. "/" . $obj["nickname"];
						}
					}
					if (isset($options["setAuthorizedTo"])) {
						$obj["authorized"] = $options["setAuthorizedTo"];
					}
					if ($this->sectionOptions["itemsByType"]) {
						$sectionItems[$obj['object_type']][] = $obj;
					} else {
						if ($obj["object_type"] == Configure::read("objectTypes.section.model"))
							$sectionItems["childSections"][] = $obj;
						else
							$sectionItems["childContents"][] = $obj;
					}
				}
			}
			$sectionItems["toolbar"] = $items['toolbar'];
		}
		return $sectionItems;

	}

	/**
	 * find first section that contain content ($name) then call section method
	 *
	 * @param string|id $name, id or content nickname
	 */
	public function content($name) {
		if (empty($name)) {
			throw new BeditaBadRequestException(
				__('Missing content unique name or id', true),
				'FrontendController::content() requires unique name or id'
			);
		}

		$content_id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);

		// if it's defined frontend publication id then search content inside that publication else in all BEdita
		$publicationId = (!empty($this->publication["id"]))? $this->publication['id'] : null;
		$section_id = $this->Tree->getParent($content_id, $publicationId, $this->status);


		if ($section_id === false) {
			throw new BeditaNotFoundException(__('Content not found', true) . ' $name: ' . $name);
		}

		// if content has more parent get the first one found
		if (is_array($section_id)) {
			$section_id = array_shift($section_id);
		}

		$this->action = 'section';
		$this->section($section_id, $content_id);
	}


	/**
	 * find section and contents from section nick or section id and set template vars
	 *
	 * Set section and:
	 * if $contentName=null set all contents in section
	 * if $contentName is defined set single content
	 * if $contentName is defined and $this->showAllContents=true set content and other contents too (default)
	 *
	 * Execute 'sectionNickname'BeforeFilter and/or 'sectionNickName'BeforeRender
	 * if they're set in the controller (i.e. pages_controller.php)
	 *
	 * @param string/int $secName: section nick or section id
	 * @param string/int $contentName: content nick or content id
	 */
	public function section($secName, $contentName = null) {

		if (is_numeric($secName)) {
			$sectionId = $secName;
			$secName = $this->BEObject->getNicknameFromId($sectionId);
		} else {
			$sectionId = $this->BEObject->getIdFromNickname($secName);
		}

		$content_id = null;

		if (!empty($contentName)) {
			if (is_numeric($contentName)) {
				$content_id = $contentName;
				$contentName = $this->BEObject->getNicknameFromId($content_id);
			} else {
				$content_id = $this->BEObject->getIdFromNickname($contentName);
			}
			$contentType = $this->BEObject->getType($content_id);
			if ($contentType === "Section") {
				$args = func_get_args();
				array_shift($args);
				return call_user_func_array(array($this, "section"), $args);
			// check that contentName is a child of secName
			} elseif ( $this->Tree->find('count',array("conditions" => array("id" => $content_id, "parent_id" => $sectionId))) == 0 ) {
				throw new BeditaNotFoundException(
					__("Content " . $contentName . " doesn't belong to " . $secName, true)
				);
			}
			$contentNameFilter = BeLib::getInstance()->variableFromNickname($contentName);
		}

		$secNameFilter = BeLib::getInstance()->variableFromNickname($secName);
		// section before filter
		if (method_exists($this, $secNameFilter . "BeforeFilter")) {
			$this->{$secNameFilter . "BeforeFilter"}($contentName);
		}

		// content before filter
		if ($contentName && method_exists($this, $contentNameFilter . "BeforeFilter")) {
			$this->{$contentNameFilter . "BeforeFilter"}($secName);
		}

		$section = $this->loadObj($sectionId);
		if ($section === self::UNLOGGED || $section === self::UNAUTHORIZED) {
			$this->accessDenied($section);
		}

		$this->setCanonicalPath($section);
		$this->sectionOptions["childrenParams"] = array_merge($this->sectionOptions["childrenParams"], $this->params["named"]);
		if (!isset($section["menu"]) || $section["menu"] !== "0") {
			$this->sectionOptions["childrenParams"]["sectionPath"] = $section["canonicalPath"];
		}

		if (!$section["parentAuthorized"] || !$section["authorized"]) {
			$section["authorized"] = false;
			$this->sectionOptions["childrenParams"]["setAuthorizedTo"] = false;
		}

		$urlFilter = $this->SessionFilter->getFromUrl();
		if ($urlFilter) {
			$this->SessionFilter->arrange($urlFilter);
			if (empty($this->sectionOptions['childrenParams']['filter'])) {
				$this->sectionOptions['childrenParams']['filter'] = $urlFilter;
			} else {
				$this->sectionOptions['childrenParams']['filter'] = array($this->sectionOptions['childrenParams']['filter'], $urlFilter);
			}
		}

		if (!empty($content_id)) {
			$section['currentContent'] = $this->loadObj($content_id);
			if ($section['currentContent'] === self::UNLOGGED || $section['currentContent'] === self::UNAUTHORIZED) {
				$this->accessDenied($section['currentContent']);
			}

			$section["contentRequested"] = true;
			$section["contentPath"] = ($section["canonicalPath"] !== "/") ? $section["canonicalPath"] : "";
			if (empty($section['currentContent']['canonicalPath'])) {
				$section['currentContent']['canonicalPath'] = $section["contentPath"] .= "/" . $section['currentContent']['nickname'];
			}
			$this->historyItem["object_id"] = $content_id;
			if (!empty($section['currentContent']['title'])) {
				$this->historyItem["title"] = $section['currentContent']['title'];
			} else {
				$this->historyItem["title"] = $section['title'];
			}


			if ($this->sectionOptions["showAllContents"]) {
				if (empty($this->sectionOptions["childrenParams"]["detailed"])
					|| $this->sectionOptions["childrenParams"]["detailed"] === false) {
					$this->baseLevel = true;
				}
				$checkPubDate = $this->checkPubDate;
				$this->checkPubDate = array("start" => false, "end" => false);
				$tmp = $this->loadSectionObjects($sectionId, $this->sectionOptions["childrenParams"]);
				if (!$this->sectionOptions["itemsByType"]) {
					$section = array_merge($section, $tmp);
				} else {
					$section = array_merge($section, array("children" => $tmp));
				}

				$this->baseLevel = false;
				$this->checkPubDate = $checkPubDate;
			}
		} else {
			$tmp = $this->loadSectionObjects($sectionId, $this->sectionOptions["childrenParams"]);

			if (!$this->sectionOptions["itemsByType"]) {
				$tmp['currentContent'] = (!empty($tmp['childContents']))? $tmp['childContents'][0] : array();
				$section = array_merge($section, $tmp);
			} else {
				if (empty($tmp)) {
					$section = array_merge($section, array("currentContent" => array(), "children" => array()));
				} else {
					$toolbar = $tmp["toolbar"];
					unset($tmp["toolbar"]);
					$current = current($tmp);
					$section = array_merge($section, array("currentContent" => $current[0], "children" => $tmp, "toolbar" => $toolbar));
				}
			}

			$this->historyItem["object_id"] = $sectionId;
			$this->historyItem["title"] = $section['title'];
		}

		$this->set('section', $section);

		// section before render
		if (method_exists($this, $secNameFilter . "BeforeRender")) {
			$this->{$secNameFilter . "BeforeRender"}();
		}

		// content before render
		if ($contentName && method_exists($this, $contentNameFilter . "BeforeRender")) {
			$this->{$contentNameFilter . "BeforeRender"}();
		}
	}

	/**
	 * Set section canonical path and set parent array in $section array
	 *
	 * @param array $section
	 * @param int $sectionId
	 * @return boolean false if some parent sections is unauthorized for user
	 */
/*
	protected function setSectionPath(array &$section, $sectionId) {
		$section["pathSection"] = $this->getPath($sectionId);
		$sectionPath = "";
		$parentAuthorized = true;
		foreach ($section["pathSection"] as $ps) {
			if ($parentAuthorized && !empty($ps["authorized"]) && !$ps["authorized"]) {
				$parentAuthorized = false;
			}
		}
		if($section["object_type_id"] == Configure::read("objectTypes.area.id")) {
			$currPath = "/";
		} else {
			$currPath = (!empty($section["menu"]) && $section["menu"] === '0') ? "" : "/" . $section["nickname"];
		}
		$parentPath = "";
		if(!empty($section["pathSection"])) {
			$parentSec = end($section["pathSection"]);
			$parentPath = !empty($parentSec['canonicalPath']) ? $parentSec['canonicalPath'] : "";
		}
		$section["canonicalPath"] = $parentPath . $currPath;
		return $parentAuthorized;
	}
*/

	/**
	 * route to section, content or another method following the below rules
	 *
	 * 1. if there aren't url arguments (i.e. /) => uses homePage reserved word
	 * 2. if first url argument is a reserved words defined in configuration var 'defaultReservedWords'
	 *	  and 'cfgReservedWords' => try to call the method itself
	 * 3. if first url argument is a method of current controller => try to call the method itself
	 * 4. if first url argugment is a valid nickname
	 *    and there is a method of current Controller with the name defined in BeLib::variableFromNickname => try to call the method itself
	 *    example: www.example.com/my-nickname => calls PagesController::myNickname() method if it exists
	 * 5. if first url argument is a valid nickname => calls the appropriate FrontendController::section() or FrontendController::content() method
	 * 6. throw exception and 404 http error
	 * @throws BeditaBadRequestException, BeditaNotFoundException
	 */
	public function route() {
		$args = func_get_args();
		if(count($args) === 0 || empty($args[0])) {
			 $args[0] = "homePage";
		}
		if($args[0] === "pages") {
			array_shift($args);
		}
		
		$name = $args[0];

		// generic methodName
		$methodName = str_replace(".", "_", $name); // example: sitemap.xml => sitemap_xml
		$methodName = BeLib::getInstance()->variableFromNickname($methodName);
		// #396 controller protected methods -> make them public, avoid direct call from url
		if (in_array($methodName, Configure::read("defaultReservedMethods"))) {
		    throw new BeditaBadRequestException(__("Reserved method called from url", true));
		}

		$reflectionClass = new ReflectionClass($this);

		$id = (is_numeric($name))? $name : $this->BEObject->getIdFromNickname($name,$this->status);

		// setup args: look if $name is reserved
		if (in_array($name, Configure::read("defaultReservedWords")) || in_array($name, Configure::read("cfgReservedWords"))) {
			// load object with nickname $methodName if exists
			if(!empty($id)) {
				$this->loadAndSetObj($id, "object");
			}
			array_shift($args);
		} else {
			$currentClassName = $reflectionClass->getName();
			$methods = array($name, $methodName);

			// try to use current Controller method (PagesController::$name or PagesController::$methodName)
			while (count($methods) > 0) {
				$m = array_shift($methods);
				try {
					// check method belongs to PagesController to avoid to call AppController, FrontendController public methods
					$methodClassName = $reflectionClass->getMethod($m)->class;
					if ($currentClassName == $methodClassName) {
						array_shift($args);
						$methodName = $m;
						if (is_string($name) && !empty($id)) {
							// load object with nickname $name if exists
							if(!empty($id)) {
								$this->loadAndSetObj($id, "object");
							}
						}
						$methods = array();
					}
				} catch (ReflectionException $ex) {
				 	$methodName = null;
				}
			}

			// try to use self::section or self::content methods
			if ($methodName === null && is_string($name) && !empty($id)) {
				$object_type_id = $this->BEObject->findObjectTypeId($id);
				if ($object_type_id == Configure::read("objectTypes.section.id") || $object_type_id == Configure::read("objectTypes.area.id")) {
					$methodName = "section";
				} else {
					$methodName = "content";
				}
			}
		}

		$this->action = $methodName;

		if (Configure::read('enableSessionFilter')) {
			$this->SessionFilter->setup();
		}

		try {
			// check before filter method
			if ($reflectionClass->hasMethod($methodName . "BeforeFilter")) {
				$this->{$methodName . "BeforeFilter"}();
			}
			// call method
			$reflectionMethod = $reflectionClass->getMethod($methodName);
			$reflectionMethod->invokeArgs($this, $args);
			// check before render method
			if ($reflectionClass->hasMethod($methodName . "BeforeRender")) {
				$this->{$methodName . "BeforeRender"}();
			}
		} catch (ReflectionException $ex) {
			// launch 404 error
			throw new BeditaNotFoundException(__("Content not found", true), $ex->getMessage());
		}

	}

	/**
	 * search inside history
	 */
	public function search() {
		$this->historyItem = null;
		if(!in_array('BeToolbar', $this->helpers)) {
       		$this->helpers[] = 'BeToolbar';
		}
		$this->searchOptions = array_merge($this->searchOptions, $this->params['named']);
		$s = $this->BEObject->getStartQuote();
		$e = $this->BEObject->getEndQuote();
		// add rules for start and end pubblication date
		if ($this->checkPubDate['start'] == true && empty($this->searchOptions['filter']['Content.start_date'])) {
			$this->searchOptions['filter']['Content.*'] = '';
			$this->searchOptions['filter']['AND'][] = array(
				'OR' => array(
					'Content.start_date <=' => date('Y-m-d'),
					'Content.start_date' => null
				)
			);
		}
		if ($this->checkPubDate['end'] == true && empty($this->searchOptions['filter']['Content.end_date'])) {
			$this->searchOptions['filter']['Content.*'] = '';
			$this->searchOptions['filter']['AND'][] = array(
				'OR' => array(
					'Content.end_date >=' => date('Y-m-d'),
					'Content.end_date' => null
				)
			);
		}
		$searchFilter = array();
		if (!empty($this->params['form']['searchstring'])) {
			$searchFilter['query'] = $this->params['form']['searchstring'];
			$this->SessionFilter->arrange($searchFilter);
			$this->set('stringSearched', $searchFilter['query']);
			$this->params['named']['query'] = urlencode($searchFilter['query']);
		} else {
			$searchFilter = $this->SessionFilter->getFromUrl();
			$this->SessionFilter->arrange($searchFilter);
			$this->set('stringSearched', $searchFilter['query']);
		}
		$filter = array_merge($this->searchOptions['filter'], $searchFilter);
		$result = $this->BeTree->getDescendants($this->publication['id'], $this->status, $filter, $this->searchOptions['order'], $this->searchOptions['dir'], $this->searchOptions['page'], $this->searchOptions['dim']);
		$this->set('searchResult', $result);
	}

	/**
	 * public subscribe page, used for newsletter/frontend subscribe/unsubscribe
	 *
	 * @param string $what
	 */
	public function subscribe($what="newsletter") {
		$this->historyItem = null;
		if ($what == "newsletter") {
			$mailGroupModel = ClassRegistry::init("MailGroup");
			$mailgroups = $mailGroupModel->find("all", array(
						"conditions" => array(
							"area_id" => $this->publication["id"],
							"visible" => 1
						),
						"contain" => array()
					)
				);
			$this->set("mailgroups", $mailgroups);
		}
		$this->set("what", $what);
	}

	/**
	 * manage hash request like newsletter/frontend subscribe/unsubscribe
	 *
	 * @param string $service_type
	 * @param string $hash
	 * @return void
	 * @throws BeditaInternalErrorException
	 */
	public function hashjob($service_type=null, $hash=null) {
		try {
			$this->Transaction->begin();
			$this->BeHash->handleHash($service_type, $hash);
			$this->Transaction->commit();
		} catch (BeditaHashException $ex) {
			$this->Transaction->rollback();
			$this->userErrorMessage($ex->getMessage());
			$this->eventError($ex->getDetails());
		} catch (BeditaException $ex) {
			$this->Transaction->rollback();
			throw new BeditaInternalErrorException($ex->getMessage(), $ex->getDetails());
		}
	}

	/**
	 * find parent path of $object_id (excluded publication)
	 *
	 * @param int $object_id
	 * @return array (the keys are object's id)
	 */
	protected function getPath($object_id) {
		$pathArr = array();
		$path = "";
		if(isset($this->objectCache[$object_id]["parent_path"])){
			$path = $this->objectCache[$object_id]["parent_path"];
		} else {
			$row = $this->Tree->find("first", array(
				"conditions" => array("id" => $object_id, "area_id" => $this->publication["id"]),
				"limit" => 1,
				"order" => array("menu" => "desc", "parent_path" => "desc")
			));
			if (!empty($row["Tree"]["parent_path"])) {
				$path = $row["Tree"]["parent_path"];
				if(!empty($this->objectCache[$object_id])) {
					$this->objectCache[$object_id] = array_merge($this->objectCache[$object_id], $row["Tree"]);
				}
			}
		}
		$parents = explode("/", trim($path,"/"));
		if (!empty($parents[0])) {
			if($parents[0] != $this->publication["id"]) {
				throw new BeditaNotFoundException("Wrong publication: " . $parents[0]);
			}
			$oldSectionBindings = null;
			if(!empty($this->modelBindings["Section"])) {
				$oldSectionBindings = $this->modelBindings["Section"];
			}
			$this->modelBindings["Section"] = array("BEObject" => array("LangText", "ObjectProperty"), "Tree");
			$currPath = "";
			$parentPath = "";
			foreach ($parents as $p) {
				if ($p != $this->publication["id"]) {
					if(isset($this->objectCache[$p])) {
						$pathArr[$p] = $this->objectCache[$p];
					} else {
						$pathArr[$p] = $this->loadObj($p);
					}
                    if ($pathArr[$p] === self::UNLOGGED || $pathArr[$p] === self::UNAUTHORIZED) {
                            $this->log('Error getting parent data in getPath() - id: ' . $object_id . ' parent id: ' . $p . ' - ' . $this->BeAuth->userid());
                            $this->accessDenied($pathArr[$p]);
                    } else if (!empty($pathArr[$p]['canonicalPath'])) {
                        $currPath = $pathArr[$p]['canonicalPath'];
                    } else {
						if($pathArr[$p]['menu'] !== '0') {
							$currPath .= (($currPath === "/") ? "" : "/") . $pathArr[$p]['nickname'];
						}
						$pathArr[$p]['canonicalPath'] = empty($currPath) ? "/" : $currPath;
						$this->objectCache[$p]['canonicalPath'] = $pathArr[$p]['canonicalPath'];
					}
				}
			}

			if(empty($oldSectionBindings)) {
				unset($this->modelBindings["Section"]);
			} else {
				$this->modelBindings["Section"] = $oldSectionBindings;
			}
		}
		return $pathArr;
	}

	/**
	 * get array of parents that contain the object specified by $object_id
	 *
	 * @param integer $object_id
	 * @return array
	 */
	protected function getParentsObject($object_id) {
		$parents_id = $this->BeTree->getParents($object_id, $this->publication["id"], $this->status);
		$parents = array();
		foreach ($parents_id as $id) {
			$parents[] = $this->loadObj($id, false);
		}
		return $parents;
	}

	/**
	 * build archive tree
	 *
	 * Array(
	 * 		"Document" => Array(
	 * 				"2008" => Array(
	 * 					"01" => Array(
	 * 						0 => document,
	 * 						1 => document,
	 * 						...
	 * 						"monthName" => month name
	 * 						"total" => number of document in january
	 * 						),
	 *	 				"02" => Array(...),
	 * 					....
	 * 					"total" => numeber of document in 2008
	 * 				),
	 * 				"2007" => Array(...),
	 * 		"ShortNews" => ....
	 * 		)
	 *
	 * @param string $secName section id or section nickname
	 * @return array
	 */
	protected function loadArchiveTree($secName, $options=array()) {

		$section_id = (is_numeric($secName))? $secName : $this->BEObject->getIdFromNickname($secName);

		$monthName = array("01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May",
						   "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October",
						   "11" => "November", "12" => "December");

		$this->modelBindings['Document'] = array("BEObject" => array("LangText"));
		$this->modelBindings['ShortNews'] = array("BEObject" => array("LangText"));
		$this->modelBindings['Event'] = array("BEObject" => array("LangText"),"DateItem");

		$oldItemsByType = $this->sectionOptions['itemsByType'];
		$this->sectionOptions['itemsByType'] = true;
		$items = $this->loadSectionObjects($section_id,$options);
		unset($this->modelBindings);
		$this->sectionOptions['itemsByType'] = $oldItemsByType;

		$archive = array();

		foreach ($items as $type => $itemGroup) {

			if($type != "toolbar") {

				foreach ($itemGroup as $item) {

					// DateItem, pubblication or creation date
					if(!empty($item["DateItem"][0]["start_date"]))
						$refDate = $item["DateItem"][0]["start_date"];
					else
						$refDate = isset($item["start_date"])? $item["start_date"] : $item["created"];

					$data = explode("-", $refDate);
					$year = $data[0];
					$id = $item["id"];
					$item["title"] = (!empty($item["LangText"]["title"][$this->currLang]))? $item["LangText"]["title"][$this->currLang] : $item["title"];
					$archive[$type][$year][$data[1]][] = $item;
				}

				// sort archive
				$sortFunction = "ksort";
				if (!empty($options["archiveSort"]) && $options["archiveSort"] == "desc")
					$sortFunction = "krsort";

				$sortFunction($archive[$type]);
				foreach ($archive[$type] as $year => $month) {
					$sortFunction($archive[$type][$year]);
				}

				// add number of items for month and year
				$countYear = 0;
				foreach ($archive[$type] as $year => $month) {

					$countYear = 0;
					foreach ($month as $key => $i) {
						$countItem = count($i);
						$countYear += $countItem;
						$archive[$type][$year][$key]["total"] = $countItem;
						$archive[$type][$year][$key]["monthName"] = __($monthName[$key],true);
					}
					$archive[$type][$year]["total"] = $countYear;
				}
			}
		}

		return $archive;
	}


	/**
	 * load all tag
	 *
	 * @param string $tplVar
	 * @param boolean $cloud, if true set 'class' key
	 * 			(possible value: smallestTag, largestTag, largeTag, mediumTag, smallTag)
	 * @param boolean $shuffle, if true shuffle the tags else order by label
	 * @param int $tagShowed, define how much tags have to be returned (null = all tags)
	 */
	public function loadTags($tplVar=null, $cloud=true, $shuffle=false, $tagShowed=null) {
		$tplVar = (empty($tplVar))? "listTags" : $tplVar;
		$category = ClassRegistry::init("Category");
		$tags = $category->getTags(array(
			"showOrphans" => false,
			"status" => $this->status,
			"cloud" => $cloud,
			"area_id" => $this->publication["id"]
		));
		if ($shuffle) {
			shuffle($tags);
		}
		if (!empty($tagShowed)) {
			$tags = array_slice($tags,0,$tagShowed);
		}
		$this->set($tplVar, $tags);
	}

	/**
	 * find all objects tagged by $name and set results for view
	 *
	 * @param string $name
	 */
	public function tag($name) {
		$this->set("tag",$this->loadObjectsByTag($name));
	}

	/**
	 * find all objects for category $name and set results for view
	 *
	 * @param string $name
	 */
	public function category($name) {
		$this->set("category",$this->loadObjectsByCategory($name));
	}

	/**
	 * return objects for a specific category
	 *
	 * @param string $category category name (friendly url/unique name)
	 * @param array $options search options
	 * 				"section" => name or id section
	 * 				"filter" => particular filter
	 * 				"order", "dir", "dim", "page" used like pagination parameters,
	 *				"baseLevel" => true to use $this->baseLevel = true for model bindings
	 * @return array
	 */
	protected function loadObjectsByCategory($categoryName, $options=array()) {
		return $this->loadObjectsByTagCategory($categoryName, $options, "category");
	}


	/**
	 * Internal method for loadObjectsByCategory loadObjectsByTag
	 *
	 * @param string $name category/tag name (friendly url/unique name)
	 * @param array $options search options (see loadObjectsByCategory)
	 * @param string $type, "tag" (default), or "category"
	 * @return array
	 * @throws BeditaNotFoundException, BeditaBadRequestException
	 */
	private function loadObjectsByTagCategory($name, $options=array(), $type = "tag") {
		$section_id = null;
		if (!empty($options["section"])) {
			$section_id = (is_numeric($options["section"]))? $options["section"] : $this->BEObject->getIdFromNickname($options["section"]);
			$this->checkParentStatus($section_id);
			$searchMethod = "getChildren";
		} else {
			$section_id = $this->publication["id"];
			$searchMethod = "getDescendants";
		}

		$filter = (!empty($options["filter"]))? $options["filter"] : false;
		if ($type === "tag") {
			$detail = ClassRegistry::init("Category")->find("first", array(
						"conditions" => array("name" => $name, "object_type_id IS NULL", "status" => $this->status)
					)
				);

			if (empty($detail)) {
				throw new BeditaNotFoundException(__("No tag found", true). " - $name");
			}

			$options = array_merge($this->tagOptions, $options, $this->params["named"]);
			$filter["tag"] = $name;

		} elseif ($type === "category"){

			$detail = ClassRegistry::init("Category")->find("first", array(
						"conditions" => array("name" => $name, "object_type_id IS NOT NULL", "status" => $this->status)
					)
				);

			if (empty($detail)) {
				throw new BeditaNotFoundException(__("No category found", true) . " - $name");
			}

			$options = array_merge($this->tagOptions, $options, $this->params["named"]);
			$filter["category"] = $name;

		} else {
			throw new BeditaBadRequestException(__("Unsupported type", true). " - $type");
		}

		$s = $this->BEObject->getStartQuote();
		$e = $this->BEObject->getEndQuote();
		$order = "";
		if (!empty($options["order"])) {
			$order = $options["order"];
		} elseif (!empty($section_id)) {
			$order = "{$s}Tree{$e}.{$s}priority{$e}";
		}
		$dir = (isset($options["dir"]))? $options["dir"] : 1;
		$page = (!empty($options["page"]))? $options["page"] : 1;
		$dim = (!empty($options["dim"]))? $options["dim"] : 100000;

		// add rules for start and end pubblication date
		if ($this->checkPubDate['start'] == true && empty($filter['Content.start_date'])) {
			$filter['Content.*'] = '';
			$filter['AND'][] = array(
				'OR' => array(
					'Content.start_date <=' => date('Y-m-d'),
					'Content.start_date' => null
				)
			);
		}
		if ($this->checkPubDate['end'] == true && empty($filter['Content.end_date'])) {
			$filter['Content.*'] = '';
			$filter['AND'][] = array(
				'OR' => array(
					'Content.end_date >=' => date('Y-m-d'),
					'Content.end_date' => null
				)
			);
		}

		$urlFilter = $this->SessionFilter->getFromUrl();
		$this->SessionFilter->arrange($urlFilter);
		$filter = array_merge($filter, $urlFilter);

		$contents = $this->BeTree->{$searchMethod}($section_id, $this->status, $filter, $order, $dir, $page, $dim);

		$result = $detail;

		if (!empty($options['baseLevel'])) {
			$oldBaseLevel = $this->baseLevel;
			$this->baseLevel = true;
		}
		foreach ($contents["items"] as $c) {
			$object = $this->loadObj($c["id"]);
			if ($object !== self::UNLOGGED && $object !== self::UNAUTHORIZED) {
				try {
					$this->setCanonicalPath($object);
					if ($this->sectionOptions["itemsByType"]) {
						$result[$object['object_type']][] = $object;
					} else {
						$result["items"][] = $object;
					}
				} catch (BeditaException $ex) {
					// do nothing, just esclude object from final result if no canonical path was found
					if (Configure::read('debug') > 0) {
						$this->log("Valid canonicalPath isn't found for object with nickname " . $object["nickname"], LOG_DEBUG);
					}
				}
			}
		}
		if (!empty($options['baseLevel'])) {
			$this->baseLevel = $oldBaseLevel;
		}

		return array_merge($result, array("toolbar" => $contents["toolbar"]));
	}

	/**
	 * return objects for a specific tag
	 *
	 * @param string $tag tag name (friendly url/unique name)
	 * @param array $options search options
	 * 				"section" => name or id section
	 * 				"filter" => particular filter
	 * 				"order", "dir", "dim", "page" used like pagination parameters
	 *				"baseLevel" => true to use $this->baseLevel = true for model bindings
	 * @return array
	 */
	protected function loadObjectsByTag($tag, $options=array()) {
		return $this->loadObjectsByTagCategory($tag, $options, "tag");
	}

	/**
	 * load annotation referenced to some object
	 *
	 * @param string $annotationType, object type of the annotation e.g. "comment"
	 * @param string $objectName, reference object nickname or id
	 * @param array $options, specific options (pagination, filter) that override annotationOptions attribute
	 * @return array of annotations
	 */
	protected function loadAnnotations($annotationType, $objectName, $options = array()) {

		if (empty($annotationType) || empty($objectName))
			throw new BeditaBadRequestException(
				__("Annotation type or object_id missing", true),
				'FrontendController::loadAnnotations() requires $annotationType and $objectName'
			);

		$object_id = (is_numeric($objectName))? $objectName : $this->BEObject->getIdFromNickname($objectName);

		$options = array_merge($this->annotationOptions[$annotationType], $options, $this->params["named"]);
		$filter = (!empty($options["filter"]))? $options["filter"] : array();
		$filter["object_type_id"] = Configure::read("objectTypes." . $annotationType . ".id");
		$filter[Configure::read("objectTypes." . $annotationType . ".model") . ".object_id"] = $object_id;
		$order = (!empty($options["order"]))? $options["order"] : "BEObject.created";
		$dir = (isset($options["dir"]))? $options["dir"] : 1;
		$page = (!empty($options["page"]))? $options["page"] : 1;
		$dim = (!empty($options["dim"]))? $options["dim"] : 100000;

		$annotations = $this->BeTree->getChildren(null, $this->status, $filter, $order, $dir, $page, $dim);
		$result = array();
		foreach ($annotations["items"] as $a) {
			$object = $this->loadObj($a["id"]);
			if ($object !== self::UNLOGGED && $object !== self::UNAUTHORIZED)
				$result[Configure::read("objectTypes." . $annotationType . ".model")][] = $object;
		}
		return array_merge($result, array("toolbar" => $annotations["toolbar"]));
	}

	/**
	 * force download of media object
	 *
	 * @param $name id or object nickname
	 * @throws BeditaBadRequestException, BeditaNotFoundException
	 */
	public function download($name) {
		if (empty($name)) {
			throw new BeditaBadRequestException(
				__('Missing object unique name or id', true),
				'FrontendController::download() requires $name'
			);
		}

		$id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
		$object_type_id = $this->BEObject->findObjectTypeId($id);
		// verify type
		$conf = Configure::getInstance();
		if (($object_type_id === false) || !in_array($object_type_id, $conf->objectTypes['multimedia']['id']))
			throw new BeditaNotFoundException(__('Content not found', true) . ' id: ' . $id);

		$obj = $this->loadObj($id);
		if ($obj === self::UNLOGGED || $obj === self::UNAUTHORIZED) {
			$this->accessDenied($obj);
		}

		// check 'download' or 'attach' relation
		// TODO: check relatedObject status and position on tree????
		$objRel = ClassRegistry::init("ObjectRelation");
		$relatedObjectId = $objRel->find('first', array(
			'conditions' => array(
				"ObjectRelation.id" => $id,
				"ObjectRelation.switch" => array("downloadable_in", "attached_to")
			),
			'fields' => array('object_id')));
		// check if multimedia is on the tree
		$isOnTree = ClassRegistry::init("Tree")->isOnTree($id, $this->publication["id"]);
		if ($relatedObjectId === false && $isOnTree === false) {
			throw new BeditaNotFoundException(__('Content not found', true));
		}

		// media with provider or file on filesystem? TODO: use DS??
		if(!empty($obj['provider']) || $obj['uri'][0] !== "/") {
			$this->redirect($obj['uri']);
		}

		// TODO: for some extensions or mime-types redirect to media URL
		if(isset($conf->redirectMimeTypesDownload) &&
			in_array($obj['mime_type'], $conf->redirectMimeTypesDownload)) {
			$this->redirect($conf->mediaUrl.$obj['uri']);
		}

		$path = ($conf->mediaRoot).$obj['uri'];
		$f = new File($path);
		$info = $f->info();
		if(isset($conf->redirectExtensionsDownload) &&
				in_array($info['extension'], $conf->redirectExtensionsDownload)) {
			$this->redirect($conf->mediaUrl.$obj['uri']);
		}

		Configure::write('debug', 0);
		// use readfile
		// TODO: optimizations! use X-Sendfile ?
		header('Content-Description: File Transfer');
		header('Content-Type: '.$obj['mime_type']);
		header('Content-Disposition: attachment; filename='.$obj['name']);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . $obj['file_size']);
		ob_clean();
   		flush();
		readfile($path);
		exit();
	}

	/**
	 * show image for captcha
	 *
	 */
	public function captchaImage() {
		$this->historyItem = null;
		if(!isset($this->Captcha)) {
			App::import('Component', 'Captcha');
			$this->Captcha = new CaptchaComponent();
			$this->Captcha->startup($this);
		}
		$this->autoRender = false;
		$this->Captcha->image($this->captchaOptions);
	}

	/**
	 * save comment relative to an object, set 'info' flash message
	 * throw Exception in case of error and set 'error' flash message
	 *
	 * If it's ajax request and if not empty $this->params["form"]["render"] renders it
	 *
	 * elseif  it's not ajax request then redirect to referer
	 *
	 * @throws BeditaException
	 */
	public function saveComment() {
		$this->historyItem = null;
		if (!empty($this->data)) {

			// sanitize from scripts
			$this->data = BeLib::getInstance()->stripData($this->data);

			if (!isset($this->Comment)) {
				$this->Comment = $this->loadModelByType("Comment");
			}
			$this->data["title"] = substr($this->data["description"],0,30) . "...";
			// for comment status check contents.comments
			$beObject = ClassRegistry::init("BEObject");
			$commentsFlag = $beObject->field("comments", array("id" => $this->data['object_id']));
			if ($commentsFlag == 'moderated') {
				 $this->data["status"] = "draft";
				 $userMsgOK = "Your message has been sent and it's waiting approval.";
			} else if ($commentsFlag == 'on'){
				 $this->data["status"] = 'on';
				 $userMsgOK = "Your message has been saved.";
			} else {
				 throw new BeditaForbiddenException(__('Post comment disabled', true));
			}

			try {
				// check IP
				$bannedIP = ClassRegistry::init("BannedIp");
        		if ($bannedIP->isBanned($_SERVER['REMOTE_ADDR'])) {
					throw new BeditaForbiddenException(
						__('Error saving comment because the IP is banned', true),
						'IP banned: ' . $_SERVER['REMOTE_ADDR']
					);
        		}

				// check captcha if not logged
				if (!$this->logged) {
					if(!isset($this->Captcha)) {
						App::import('Component', 'Captcha');
						$this->Captcha = new CaptchaComponent();
						$this->Captcha->startup($this);
					}
					$this->Captcha->checkCaptcha();
				// set User data
				} else {
					$userdata = $this->Session->read($this->BeAuth->sessionKey);
					$this->data["user_created"] = $userdata["id"];
					$this->data["user_modified"] = $userdata["id"];
					$this->data["author"] = (!empty($userdata["realname"]))? $userdata["realname"] : $userdata["userid"];
					if ( (trim($userdata["email"]) != "") ) {
						$this->data["email"] = $userdata["email"];
					}
				}

				// build thread path
				if (!empty($this->params["form"]["thread_parent_id"])) {
					$thread_path = $this->Comment->field("thread_path", array("id" => $this->params["form"]["thread_parent_id"]));
					$this->data["thread_path"] = (!empty($thread_path))? $thread_path . "/" . $this->params["form"]["thread_parent_id"] : "/" . $this->params["form"]["thread_parent_id"];
				}

				// content url shown in notification
				if (empty($this->data["notification_content_url"])) {
					$this->data["notification_content_url"] = $this->publication["public_url"] . $this->referer();
				}

				$this->Transaction->begin();
				if (!$this->Comment->save($this->data)) {
					throw new BeditaException(__("Error saving comment", true), $this->Comment->validationErrors);
				}
				$this->Transaction->commit();
				$this->userInfoMessage(__($userMsgOK, true));
			} catch (BeditaException $ex) {
				$this->Transaction->rollback();
				$this->log($ex->errorTrace());
				$this->userErrorMessage($ex->getMessage());
				$error = true;
			}

		}

		// if it's ajax call no redirect by referer
		if($this->RequestHandler->isAjax()) {
			$this->layout = "ajax";
			$this->set('comment',$this->loadObj($this->Comment->id));
			if (!empty($this->params["form"]["render"])) {
				$this->render(null, null, $this->params["form"]["render"]);
			}
		} else {
			// saveCommentBeforeFilter
			if (method_exists($this, "saveCommentBeforeRender")) {
				$this->saveCommentBeforeRender();
			}
			$urlToRedirect = $this->referer();
			if (!empty($error))
				$urlToRedirect .= "/#error";
			elseif ($commentsFlag == 'on')
				$urlToRedirect .= "/#comment-".$this->Comment->id;
			$this->redirect($urlToRedirect);
		}

	}

	/**
	 * show an object in print mode with specific layout and view
	 * CakePHP layout: print (if dosen't exists in frontend app use backend print layout)
	 * use print view if not set a specific $printLayout
	 *
	 * @param int $id
	 * @param string $printLayout, the view template to use
	 */
	public function printme($id=null, $printLayout=null) {
		if (!empty($this->params["form"]["id"]))
			$id = $this->params["form"]["id"];
		if (!empty($this->params["form"]["printLayout"]))
			$id = $this->params["form"]["printLayout"];
		$objectData = $this->loadObj($id);
		if ($objectData == self::UNLOGGED || $objectData === self::UNAUTHORIZED) {
			$this->accessDenied($objectData);
		}
		$this->layout = "print";
		$this->set("printLayout", $printLayout);
		$this->set("object", $objectData);
		if (file_exists(APP."views".DS."pages".DS.$printLayout.".tpl"))
			$this->render($printLayout);
		else
			$this->render("print");
	}

	/**
	 * save a BEdita object. User has to be logged
	 *
	 * @param string $modelName (Document, Event, ....).
	 * 		  If undefined get object type from $this->data["object_type_id"]
	 * @return mixed int|boolean, false on error, object_id saved on success
	 */
	protected function save($modelName=null) {
		if (!$this->logged) {
			$this->accessDenied(self::UNLOGGED);
		}
		try {
			if (empty($modelName) && empty($this->data["object_type_id"])) {
				throw new BeditaBadRequestException(__("no object type defined",true));
			}
			$modelName = (empty($modelName))? Configure::read("objectTypes.".$this->data["object_type_id"].".model") : $modelName;
			$objectModel = ClassRegistry::init($modelName);
			// content url shown in notification
			if (empty($this->data["notification_content_url"])) {
				$this->data["notification_content_url"] = $this->publication["public_url"] . $this->referer();
			}
			// sanitize from scripts
			$this->data = BeLib::getInstance()->stripData($this->data);

			$this->Transaction->begin();
			$this->saveObject($objectModel);
			$this->Transaction->commit();
			$this->userInfoMessage(__($modelName . " saved",true));
			$this->eventInfo("object [". $objectModel->id ."] saved");
			return $objectModel->id;
		} catch (BeditaException $ex) {
			$this->Transaction->rollback();
			$this->log($ex->errorTrace());
			$this->userErrorMessage($ex->getMessage());
			return false;
		}
	}

	/**
	 * delete a BEdita object. User has to be logged
	 *
	 * @return boolean
	 */
	protected function delete() {
		if (!$this->logged) {
			$this->accessDenied(self::UNLOGGED);
		}
		try {
			if (!empty($this->data["object_type_id"])) {
				$object_type_id = $this->data["object_type_id"];
			} elseif (!empty($this->data["id"])) {
				$object_type_id = $this->BEObject->findObjectTypeId($this->data["id"]);
			} else {
				throw new BeditaBadRequestException(__("no object type defined",true));
			}
			$modelName = Configure::read("objectTypes.".$object_type_id.".model");
			$this->{$modelName} = ClassRegistry::init($modelName);
			$objectsDeleted = $this->deleteObjects($modelName);
			$this->userInfoMessage(__($objectsDeleted . " deleted",true));
			return true;
		} catch (BeditaException $ex) {
			$this->log($ex->errorTrace());
			$this->userErrorMessage($ex->getMessage());
			return false;
		}
	}

	/**
	 * check parents status of $section_id
	 *
	 * if one or more parents haven't status IN $this->status array throw a BeditaNotFoundException
	 *
	 * @param int $section_id
	 * @throws BeditaNotFoundException
	 */
	private function checkParentStatus($section_id) {
		$parent_path = $this->Tree->field("parent_path", array("id" => $section_id));
		$parent_array = explode("/", trim($parent_path,"/"));
		if (!empty($parent_array[0])) {
			$countParent = count($parent_array);
			$countParentStatus = $this->BEObject->find("count", array(
					"conditions" => array(
						"status" => $this->status,
						"id" => $parent_array
					),
					"contain" => array()
				)
			);

			if ($countParent != $countParentStatus) {
				throw new BeditaNotFoundException(__("Content not found", true));
			}
		}
	}

	/**
	 * add "draft" status to class attribute $status
	 */
	protected function showDraft() {
		$this->status[] = "draft";
	}

	/**
	 * return class attribute $status
	 * @return array
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * dynamic manifest
	 * if Configure::read("debug") === 0 (production env) the manifest is cached by CakePHP
	 * else  (development env) the manifest is generated every time
	 */
	public function manifestAppcache(){
		$debugLevel = Configure::read("debug");
		$manifestAppcache = ($debugLevel === 0)? Cache::read('manifestAppcache') : false;
		if (!$manifestAppcache) {
			App::Import('Core','Folder');
			$folder = new Folder();
			$exceptions = (Configure::read("appcacheExceptions")) ? Configure::read("appcacheExceptions") : array();
			$assets = $folder->tree(WWW_ROOT, $exceptions, 'file');
			// hash: MD5 of a string containing ordered file paths and last modified time of those files
			$treeStr = "";
			foreach($assets as $file) {
				$treeStr .= $file . "-" . filemtime($file);
			}
			$manifestAppcache["hash"] = md5($treeStr);
			// rewrite all paths to be relative to index.php
			$assets = array_map(
				function($file){
					return str_replace(WWW_ROOT, '', $file);
				},
				$assets
			);
			natsort($assets);
			$manifestAppcache["assets"] = $assets;
			if ($debugLevel !== 0) {
				Cache::write('manifestAppcache', $manifestAppcache);
			}
		}
		Configure::write('debug',0);
		header('Content-Type: text/cache-manifest');
		$this->layout = 'ajax';
		$this->set("hash",$manifestAppcache["hash"]);
		$this->set("assets",$manifestAppcache["assets"]);
	}
}
