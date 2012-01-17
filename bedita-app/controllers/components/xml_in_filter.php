<?php

class XmlInFilterComponent extends Object {
	var $controller	;

	function startup(&$controller) {
		$this->controller = $controller;
	}
	
	function createObjects($fileName, array $options = array()) {
		
		App::import("Core", "Xml");
		$xml = new XML(file_get_contents($fileName));
		$treeModel = ClassRegistry::init("Tree");
		$nObj = 0;	
		$parsed = set::reverse($xml);				
		$objs = array();
		if(!empty($parsed["Section"]["ChildContents"])) {
			$objs = $parsed["Section"]["ChildContents"];
		} else if(!empty($parsed["Bedita"]["Objects"])) {
			$objs = $parsed["Bedita"]["Objects"];
		} else {
			$this->out("No contents found.");
			return;
		}
		if(!is_int(key($objs))) {
			$objs = array($objs);
		} 
		foreach ($objs as $data) {

			$objTypeId = isset($data['ObjectType']['name']) ?  
				Configure::read("objectTypes." . $data['ObjectType']['name'] . ".id") : $data['object_type_id']; 
			$modelType = Configure::read("objectTypes." . $objTypeId . ".model");
			$model = ClassRegistry::init($modelType);
			// $data = array_merge($data, $defaults);
			unset($data["id"]);
			$data["object_type_id"] = $objTypeId;
			$model->create();
			if(!$model->save($data)) {
				throw new BeditaException("Error saving object - " . print_r($data, true) . 
					" - validation: " . print_r($model->validationErrors, true));
			}
			if(!empty($options["sectionId"])) {
				$treeModel->appendChild($model->id, $options["sectionId"]);
			}
			$nObj++;		
		}
		return $nObj;
	}
};
