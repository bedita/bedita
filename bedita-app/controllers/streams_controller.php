<?php

class StreamsController extends AppController {
	
	var $components = array('BeTree','BeFileHandler');
	var $uses = array('BEObject', 'Stream');
	var $helpers 	= array('BeTree', 'BeToolbar');
	
	/**
	 * get all or not related streams to an object 
	 *
	 * @param int $obj_id, if it's null get all streams else exclude related streams to $obj_id
	 * @param $collection, if it's set works on collection object (gallery,...)
	 * 					   else works on content object (document,... )
	 */
	public function showStreams($obj_id=null, $collection=0, $page = 1, $dim = 20) {
		$conf = Configure::getInstance();
		$ot  = array($conf->objectTypes['image'],
					$conf->objectTypes['audio'],
					$conf->objectTypes['video']
				);
		if (empty($collection)) {
			$ot[] = $conf->objectTypes['befile'];
		}
		$relations_id = array();
		if (!empty($obj_id)) {
			$relations_id = $this->getRelatedStreamIDs($obj_id, $ot, $collection);
		}
		
		$bedita_items = $this->BEObject->findObjs(null, null, $ot, $order=null, $dir=true, $page, $dim, $relations_id)  ;

		foreach($bedita_items['items'] as $key => $value) {
			$modelLoaded = $this->loadModelByObjectTypeId($value['object_type_id']);
			
			$modelLoaded->restrict(array(
									"BEObject" => array("ObjectType", 
														"LangText"
														),
									"ContentBase" => array("*"),
									"Stream"
									)
								);
			if(($Details = $modelLoaded->findById($value['id']))) {
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
				$bedita_items['items'][$key] = array_merge($bedita_items['items'][$key], $Details);	
			}
		}
		
		$this->layout = "empty";
		$this->set("bedita_items",$bedita_items['items']);
		$this->set('toolbar', 		$bedita_items['toolbar']);
		$this->set("collection", $collection);
		$this->set("object_id", $obj_id);
	}
	
	public function searchStreams($obj_id=null, $collection=0, $text=null) {
		$conf = Configure::getInstance();
		$ot  = array($conf->objectTypes['image'],
					$conf->objectTypes['audio'],
					$conf->objectTypes['video']
				);
		if (empty($collection)) {
			$ot[] = $conf->objectTypes['befile'];
		}
		$relations_id = array();
		if (!empty($obj_id)) {
			$relations_id = $this->getRelatedStreamIDs($obj_id, $ot, $collection);
		}
		$bedita_items = $this->Stream->search($text, $ot, $relations_id);
		$this->layout = "empty";
		$this->set("bedita_items", $bedita_items);
		$this->set("streamSearched", $text);
		$this->set("collection", $collection);
		$this->set("object_id", $obj_id);
	}
	
	/* Called by Ajax.
	 * Show multimedia object in the form page
	 * @param string $filename	File to show in the form page
	 */
	public function get_item_form($filename = null) {
		$filename = urldecode($this->params['form']['filename']) ;
		if(!($id = $this->Stream->getIdFromFilename($filename))) throw new BeditaException(sprintf(__("Error get id object: %d", true), $id));
		$this->_get_item_form($id) ;
	}
	 
	/**
	 * Called by Ajax.
	 * Show multimedia object in the form page
	 * @param integer $id	Id dell'oggetto da linkare
	 */
	public function get_item_form_by_id($id =null) {	
		$this->_get_item_form($this->params['form']['id']) ;
	}

	
	private function _get_item_form($id) {
		$conf  = Configure::getInstance() ;
		foreach ($this->params['form'] as $k =>$v) {
			$$k = $v ;
		}
		$rec = $this->BEObject->recursive ;
		$this->BEObject->recursive = -1 ;
		if(!($ret = $this->BEObject->read('object_type_id', $id))) throw new BeditaException(sprintf(__("Error get object: %d", true), $id));
		$this->BEObject->recursive = $rec ;
		$modelClass = $conf->objectTypeModels[$ret["BEObject"]["object_type_id"]];
		if(!class_exists($modelClass)){
			App::import('Model',$modelClass);
		}
		if (!class_exists($modelClass)) {
			throw new BeditaException(__("Object type not found - ", true).$modelClass);			
		}
		$this->{$modelClass} = new $modelClass();
		$this->{$modelClass}->restrict(array(
										"BEObject" => array("ObjectType"),
										"Stream"
										)
								);
		if(!($obj = $this->{$modelClass}->findById($id))) {
			 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
		}
		$imagePath 	= $this->BeFileHandler->path($id) ;
		$imageURL 	= $this->BeFileHandler->url($id) ;
		// data for template
		$this->set('object',	@$obj);
		$this->set('imagePath',	@$imagePath);
		$this->set('imageUrl',	@$imageURL);
		$this->set('priority',	@$priority);
		$this->set('objIndex',		@$index);
		$this->set('relation',	@$relation);
		$this->set('cols',		@$cols);
		$this->selfUrlParams = array("id", @$id);    
		$this->layout = "empty" ;
	}
	
	/**
	 * get related streams to an object and return an array of ids
	 *
	 * @param int $obj_id
	 * @param array $ot object types
	 * @param bool $collection
	 * @return array of related streams to $obj_id
	 */
	private function getRelatedStreamIDs($obj_id, $ot=null, $collection=false) {
		$conf = Configure::getInstance();
		$relations_id = array();
		$object = $this->BEObject->find("first", array(
														"restrict" 	=> array(),
														"fields" 	=> "object_type_id",
														"conditions" => "id=".$obj_id
													)
										);
		if (!$collection) {
			$modelLoaded = $this->loadModelByObjectTypeId($object["BEObject"]["object_type_id"]);
			$objRel = $modelLoaded->find("first",array(
													"restrict" => array("ContentBase" => "ObjectRelation"),
													"conditions" => "ContentBase.id=".$obj_id
												)
											);
			if (!empty($objRel["ObjectRelation"])) {
				foreach ($objRel["ObjectRelation"] as $rel) {
					$relations_id[] = $rel["id"];
				}
			}
			
		} else {
			$objRel = $this->BeTree->getChildren($obj_id, null, $ot, "priority") ;
				foreach($objRel['items'] as $rel) {
					$relations_id[] = $rel['id'];
				}
		}
		
		return $relations_id;
	}
	
	
	protected function forward($action, $esito) {
	 	 	$REDIRECT = array(
				"searchStreams" => array(
 								"OK"	=> self::VIEW_FWD.'show_streams',
	 							"ERROR"	=> self::VIEW_FWD.'show_streams'
	 						)
	 			);
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false;
	 }
}
?>