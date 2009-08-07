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

/**
 * Frontend base class (Frontend API)
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
if(defined('BEDITA_CORE_PATH')) {
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
	 * true to check Content.start >= now
	 * 
	 * @var boolean 
	 */
	protected $checkPubDate = true;
	
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
	 * 		"childrenParams" array to define special filters and pagination options   
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
	 * @var unknown_type
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
	 * tag options
	 * 
	 * @var array
	 */
	protected $tagOptions = array();
	
	/**
	 * search options, attribute used on search
	 * 
	 * @var array
	 */
	protected $searchOptions = array("order" => "title", "dir" => 1, "dim" => 50, "page" => 1, "filter" => false);
	
	/**
	 * user logged in or not
	 * 
	 * @var bool
	 */
	private $logged = false;
	
	/**
	 * path to redirect after logout action
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
	 * every frontend has to implement checkLogin
	 * 
	 * @see bedita-app/AppController#checkLogin()
	 */
	protected function checkLogin() {
		return false;
	}
	
	/**
	 * try to login user with some groups
	 * return false if it fails to login 
	 * 		try to login user if there is POST data correct 
	 * 
	 * @param array $groups
	 * @return boolean
	 */
	private function login(array $groups) {
		if (!empty($this->params["form"]["login"])) {
			$userid 	= (isset($this->params["form"]["login"]["userid"])) ? $this->params["form"]["login"]["userid"] : "" ;
			$password 	= (isset($this->params["form"]["login"]["passwd"])) ? $this->params["form"]["login"]["passwd"] : "" ;
			
			if(!$this->BeAuth->login($userid, $password, null, $groups)) {
				//$this->loginEvent('warn', $userid, "login not authorized");
				$this->userErrorMessage(__("Wrong username/password or session expired", true));
				return false;
			} else {
				$this->eventInfo("FRONTEND logged in publication");
			}
			$redirect = (!empty($this->params["form"]["backURL"]))? $this->params["form"]["backURL"] : $this->loginRedirect;
			
			$this->redirect($redirect);
			return true;
		}
		return false;
	}
	
	protected function logout($autoRedirect=true) {
		$this->BeAuth->logout();
		$this->eventInfo("FRONTEND logged out: publication " . $this->publication["title"]);
		if ($autoRedirect) {
			$this->redirect($this->logoutRedirect);
		}
	}
	
	/**
	 * check if there's an active session and try to login if user not logged
	 *  - if "authorizedGroups" array defined in frontend.ini.php, user has to be in one of those groups
	 *  - if "staging" is defined only backend authorized groups are permitted 
	 *	- otherwise any group is accepted
	 * 
	 * @return boolean
	 */
	protected function checkIsLogged() {	
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
			return $this->login($frontendGroupsCanLogin);
		}

		return true;
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
			$this->status = array('on', 'off', 'draft');
			$this->publication = $this->loadObj(Configure::read("frontendAreaId"));
			$this->set('publication', $this->publication);
			throw new BeditaPublicationException("Publication not ON", array("layout" => $pubStatus));
		}
		
		// check is logged
		$this->logged = $this->checkIsLogged();
		$defaultShow = $this->showUnauthorized;
		$this->showUnauthorized = true;
		$this->publication = $this->loadObj(Configure::read("frontendAreaId"),false);
		$this->showUnauthorized = $defaultShow;
		// set publication data for template
		$this->set('publication', $this->publication);
		
		/* if user is unlogged and it's a staging site OR
		 * if user hasn't permissions to access at the publication 
		 * throws exception
		 */
		if ( (!$this->logged && Configure::read("staging") === true) || ((empty($this->params["pass"][0]) || $this->params["pass"][0] != "logout") && !$this->publication["authorized"])) {
			if (!$this->logged)
				$errorType = self::UNLOGGED;
			else
				$errorType = self::UNAUTHORIZED;
			$this->setupLocale();
			$this->beditaBeforeFilter();
			throw new BeditaFrontAccessException(null, array("errorType" => $errorType));
		}
						
		// set filterPublicationDate
		$filterPubDate = Configure::read("filterPublicationDate");
		if (isset($filterPubDate)) 
			$this->checkPubDate = $filterPubDate;
			
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
			if(!empty($lang)) {
				$this->currLang = $lang;
			} else {
				// HTTP autodetect
				$l10n = new L10n();
				$l10n->get();
				$this->currLang = $l10n->lang;
				if(!isset($this->currLang)) {
					$this->currLang = $conf->frontendLang;
				} else if(!array_key_exists($this->currLang, $conf->frontendLangs)) {
					if (!empty($conf->frontendLangsMap[$this->currLang])) {
						$this->currLang = $conf->frontendLangsMap[$this->currLang];
					} else {
						$this->currLang = $conf->frontendLang;
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
		$dateFormatValidation = $conf->datePattern;
		$dateFormatValidation = preg_replace(array("/%d/", "/%m/", "/%Y/"), array("dd","mm","yyyy"), $dateFormatValidation);
		Configure::write('dateFormatValidation', $dateFormatValidation);
	}

	/**
	 * change language
	 * 
	 * @param string $lang
	 * @param string $forward redirect action after changing language. If it's null redirect to refere
	 * @return unknown_type
	 */
	public function changeLang($lang, $forward = null) {

		if (empty($lang)) {
			throw new BeditaException("No lang selected");
		}

		$conf = Configure::getInstance();
		if (!array_key_exists($lang, $conf->frontendLangs)) {
			throw new BeditaException("wrong lang selected: ".$lang);
		}
		$this->Session->write('Config.language', $lang);
		$this->Cookie->write($conf->cookieName["langSelect"], $lang, null, '+350 day'); 
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
	 * @return true if content may be published, false otherwise
	 */
	protected function checkPubblicationDate(array $obj) {
		$currDate = strftime("%Y-%m-%d");
		if(isset($obj["start"])) {
			if(strncmp($currDate, $obj["start"], 10) < 0)
				return false;
		}
		if(isset($obj["end"])) {
			if(strncmp($currDate, $obj["end"], 10) > 0)
				return false;
		}
		return true;
	}
	
	/**
	 * handle Exceptions
	 * 
	 * @param Exception $ex
	 * @return unknown_type
	 */
	public static function handleExceptions(Exception $ex) {

		if ($ex instanceof BeditaPublicationException) {
			$currentController = AppController::currentController();
			echo $currentController->render(false, $ex->getLayout());
		} elseif ($ex instanceof BeditaFrontAccessException) {
			$errorType = $ex->getErrorType();
			$params = array(
				'details' => $ex->getDetails(), 
				'msg' => $ex->getMessage(), 
				'result' => $ex->result,
				'errorType' => $ex->getErrorType()
			);
			
			include_once (APP . 'app_error.php');
			return new AppError('handleExceptionFrontAccess', $params, $ex->errorTrace());
		} else {
			
			if($ex instanceof BeditaException) {
				$errTrace =  $ex->errorTrace();   
			} else {
				$errTrace =  get_class($ex)." -  ". $ex->getMessage().
					"\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
			}
			include_once (APP . 'app_error.php');
			return new AppError('handleExceptionFrontend', 
					array('details' => $ex->getDetails(), 'msg' => $ex->getMessage(), 
					'result' => $ex->result), $errTrace);
					
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see bedita-app/AppController#handleError()
	 */
	public function handleError($eventMsg, $userMsg, $errTrace) {
		if(Configure::read('debug') > 0) {
			$this->log($errTrace);
		}
	}
	
	/**
	* Get tree starting from specified section or area
	* 
	* @param integer $parentName		parent nickname or id 
	* @param bool $loadContents			if it's true load all contents too. Default false
	* @param array $exclude_nicknames	list exclude sections 
	* @param integer $depth				tree's depth level (default=10000 => all levels)
	* */
	protected function loadSectionsTree($parentName,  $loadContents=false, array $exclude_nicknames=null, $depth=10000) {

		$conf = Configure::getInstance(); 
		$parent_id = is_numeric($parentName) ? $parentName: $this->BEObject->getIdFromNickname($parentName);
		$result = array();
		$filter["object_type_id"] = $conf->objectTypes['section']["id"];
		$sections = $this->BeTree->getChildren($parent_id, $this->status, 
			$filter, "priority") ;

		foreach ($sections['items'] as $s) {
			
			if(!empty($exclude_nicknames) && in_array($s['nickname'], $exclude_nicknames)) 
				continue ;
			
			$sectionObject = $this->loadObj($s['id'],false);
			if ($sectionObject !== self::UNAUTHORIZED) {			
				if($loadContents) {
					$option = array("filter" => array("object_type_id" => Configure::read("objectTypes.leafs.id")));
					 $objs = $this->loadSectionObjects($s['id'], $option);
					 $sectionObject['objects'] = (!$this->sectionOptions["itemsByType"] && !empty($objs["childContents"]))? $objs["childContents"] : $objs;
				}
				if ($depth > 1)
					$sectionObject['sections'] = $this->loadSectionsTree($s['id'], $loadContents, $exclude_nicknames, $depth-1);
				$result[] = $sectionObject;
			}
		}

		return $result;
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
	* @param  $secName					nickname or section id
	* @param  bool $loadContents		true meaning it loads all contents of each section 
	* @param array $exclude_nicknames	list exclude sections 
	* 
	* @return array of level selected 
	* 							
	* */
	protected function loadSectionsLevels($secName, $loadContents=false, array $exclude_nicknames=null) {
		$conf = Configure::getInstance(); 
		$result = array();
		
		$section_id = is_numeric($secName) ? $secName : $this->BEObject->getIdFromNickname($secName);
		
		$path = $this->Tree->field("path", array("id" => $section_id));
		$parents = explode("/", trim($path,"/"));
		
		$level = 0;
		$filter["object_type_id"] = $conf->objectTypes['section']["id"];
		foreach ($parents as $p_id) {
			$sections = $this->BeTree->getChildren($p_id, $this->status, 
				$filter, "priority") ;

			foreach ($sections["items"] as $s) {
				
				if(!empty($exclude_nicknames) && in_array($s['nickname'], $exclude_nicknames)) 
					continue ;
				
				$sectionObject = $this->loadObj($s['id'],false);
				if ($sectionObject !== self::UNAUTHORIZED) {
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
				$obj = $this->loadObj($pub["id"],false);
				if ($obj !== self::UNAUTHORIZED)
					$publications[] = $obj;
			}
		}
		$tplVar = (!empty($tplVar))? $tplVar : "publicationsList";
		$this->set($tplVar, $publications);
	}
	
	/**
	 * preapre an XML containing sitemap specification
	 * view in bedita-app/views/pages/sitemap_xml.tpl
	 * 
	 */
	public function sitemapXml() {
		$this->sitemap(true);
		$this->layout = null;
		$this->view = "Smarty";
		header("Content-type: text/xml; charset=utf-8");
	}

	/**
	 * build array for sitemap
	 * 
	 * @param bool $xml_out
	 * @return array
	 */
	public function sitemap($xml_out = false) {
		$conf = Configure::getInstance() ;
		$extract_all = (!empty($conf->sitemapAllContent)) ? $conf->sitemapAllContent : false;
		
		if($xml_out) {
			$filter = null;
			if(!$extract_all) {
				$filter = array();
				$filter["object_type_id"] = $conf->objectTypes['section']["id"];
			}
			$sections = $this->BeTree->getDiscendents($conf->frontendAreaId,$this->status,$filter) ;
			$sectionsTree = $sections['items'];
			$urlset = array();
			$i=0;
			foreach($sectionsTree as $k => $v) {
				$urlset[$i] = array();
				$urlset[$i]['loc'] = $this->publication["public_url"]."/".$v['nickname'];
				//$urlset['lastmode'] = $this->BeTree->getChildren($id, null, $filter, "title", true, $page, $dim=1);
				//$urlset[$i]['changefreq'] = 'always'; /*always,hourly,daily,weekly,monthly,yearly,never*/
				//$urlset[$i]['priority'] = '0.5';
				$i++;
			}
			$this->set('urlset',$urlset);
		} else {
			if(!in_array('BeTree', $this->helpers)) {
				$this->helpers[] = 'BeTree';
			}
			$this->baseLevel = true;
			$itemsByType = $this->sectionOptions["itemsByType"];
			$this->sectionOptions["itemsByType"] = false;
			$sectionsTree = $this->loadSectionsTree($conf->frontendAreaId,$extract_all) ;
			$this->sectionOptions["itemsByType"] = $itemsByType;
			$this->baseLevel = false;
		}
		
		$this->set('sections_tree',$sectionsTree);
	}

	
	/**
	 * Publish RSS feed with contents inside section $sectionName
	 *
	 * @param string $sectionName, section's nickname
	 */
	public function rss($sectionName) {
	   $s = $this->loadObjByNick($sectionName);
	   if($s['syndicate'] === "off") {
	   		throw new BeditaException(__("Content not found", true));
	   }
	   
	   $channel = array( 'title' => $this->publication["public_name"] . " - " . $s['title'] , 
        'link' => "/section/".$sectionName,
//        'url' => Router::url("/section/".$sectionName),
        'description' => $s['description'],
        'language' => $s['lang'],
       );
	   $this->set('channelData', $channel);
       $rssItems = array();
	   $items = $this->BeTree->getChildren($s['id'], $this->status, false, "priority", ($s['priority_order']=="asc"));
	   if(!empty($items) && !empty($items['items'])) {
			foreach($items['items'] as $index => $item) {
				$obj = $this->loadObj($item['id']);
				$description = $obj['description'];
				$description .= (!empty($obj['abstract']) && !empty($description))? "<hr/>" .  $obj['abstract'] : $obj['abstract'];
				$description .= (!empty($obj['body']) && !empty($description))? "<hr/>" .  $obj['body'] : $obj['body'];
	            $rssItems[] = array( 'title' => $obj['title'], 'description' => $description,
	                'pubDate' => $obj['created'], 'link' => "/section/".$s['nickname']."/".$item['id']);
			}
		}
       $this->set('items', $rssItems);
       $this->view = 'View';
       // add RSS helper if not present
       if(!in_array('Rss', $this->helpers)) {
       		$this->helpers[] = 'Rss';
       }
       $this->layout = NULL;
	}
	
	/**
	 * output a json object of returned array by section or content method
	 * @param $name
	 * @return unknown_type $name, nickname or id
	 */
	public function json($name) {
		$this->route($name);
		header("Content-Type: application/json");
		$this->view = 'View';
		$this->layout = null;
		$this->action = "json";
		$this->set("data", $this->viewVars["section"]);
	}
	
	/**
	 * output an xml of returned array by section or content method
	 *
	 * passing a "format" named parameters in the url obtain an xml "attributes" format or an xml "tags" format
	 * i.e. http://www.example.com/xml/nickname/format:tags output a tag style xml 
	 * default is defined by class attribute xmlFormat
	 * 
	 * @param unknown_type $name, nickname or id
	 */
	public function xml($name) {
		$this->route($name);
		$this->outputXML(array("section" => $this->viewVars["section"]));
	}
	
	/**
	 * output an xml of returned array by loadObj/loadObjByNick method
	 *
	 * passing a "format" named parameters in the url obtain an xml "attributes" format or an xml "tags" format
	 * i.e. http://www.example.com/xmlobject/nickname/format:tags output a tag style xml 
	 * default is defined by class attribute xmlFormat
	 * 
	 * @param string $name, nickname or id
	 */
	public function xmlobject($name) {
		$object = (is_numeric($name))? $this->loadObj($name) : $this->loadObjByNick($name);
		$this->outputXML(array("object" => $object));
	}
	
	/**
	 * prepare to XML output
	 * 
	 * @param $data
	 */
	private function outputXML($data) {
		header("content-type: text/xml; charset=utf-8");
		if(!in_array('Xml', $this->helpers)) {
       		$this->helpers[] = 'Xml';
		}
		
		$availableFormat = array("attributes", "tags");
		if (!empty($this->passedArgs["format"]) && in_array($this->passedArgs["format"],$availableFormat)) {
			$options = array("format" => $this->passedArgs["format"]);
		} else {
			$options = array("format" => $this->xmlFormat);
		}
		
		$this->set("options", $options);
		$this->set("data", $data);
		$this->action = "xml";
		$this->view = 'View';
		$this->layout = NULL;
	}
	
	/**
	 * Like loadObj using nickname
	 *
	 * @param string $obj_nick
	 * @return array
	 */
	protected function loadObjByNick($obj_nick) {
		return $this->loadObj($this->BEObject->getIdFromNickname($obj_nick));
	}

	/**
	 * Like loadAndSetObj using nickname
	 *
	 * @param string $obj_nick
	 * @return array
	 */
	protected function loadAndSetObjByNick($obj_nick, $var_name = null) {
		$this->loadAndSetObj($this->BEObject->getIdFromNickname($obj_nick) , $var_name);
	}
	
	/**
	 * Load bedita Object and set view var with $var_name or object type (e.g. "Document", "Event"..)
	 * Returns object loaded
	 * Throws Exception on errors
	 *
	 * @param int $obj_id
	 * @param string $var_name
	 * @return array
	 */
	protected function loadAndSetObj($obj_id, $var_name = null) {
		$obj = $this->loadObj($obj_id);
		$this->set((isset($var_name)? $var_name: $obj['object_type']),$obj);
	}
	
	/**
	 * Returns bedita Object
	 * Throws Exception on errors
	 *
	 * @param int $obj_id
	 * @param bool $blockAccess
	 * 				true => if user is unlogged eturn UNLOGGED constant
	 * 						if user hasn't permission to access at the object return UNAUTHORIZED constant
	 * 						(used when load pages main object like in section method)
	 * 				false => if user unlogged dosen't block the action
	 * 						 if user unauthorized to access at the object and $this->showUnauthorized=true 
	 * 						 	load object detail setting "authorized" => false in object array
	 * 						(used when load objects list like in loadSectionObjects method)
	 * @return array object detail
	 */
	protected function loadObj($obj_id, $blockAccess=true) {
		if($obj_id === null)
			throw new BeditaException(__("Content not found", true));
		
		$authorized = false;
			
		// check permissions
		$permissionModel = ClassRegistry::init("Permission");
		if ($perms = $permissionModel->isPermissionSetted($obj_id, OBJ_PERMS_READ_FRONT)) {
			if (!$this->logged && $blockAccess)
				return self::UNLOGGED;
			
			if ($this->logged && $permissionModel->checkPermissionByUser($perms, $this->BeAuth->user)) {
				$authorized = true;
			}
		} else {
			$authorized = true;
		}
		
		if ($authorized == false && ($blockAccess || !$this->showUnauthorized))
			return self::UNAUTHORIZED;
		
		$modelType = $this->BEObject->getType($obj_id);
		if(!isset($this->{$modelType})) {
			$this->{$modelType} = $this->loadModelByType($modelType);
		}

		if (!$this->baseLevel) {
			$this->modelBindings($this->{$modelType});
		} else {
			$this->{$modelType}->contain(array("BEObject" => array("LangText")));
		}
			
		$obj = $this->{$modelType}->find("first", array(
								"conditions" => array(
									"BEObject.id" => $obj_id,
									"status" => $this->status
									)
								)
							);
		if(empty($obj)) {
			throw new BeditaException(__("Content not found", true));
		}
							
		if($this->checkPubDate && !$this->checkPubblicationDate($obj)) {
			throw new BeditaException(__("Content not found", true));
		}
		
		$obj["publication_date"] = (!empty($obj["start"]))? $obj["start"] : $obj["created"];

		$this->BeLangText->setObjectLang($obj, $this->currLang, $this->status);

		if(!empty($obj["RelatedObject"])) {
			$obj['relations'] = $this->objectRelationArray($obj['RelatedObject'], $this->status, array("mainLanguage" => $this->currLang));
			unset($obj["RelatedObject"]);
			$obj['relations_count'] = array();
			foreach ($obj["relations"] as $k=>$v) {
				$obj['relations_count'][$k] = count($v);
			}
		}
		if (!empty($obj['Annotation'])) {
			$this->setupAnnotations($obj, $this->status);
		}
		unset($obj['Annotation']);
		$obj['object_type'] = $modelType;
		$obj['authorized'] = $authorized;
		
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
	protected function loadSectionObjectsByNick($parentNick, $options=array()) {
		return $this->loadSectionObjects($this->BEObject->getIdFromNickname($parentNick), $options);
	}	

	/**
	 * Load objects in section $parent_id
	 *
	 * @param int $parent_id
	 * @param array $options, filter and pagination options
	 * 
	 * @return array
	 */
	protected function loadSectionObjects($parent_id, $options=array()) {

		if(empty($parent_id)) {
			throw new BeditaException("Bad data");
		}
		
		$this->checkParentStatus($parent_id);
		
		$priorityOrder = $this->Section->field("priority_order", array("id" => $parent_id));
		if(empty($priorityOrder)) {
			$priorityOrder = "asc";
		}
		$sectionItems = array();
		
		$filter = (!empty($options["filter"]))? $options["filter"] : false;
		$order = (!empty($options["order"]))? $options["order"] : "priority";
		$dir = (isset($options["dir"]))? $options["dir"] : ($priorityOrder == "asc");
		$page = (!empty($options["page"]))? $options["page"] : 1;
		$dim = (!empty($options["dim"]))? $options["dim"] : 100000;
		
		// add rules for start and end pubblication date
		if ($this->checkPubDate == true) {
			if (empty($filter["Content.start"]))
				$filter["Content.start"] = "<= '" . date("Y-m-d") . "' OR `Content`.start IS NULL";
			if (empty($filter["Content.end"]))
				$filter["Content.end"] = ">= '" . date("Y-m-d") . "' OR `Content`.end IS NULL";
		}
		
		$items = $this->BeTree->getChildren($parent_id, $this->status, $filter, $order, $dir, $page, $dim);
		
		if(!empty($items) && !empty($items['items'])) {
			foreach($items['items'] as $index => $item) {
				$obj = $this->loadObj($item['id'], false);
				if ($obj !== self::UNAUTHORIZED) {
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
	 * @param $name, id or content nickname
	 */
	public function content($name) {
		if(empty($name))
			throw new BeditaException(__("Content not found", true));
		
		$content_id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
		
		// if it's defined frontend publication id then search content inside that publication else in all BEdita
		$conditions = (!empty($this->publication["id"]))? "id = $content_id AND path LIKE '/" . $this->publication["id"] . "/%'" : "id = $content_id" ;
		
		$section_id = $this->Tree->field('parent_id',$conditions, "priority");
		
		if($section_id === false) {
			throw new BeditaException(__("Content not found", true));
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
		if(!empty($contentName)) {
			$content_id = is_numeric($contentName) ? $contentName : $this->BEObject->getIdFromNickname($contentName);
			$contentType = $this->BEObject->getType($content_id);
			if($contentType === "Section") {
				$args = func_get_args();
				array_shift($args);
				return call_user_func_array(array($this, "section"), $args);
			// check that contentName is a child of secName
			} elseif ( $this->Tree->find('count',array("conditions" => array("id" => $content_id, "parent_id" => $sectionId))) == 0 ) {	
				throw new BeditaException(__("Content " . $contentName . " doesn't belong to " . $secName, true));
			}
		}
		
		$secNameFilter = str_replace("-","_",$secName);
		// section before filter
		if (method_exists($this, $secNameFilter . "BeforeFilter")) {
			$this->{$secNameFilter . "BeforeFilter"}();
		}
		
		$section = $this->loadObj($sectionId);
		if ($section === self::UNLOGGED || $section === self::UNAUTHORIZED)
			throw new BeditaFrontAccessException(null, array("errorType" => $section));

		$section["pathSection"] = $this->getPath($sectionId);
		$this->sectionOptions["childrenParams"] = array_merge($this->sectionOptions["childrenParams"],$this->getPassedArgs());
		
		if(!empty($content_id)) {
			$section['currentContent'] = $this->loadObj($content_id);
			if ($section['currentContent'] === self::UNLOGGED || $section['currentContent'] === self::UNAUTHORIZED)
				throw new BeditaFrontAccessException(null, array("errorType" => $section['currentContent']));
			
			$section["contentRequested"] = true;
			
			if ($this->sectionOptions["showAllContents"]) {
				$this->baseLevel = true;
				$checkPubDate = $this->checkPubDate;
				$this->checkPubDate = false;
				
				$tmp = $this->loadSectionObjects($sectionId, $this->sectionOptions["childrenParams"]);
				if (!$this->sectionOptions["itemsByType"])
					$section = array_merge($section, $tmp);
				else
					$section = array_merge($section, array("children" => $tmp));
				
				$this->baseLevel = false;
				$this->checkPubDate = $checkPubDate;
			}
		} else {
			$tmp = $this->loadSectionObjects($sectionId, $this->sectionOptions["childrenParams"]);
			
			if (!$this->sectionOptions["itemsByType"]) {
				$tmp['currentContent'] = (!empty($tmp['childContents']))? $tmp['childContents'][0] : array();
				$section = array_merge($section, $tmp);
			} else {
				if(empty($tmp)) {
					$section = array_merge($section, array("currentContent" => array(), "children" => array()));
				} else {
					$toolbar = $tmp["toolbar"];
					unset($tmp["toolbar"]);
					$current = current($tmp);
					$section = array_merge($section, array("currentContent" => $current[0], "children" => $tmp, "toolbar" => $toolbar));
				}
			}
		}

		$this->set('section', $section);
		
		// section after filter
		if (method_exists($this, $secNameFilter . "BeforeRender")) {
			$this->{$secNameFilter . "BeforeRender"}();
		}
	}
	
	/**
	 * route to section, content or another method defined in reservedWords
	 *
	 */
	public function route() {

		$args = func_get_args();
		if(count($args) === 0 || empty($args[0]))
			throw new BeditaException(__("Content not found", true));

		$name = $args[0];
		// look if reserverd 
		if(in_array($name, Configure::read("defaultReservedWords")) ||
			in_array($name, Configure::read("cfgReservedWords"))) {
			$name = str_replace(".", "_", $name); // example: sitemap.xml => sitemap_xml
			$this->action = $name;
			// load object with nickname $name if exists
			$id = $this->BEObject->getIdFromNickname($name);
			if(!empty($id)) {
				$this->loadAndSetObj($id, "object");
			}
			$methodName = $name[0] . substr(Inflector::camelize($name), 1);
			// check before filter method
			if (method_exists($this, $methodName . "BeforeFilter")) {
				$this->{$methodName . "BeforeFilter"}();
			}
			// check method
			if(method_exists($this, $methodName)) {
				array_shift($args);
				call_user_func_array(array($this, $methodName), $args);
			}
			// check before render method
			if (method_exists($this, $methodName . "BeforeRender")) {
				$this->{$methodName . "BeforeRender"}();
			}
			return;
		}
			
		$id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
		$object_type_id = $this->BEObject->findObjectTypeId($id);
		
		if ($object_type_id == Configure::read("objectTypes.section.id") || $object_type_id == Configure::read("objectTypes.area.id")) {
			$this->action = "section";
			call_user_func_array(array($this, "section"), $args);
		} else {
			$this->content($id);
		}
	}
	
	
	public function search() {
		if(!in_array('BeToolbar', $this->helpers)) {
       		$this->helpers[] = 'BeToolbar';
		}
		$this->searchOptions = array_merge($this->searchOptions,$this->getPassedArgs());
		$result = $this->BeTree->getDiscendents($this->publication["id"], $this->status, $this->searchOptions["filter"], $this->searchOptions["order"], $this->searchOptions["dir"], $this->searchOptions["page"], $this->searchOptions["dim"]);
		$this->set("searchResult", $result); 
	}
	
	/**
	 * public subscribe page, used for newsletter/frontend subscribe/unsubscribe
	 * 
	 * @param string $what
	 * @return unknown_type
	 */
	public function subscribe($what="newsletter") {
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
	}
	
	/**
	 * manage hash request like newsletter/frontend subscribe/unsubscribe
	 * 
	 * @param string $service_type
	 * @param string $hash
	 * @return unknown_type
	 */
	public function hashjob($service_type=null, $hash=null) {
		if (!empty($service_type) || !empty($hash)) {
			
			if (!empty($hash)) {
				
				if (!$hashRow = $this->BeHash->getHashRow($hash)) {
					$this->redirect("/hashjob");
				}
				$service_type = $hashRow["service_type"];
				$method = (!empty($hashRow["command"]))?  $hashRow["service_type"] . "_" . $hashRow["command"] : $hashRow["service_type"];
				$method = Inflector::camelize($method);
				$method{0} = strtolower($method{0});
				$this->data["HashJob"] = $hashRow;
				
			// first hash operation
			} else {
				if (empty($service_type)) {
					throw new BeditaException(__("missing service type", true));
				}
				$method = Inflector::camelize($service_type);
				$method{0} = strtolower($method{0});
				$this->data["HashJob"]["service_type"] = $service_type;
				$this->data = array_merge($this->data, $this->getPassedArgs());
			}
			
			if (!method_exists($this->BeHash, $method)) {
				throw new BeditaException(__("missing method to manage hash case", true));
			}
			
			$this->Transaction->begin(); 
			$this->BeHash->{$method}($this->data);
			$this->Transaction->commit();
			$this->redirect("/hashjob");
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
		$path = $this->Tree->field("parent_path", array("id" => $object_id));
		$parents = explode("/", trim($path,"/"));
		if (!empty($parents[0])) {
			if($parents[0] != $this->publication["id"]) {
				throw new BeditaException("Wrong publication: " . $parents[0]);
			}
			$oldBaseLevel = $this->baseLevel; 
			$this->baseLevel = true;
			foreach ($parents as $p) {
				if ($p != $this->publication["id"]) {
					$pathArr[$p] = $this->loadObj($p);
					if ($pathArr[$p] === self::UNLOGGED || $pathArr[$p] === self::UNAUTHORIZED)
						throw new BeditaFrontAccessException(null, array("errorType" => $pathArr[$p]));
				}
			}
			$this->baseLevel = $oldBaseLevel;
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
		$parents_id = $this->BeTree->getParents($object_id, $this->publication["id"]);
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
	 * @param unknown_type $secName section id or section nickname
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
		$items = $this->loadSectionObjects($section_id);
		unset($this->modelBindings);
		$this->sectionOptions['itemsByType'] = $oldItemsByType;
		
		$archive = array();
		
		foreach ($items as $type => $itemGroup) {
		
			if($type != "toolbar") {
			
				foreach ($itemGroup as $item) {
					
					// DateItem, pubblication or creation date
					if(!empty($item["DateItem"][0]["start"]))
						$refDate = $item["DateItem"][0]["start"];
					else
						$refDate = isset($item["start"])? $item["start"] : $item["created"];
					 
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
						$countYear += count($i);
						$archive[$type][$year][$key]["total"] = count($i);
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
	 * @param bool $cloud, if true set 'class' key 
	 * 			(possible value: smallestTag, largestTag, largeTag, mediumTag, smallTag)
	 */
	public function loadTags($tplVar=null, $cloud=true) {
		$tplVar = (empty($tplVar))? "listTags" : $tplVar;
		$category = ClassRegistry::init("Category");
		$this->set($tplVar, $category->getTags(false, $this->status, $cloud));
	}
	
	/**
	 * find all objects tagged by $name and set results for view
	 * 
	 * @param string $name
	 */
	public function tag($name) {
		$this->baseLevel = true;
		$this->set("tag",$this->loadObjectsByTag($name));
		$this->baseLevel = false;
	}
	
	/**
	 * return objects for a specific tag
	 *
	 * @param string $tag tag label
	 * @params array $options search options
	 * 				"section" => name or id section
	 * 				"filter" => particular filter
	 * 				"order", "dir", "dim", "page" used like pagination parameters
	 * @return array
	 */
	protected function loadObjectsByTag($tag, $options=array()) {
		
		$section_id = null;
		if (!empty($options["section"])) {
			$section_id = (is_numeric($options["section"]))? $options["section"] : $this->BEObject->getIdFromNickname($options["section"]);
			$this->checkParentStatus($section_id);
		}
		
		// remove '+' from $tag, if coming from url
		$tag = strtolower(str_replace("+", " ", $tag));
		
		$tagDetail = ClassRegistry::init("Category")->find("first", array(
					"conditions" => array("name" => $tag, "object_type_id IS NULL", "status" => $this->status)
				)
			);
		
		if (empty($tagDetail))
			throw new BeditaException(__("No tag found", true));
		
		$options = array_merge($this->tagOptions, $options, $this->getPassedArgs());
		$filter = (!empty($options["filter"]))? $options["filter"] : false;
		$filter["tag"] = $tag;
		$order = "";
		if (!empty($options["order"])) {
			$order = $options["order"];
		} elseif (!empty($section_id)) {
			$order = "`Tree`.priority";
		}
		$dir = (isset($options["dir"]))? $options["dir"] : 1;
		$page = (!empty($options["page"]))? $options["page"] : 1;
		$dim = (!empty($options["dim"]))? $options["dim"] : 100000;
		
		// add rules for start and end pubblication date
		if ($this->checkPubDate == true) {
			if (empty($filter["Content.start"]))
				$filter["Content.start"] = "<= '" . date("Y-m-d") . "' OR `Content`.start IS NULL";
			if (empty($filter["Content.end"]))
				$filter["Content.end"] = ">= '" . date("Y-m-d") . "' OR `Content`.end IS NULL";
		}
		
		$contents = $this->BeTree->getChildren($section_id, $this->status, $filter, $order, $dir, $page, $dim);
		
		$result = $tagDetail;

		foreach ($contents["items"] as $c) {
			$object = $this->loadObj($c["id"],false);
			if ($object !== self::UNAUTHORIZED) {
				if ($this->sectionOptions["itemsByType"])
					$result[$object['object_type']][] = $object;
				else
					$result["items"][] = $object;
			}
		}
		
		return array_merge($result, array("toolbar" => $contents["toolbar"]));
	}
	
	/**
	 * load annotation referenced to some object
	 * 
	 * @param string $annotationType, object type of the annotation e.g. "comment"
	 * @param $objectName, reference object nickname or id 
	 * @param array $options, specific options (pagination, filter) that override annotationOptions attribute
	 * @return array of annotations
	 */
	protected function loadAnnotations($annotationType, $objectName, $options=array()) {
		
		if (empty($annotationType) || empty($objectName))
			throw new BeditaException(__("Annotation type or object_id missing", true));
		
		$object_id = (is_numeric($objectName))? $objectName : $this->BEObject->getIdFromNickname($objectName);
		
		$options = array_merge($this->annotationOptions[$annotationType], $options, $this->getPassedArgs());
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
			$object = $this->loadObj($a["id"],false);
			if ($object !== self::UNAUTHORIZED)
				$result[Configure::read("objectTypes." . $annotationType . ".model")][] = $object;
		}
		return array_merge($result, array("toolbar" => $annotations["toolbar"]));
	}
	
	/**
	 * force download of media object
	 * 
	 * @param $name id or object nickname
	 */
	public function download($name) {
		if(empty($name))
			throw new BeditaException(__("Content not found", true));
		
		$id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
		$object_type_id = $this->BEObject->findObjectTypeId($id);
		// verify type
		$conf = Configure::getInstance() ;
		$types = array($conf->objectTypes['image']['id'], $conf->objectTypes['video']['id'],
			$conf->objectTypes['befile']['id'], $conf->objectTypes['audio']['id'], $conf->objectTypes['application']['id']);
		if(($object_type_id === false) || !in_array($object_type_id, $types))
			throw new BeditaException(__("Content not found", true));

		$obj = $this->loadObj($id);
		if ($obj === self::UNLOGGED || $obj === self::UNAUTHORIZED)
			throw new BeditaFrontAccessException(null, array("errorType" => $obj));
		
		// check 'download' relation
		// TODO: check relatedObject status????
		$objRel = ClassRegistry::init("ObjectRelation");
		$relatedObjectId = $objRel->find('first', 
				array('conditions' => array("ObjectRelation.id" => $id, 
						"ObjectRelation.switch" => "download"), 'fields' => array('object_id')));
		if($relatedObjectId === false) {
			throw new BeditaException(__("Content not found", true));
		}

		// media with provider or file on filesystem? TODO: use DS?? 
		if(!empty($obj['provider']) || $obj['path'][0] !== "/") {
			$this->redirect($obj['path']);
		}

		// TODO: for some extensions or mime-types redirect to media URL
		if(isset($conf->redirectMimeTypesDownload) && 
			in_array($obj['mime_type'], $conf->redirectMimeTypesDownload)) {
			$this->redirect($conf->mediaUrl.$obj['path']);
		}
			
		$path = ($conf->mediaRoot).$obj['path'];
		$f = new File($path);
		$info = $f->info();
		if(isset($conf->redirectExtensionsDownload) && 
				in_array($info['extension'], $conf->redirectExtensionsDownload)) {
			$this->redirect($conf->mediaUrl.$obj['path']);
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
		header('Content-Length: ' . $obj['size']);
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
	 * If it's ajax request then if not empty $this->params["form"]["render"] renders it 
	 * 
	 * elseif  it's not ajax request then redirect to referer
	 *  
	 */
	public function saveComment() {
		if (!empty($this->data)) {
			if(!isset($this->Comment)) {
				$this->Comment = $this->loadModelByType("Comment");
			}
			$this->data["title"] = substr($this->data["description"],0,30) . "...";
			// for comment status check contents.comments 
			$beObject = ClassRegistry::init("BEObject");
			$commentsFlag = $beObject->field("comments", array("id" => $this->data['object_id']));
			if($commentsFlag == 'moderated') {
				 $this->data["status"] = "draft";
				 $userMsgOK = "Your message has been sent and it's waiting approval.";
			} else if ($commentsFlag == 'on'){
				 $this->data["status"] = 'on';
				 $userMsgOK = "Your message has been saved.";
			} else {
				 throw new BeditaException(__("Post comment disabled", true));
			}

			try {
				// check IP
				$bannedIP = ClassRegistry::init("BannedIp");
        		if($bannedIP->isBanned($_SERVER['REMOTE_ADDR'])) {
					throw new BeditaException(__("Error saving comment", true));
        		}
				
				// check captcha				
				if(!isset($this->Captcha)) {
					App::import('Component', 'Captcha');
					$this->Captcha = new CaptchaComponent();
					$this->Captcha->startup($this);
				}
				$this->Captcha->checkCaptcha();
				
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
		
		if (!isset($this->RequestHandler)) {
			App::import("Component", "RequestHandler");
			$this->RequestHandler = new RequestHandlerComponent();
			$this->RequestHandler->initialize($this);
			$this->RequestHandler->startup($this);
		}
		// if it's ajax call no redirect by referer
		if($this->RequestHandler->isAjax()) { 
			$this->layout = "ajax";
			if (!empty($this->params["form"]["render"])) { 
				$this->render(null, null, $this->params["form"]["render"]);
			}
		} else {
			$urlToRedirect = $this->referer();
			if (!empty($error))
				$urlToRedirect .= "/#error";
			elseif ($commentsFlag == 'on')
				$urlToRedirect .= "/#comment-".$this->Comment->id; 
			$this->redirect($urlToRedirect);
		}

	}
	
	public function printme($id=null, $printLayout=null) {
		if (!empty($this->params["form"]["id"]))
			$id = $this->params["form"]["id"];
		if (!empty($this->params["form"]["printLayout"]))
			$id = $this->params["form"]["printLayout"];
		$objectData = $this->loadObj($id);
		$this->layout = "print";
		$this->set("printLayout", $printLayout);
		$this->set("object", $objectData);
		if (file_exists(APP."views".DS."pages".DS.$printLayout.".tpl"))
			$this->render($printLayout);
		else
			$this->render("print");		
	}
	
	protected function save($modelName=null) {
		if (!$this->logged)
			throw new BeditaFrontAccessException(null, array("errorType" => self::UNLOGGED));
		try {
			if (empty($modelName) && empty($this->data["object_type_id"]))
				throw new BeditaException(__("no object type defined",true));
			$modelName = (empty($modelName))? Configure::read("objectTypes.".$this->data["object_type_id"].".model") : $modelName;
			$objectModel = ClassRegistry::init($modelName);
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
	
	protected function delete() {
		if (!$this->logged)
			throw new BeditaFrontAccessException(null, array("errorType" => self::UNLOGGED));
		try {
			if (!empty($this->data["object_type_id"])) {
				$object_type_id = $this->data["object_type_id"];
			} elseif (!empty($this->data["id"])) {
				$object_type_id = $this->BEObject->findObjectTypeId($this->data["id"]);
			} else {
				throw new BeditaException(__("no object type defined",true));
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
	 * if one or more parents haven't status IN $this->status array throw a BeditaException
	 * 
	 * @param int $section_id
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
			
			if ($countParent != $countParentStatus)
				throw new BeditaException(__("Content not found", true));
		}
	}
	
	/**
	 * get passed args by name and return
	 *
	 */
	private function getPassedArgs() {
		$args = array();
		if (!empty($this->passedArgs)) {
			foreach ($this->passedArgs as $key => $val) {
				if (!is_numeric($key)) {
					$args[$key] = $val;
				}
			}
		}
		return $args;
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
}

?>