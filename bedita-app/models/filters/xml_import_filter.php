<?php

class XmlImportFilter extends BEAppModel 
{
	var $useTable = false;

	/**
	 * Import BE objects from XML source string
	 * @param string $source, XML source
	 * @param array $options, import options: "sectionId" => import objects in this section 
	 * @throws BeditaException
	 */
	function import($source, array $options = array()) {
		
		App::import("Core", "Xml");
		$xml = new XML($source);
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
			
			// remove fields to recreate
			$remove = array("id", "user_created", "user_modified", "created", "modified", "ip_created");
			foreach ($remove as $r) {
				unset($data[$r]);
			}

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
