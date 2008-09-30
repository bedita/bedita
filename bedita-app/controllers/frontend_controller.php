<?php
/**
 * Base frontend controller
 * @author ste@channelweb.it
 * @author dante@channelweb.it
 */
abstract class FrontendController extends AppController {

	private $status = array('on', 'fixed');
	protected $checkPubDate = true;
	protected $showAllContents = false;

	protected function checkLogin() {
		return false; // every frontend has to implement checkLogin
	}
	
	/**
	 * $uses & $components array don't work... (abstract class ??)
	 */
	final protected function initAttributes() {
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
		if(!isset($this->BEObject)) {
			$this->BEObject = $this->loadModelByType('BEObject');
		}
		$conf = Configure::getInstance() ;
		if (!empty($conf->draft))
			$this->status[] = "draft";
	}

	/**
	 * Called in beforefilter...session, cookie, http agent...
	 *
	 */
	protected function setupLocale() {

		$this->currLang = $this->Session->read('Config.language');

		if($this->currLang === null || empty($this->currLang)) {
			$conf = Configure::getInstance();
			if (isset($conf->cookieName["langSelect"])) {
				$lang = $this->Cookie->read($conf->cookieName["langSelect"]);
			}
			if(!empty($lang)) {
				$this->currLang = $lang;
			} else {
				// HTTP autodetect
				$l10n = new L10n();
				$l10n->get();		
				$this->currLang = $conf->Config['language'];

				if(!array_key_exists($this->currLang, $conf->frontendLangs)) {
					if (isset($conf->frontendLangsMap)) {
						$lang = $conf->frontendLangsMap[$this->currLang];
						$this->currLang = (!empty($lang))? $lang : $conf->frontendLang;						
					} else {
						$this->currLang = $conf->frontendLang;
					}
					
				}
			}

			$this->Session->write('Config.language', $this->currLang);
			Configure::write('Config.language', $this->currLang);
		}
		$this->set('currLang', $this->currLang);
	}

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
	
	public static function handleExceptions(Exception $ex) {

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
	
	public function handleError($eventMsg, $userMsg, $errTrace) {
		if(Configure::read('debug') > 0) {
			$this->log($errTrace);
		}
	}
	
	/**
	* Get area's section recursively
	* 
	* @param integer $area_id			area parent
	* @param  string $var_name			name result in to template_ vars
	* @params array $exclude_nicknames	list exclude sections 
	* */
	protected function loadSectionsTree($parent_id,  $loadContents = false, array $exclude_nicknames = null) {

		$conf = Configure::getInstance(); 
		$result = array();
		$sections = $this->BeTree->getChildren($parent_id, $this->status, 
			array($conf->objectTypes['section']["id"]), "priority") ;

		foreach ($sections['items'] as $s) {
			
			if(!empty($exclude_nicknames) && in_array($s['nickname'], $exclude_nicknames)) 
				continue ;
			
			$sectionObject = $this->loadObj($s['id']);			
			if($loadContents) {
				$sectionObject['objects'] = $this->loadSectionObjects($s['id']);	
			}
			$sectionObject['sections'] = $this->loadSectionsTree($s['id'], $loadContents, $exclude_nicknames);
			$result[] = $sectionObject;
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
	protected function loadSectionsLevels($secName, $loadContents = false, array $exclude_nicknames = null) {
		$conf = Configure::getInstance(); 
		$result = array();
		
		$section_id = is_numeric($secName) ? $secName : $this->BEObject->getIdFromNickname($secName);
		
		$path = $this->Tree->field("path", array("id" => $section_id));
		$parents = explode("/", trim($path,"/"));
		
		$level = 0;
		foreach ($parents as $p_id) {
			$sections = $this->BeTree->getChildren($p_id, $this->status, 
				array($conf->objectTypes['section']["id"]), "priority") ;

			foreach ($sections["items"] as $s) {
				
				if(!empty($exclude_nicknames) && in_array($s['nickname'], $exclude_nicknames)) 
					continue ;
				
				$sectionObject = $this->loadObj($s['id']);
				
				if (in_array($s["id"], $parents)) {
				 	$sectionObject["selected"] = true;
				}
				
				if($loadContents) {
					$sectionObject['objects'] = $this->loadSectionObjects($s['id']);	
				}
				$result[$level][] = $sectionObject;
				
			}

			$level++;
		}
		return $result;
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
	   $channel = array( 'title' => $s['title'] , 
        'link' => "/rss/".$sectionName,
        'url' => "/rss/".$sectionName,
        'description' => $s['description'],
        'language' => $s['lang'],
       );
	   $this->set('channelData', $channel);
       $rssItems = array();
	   $items = $this->BeTree->getChildren($s['id'], $this->status, false, "priority");
	   if(!empty($items) && !empty($items['items'])) {
			foreach($items['items'] as $index => $item) {
				$obj = $this->loadObj($item['id']);
	            $rssItems[] = array( 'title' => $obj['title'], 'description' => $obj['description'],
	                'pubDate' => $obj['created']);
			}
		}
       $this->set('items', $rssItems);
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
	 * Load bedita Object, set view var with $var_name or object type (e.g. "Document", "Event"..)
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
	 * @return array
	 */
	protected function loadObj($obj_id) {
		if($obj_id === null)
			throw new BeditaException(__("Content not found", true));
		
		$modelType = $this->BEObject->getType($obj_id);
		if(!isset($this->{$modelType})) {
			$this->{$modelType} = $this->loadModelByType($modelType);
		}

		$this->modelBindings($this->{$modelType});
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

		if(!empty($obj["LangText"])) {
			$this->BeLangText->objectForLang($obj_id, $this->currLang, $obj);
		}
		
		if(!empty($obj["RelatedObject"])) {
			$obj['relations'] = $this->objectRelationArray($obj['RelatedObject'], $this->status);
			unset($obj["RelatedObject"]);
		}

		$obj['object_type'] = $modelType;
		return $obj;
	}

	/**
	 * Load and set objects in section $parent_id
	 *
	 * @param int $parent_id
	 */
	protected function loadAndSetSectionObjects($parent_id) {
		$sectionItems = $this->loadSectionObjects($parent_id);
		foreach($sectionItems as $key => $objs) {
			$this->set($key, $objs);
		}
	}

	/**
	 * Load and set objects in section $parentNick
	 *
	 * @param string $parentNick
	 */
	protected function loadAndSetSectionObjectsByNick($parentNick) {
		$sectionItems = $this->loadSectionObjectsByNick($parentNick);
		foreach($sectionItems as $key => $objs) {
			$this->set($key, $objs);
		}
	}
	
	protected function loadSectionObjectsByNick($parentNick) {
		return $this->loadSectionObjects($this->BEObject->getIdFromNickname($parentNick));
	}	

	/**
	 * Load objects in section $parent_id
	 *
	 * @param int $parent_id
	 * @return array
	 */
	protected function loadSectionObjects($parent_id) {

		if(empty($parent_id)) {
			throw new BeditaException("Bad data");
		}
		$sectionItems = array();
		$items = $this->BeTree->getChildren($parent_id, $this->status, false, "priority");
		if(!empty($items) && !empty($items['items'])) {
			foreach($items['items'] as $index => $item) {
				$obj = $this->loadObj($item['id']);
				$sectionItems[$obj['object_type']][] = $obj; 
			}
		}
		return $sectionItems;
	
	}
	
	
	public function content($name) {
		if(empty($name))
			throw new BeditaException(__("Content not found", true));
		
		$content_id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
		$section_id = $this->Tree->field('parent_id',"id = $content_id", "priority");
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
	 * if $contentName is defined set single content (default)
	 * if $contentName is defined and $this->showAllContents=true set content and other contents too
	 * 
	 * Execute 'sectionNickname'BeforeFilter and/or 'sectionNickName'BeforeRender 
	 * if they're set in the controller (i.e. pages_controller.php)				
	 *
	 * @param string/int $secName: section nick or section id
	 * @param string/int $contentName: content nick or content id
	 */
	public function section($secName, $contentName=null) {
		
		if (is_numeric($secName)) {
			$sectionId = $secName;
			$secName = $this->BEObject->getNicknameFromId($sectionId);
		} else {
			$sectionId = $this->BEObject->getIdFromNickname($secName);
		}		
		
		$secNameFilter = str_replace("-","_",$secName);
		// section before filter
		if (method_exists($this, $secNameFilter . "BeforeFilter")) {
			$this->{$secNameFilter . "BeforeFilter"}();
		}
		
		$section = $this->loadObj($sectionId);
		
		if(!empty($contentName)) {
			$content_id = is_numeric($contentName) ? $contentName : $this->BEObject->getIdFromNickname($contentName);
			$section['content'] = $this->loadObj($content_id);
			
			if ($this->showAllContents) {
				$section['contents'] = $this->loadSectionObjects($sectionId);
			}
		} else {
			$section['contents'] = $this->loadSectionObjects($sectionId);
		}
		$this->set('section', $section);
		
		// section after filter
		if (method_exists($this, $secNameFilter . "BeforeRender")) {
			$this->{$secNameFilter . "BeforeRender"}();
		}
	}
	
	/**
	 * build archive tree
	 *
	 * Array(
	 * 		"Document" => Array(
	 * 				"2008" => Array(
	 * 					"January" => Array(
	 * 						0 => document,
	 * 						1 => document,
	 * 						...
	 * 						"total" => number of document in january
	 * 						),
	 *	 				"February" => Array(...),
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
	public function loadArchiveTree($secName) {
		
		$section_id = (is_numeric($secName))? $secName : $this->BEObject->getIdFromNickname($secName);
		
		$monthName = array("01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May",
						   "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October",
						   "11" => "November", "12" => "December");
	
		$this->modelBindings['Document'] = array("BEObject" => array("LangText"));
		$this->modelBindings['ShortNews'] = array("BEObject" => array("LangText"));
		$this->modelBindings['Event'] = array("BEObject" => array("LangText"),"DateItem");
		
		$items = $this->loadSectionObjects($section_id);
		unset($this->modelBindings);
		
		$archive = array();
		
		foreach ($items as $type => $itemGroup) {
		
			foreach ($itemGroup as $item) {
		
				$data = explode("-", $item["start"]);
				$year = $data[0];
				$month = $monthName[$data[1]];
				$id = $item["id"];
				$item["title"] = (!empty($item["LangText"]["title"][$this->currLang]))? $item["LangText"]["title"][$this->currLang] : $item["title"];
				$archive[$type][$year][$month][] = $item;
			
			}
			
			// add number of items for month and year
			$countYear = 0;
			foreach ($archive[$type] as $year => $month) {
				
				$countYear = 0;
				foreach ($month as $key => $i) {
					$countYear += count($i);
					$archive[$type][$year][$key]["total"] = count($i);
				}
				$archive[$type][$year]["total"] = $countYear;
				
			}
	
		}

		return $archive;
	}
	
	
	/**
	 * load all tag
	 *
	 * @param string $tplVar
	 */
	public function loadTags($tplVar=null) {
		$tplVar = (empty($tplVar))? "listTags" : $tplVar;
		$category = ClassRegistry::init("Category");
		$this->set($tplVar, $category->getTags(false, $this->status));
	}
	
	/**
	 * return contents for a specific tag
	 *
	 * @param string $tag tag label 
	 * @return array
	 */
	protected function loadContentsByTag($tag) {
		$category = ClassRegistry::init("Category");
		// remove '+' from $tag, if coming from url
		$tag = str_replace("+", " ", $tag);
		$contents = $category->getContentsByTag($tag);
		$result = array();
		foreach ($contents as $c) {
			$object = $this->loadObj($c["id"]);
			$result[$object['object_type']][] = $object;
		}
		return $result;
	}
	
	/**
	 * show image for captch
	 *
	 */
	public function captchaImage() {	
		if(!isset($this->Captcha)) {
			App::import('Component', 'Captcha');
			$this->Captcha = new CaptchaComponent();
			$this->Captcha->startup($this);
		}
		$this->layout = null;
		$this->Captcha->image();
		$this->render = false;
	}
	
	public function saveComment() {
		if (!empty($this->data)) {
			if(!isset($this->Comment)) {
				$this->Comment = $this->loadModelByType("Comment");
			}
			$this->data["title"] = substr($this->data["abstract"],0,30) . "...";
			$this->data["status"] = "on";
			
			try {
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
				$this->userInfoMessage(__("Comment saved", true));
			} catch (BeditaException $ex) {
				$this->Transaction->rollback();
				$errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
				$this->log($errTrace);
				$this->userErrorMessage($ex->getMessage());
			}
	
		}
		$this->redirect($this->referer());

	}
	
	protected function showDraft() {
		$this->status[] = "draft";
	}
	
	public function getStatus() {
		return $this->status;
	}
}
?>