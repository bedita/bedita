<?php
/**
 * Base frontend controller
 * @author ste@channelweb.it
 * @author dante@channelweb.it
 */
abstract class FrontendController extends AppController {

	private $status = array('on');
	protected $checkPubDate = true;
	private $currLang = null; // selected lang -- move in AppController?
	
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
		$conf = Configure::getInstance() ;
		if (!empty($conf->draft))
			$this->status[] = "draft";
	}

	/**
	 * Called in beforefilter...session, cookie, http agent...
	 *
	 */
	protected function setupLocale() {
		// setup curr lang
		if($this->currLang === null) {
			$this->currLang = $this->Session->read('Config.language');
			if($this->currLang === null) {
				$this->currLang = Configure::getInstance()->frontendLang;
			}
		}
		$this->set('currLang', $this->currLang);
	}

	public function changeLang($lang, $forward = null) {
		
		if (empty($lang)) {
			throw new BeditaException("No lang selected");
		}
		$conf = Configure::getInstance();
		if (!array_key_exists($lang, $conf->frontendLangs)) {
			throw new BeditaException(" lang selected");
		}
		$this->Session->write('Config.language', $lang);
		$this->Cookie->write($conf->cookieName["langSelect"], $lang, null, '+350 day'); 

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
		self::$current->handleError($ex->getDetails(), $ex->getMessage(), $errTrace);
		self::$current->render(null, "error", VIEWS."errors/error404.tpl");
	}
	
	public function handleError($eventMsg, $userMsg, $errTrace) {
		$this->log($errTrace);
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
			array($conf->objectTypes['section']), "priority") ;

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
			throw new BeditaException(__("Content not found"));
		
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
			throw new BeditaException(__("Content not found"));
		}
							
		if($this->checkPubDate && !$this->checkPubblicationDate($obj)) {
			throw new BeditaException(__("Content not found"));
		}

		if(!empty($obj["LangText"])) {
			$this->BeLangText->setupForView($obj["LangText"]) ;		
			$this->BeLangText->objectForLang($obj_id, $this->currLang, $obj);
		}
		
		if(!empty($obj["ObjectRelation"])) {
			$relations = $this->objectRelationArray($obj['ObjectRelation']);
			$obj['relations'] = $relations;
		}

		if(!empty($obj['gallery_id'])) {
			$obj['gallery_items'] = $this->loadGalleryItems($obj['gallery_id']);
		}
		
		$obj['object_type'] = $modelType;
		return $obj;
	}
	
	
	protected function loadGalleryItems($gallery_id) {

		$children = $this->BeTree->getChildren($gallery_id, $this->status, false, "priority") ;
		$multimedia=array();
		$conf = Configure::getInstance();
		foreach($children['items'] as $index => $object) {
			
			$modelType = $conf->objectTypeModels[$object['object_type_id']];
			if(!isset($this->{$modelType})) {
				$this->{$modelType} = $this->loadModelByType($modelType);
			}
			$this->modelBindings($this->{$modelType});
			$details = $this->{$modelType}->find("first", array(
								"conditions" => array(
									"BEObject.id" => $object['id'],
									"status" => $this->status
									)
							)
						);

			if (!$details) 
				continue ;
			if(!empty($details["LangText"])) {
				$this->BeLangText->setupForView($details["LangText"]) ;
			}
			$details['priority'] = $object['priority'];
			$details['filename'] = substr($details['path'], strripos($details['path'],"/")+1);
			$multimedia[$index] = $details;
		}
		return $multimedia;
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
		$items = $this->BeTree->getChildren($parent_id, $this->status);
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
			throw new BeditaException(__("Content not found"));
		
		$content_id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
		$section_id = $this->Tree->field('parent_id',"id = $content_id", "priority");
		if($section_id === false) {
			throw new BeditaException(__("Content not found"));
		}
		$this->action = 'section';
		$this->section($section_id, $content_id);	
	}

	public function section($secName, $contentName=null) {
		
		$sectionId = is_numeric($secName) ? $secName : $this->BEObject->getIdFromNickname($secName);		
		$section = $this->loadObj($sectionId);
		
		if(!empty($contentName)) {
			$content_id = is_numeric($contentName) ? $contentName : $this->BEObject->getIdFromNickname($contentName);
			$section['content'] = $this->loadObj($content_id);
		} else {
			$section['contents'] = $this->loadSectionObjects($sectionId);
		}
		$this->set('section', $section);
	}
	
	
	protected function showDraft() {
		$this->status[] = "draft";
	}
	
	public function getStatus() {
		return $this->status;
	}
}
?>