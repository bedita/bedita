<?php

App::uses('BeditaSimpleObjectModel', 'Model/Object/Base');

class BeditaObjectModel extends BeditaSimpleObjectModel {

	public $actsAs = array(
		'CompactResult' => array(),
		'SearchTextSave',
		'DeleteObject' => 'objects',
		'Notify'
	);

	public $hasOne = array(
		'BEObject' =>
			array(
				'className'		=> 'BEObject',
				'foreignKey'	=> 'id'
			)
	);

	protected $modelBindings = array(
				"detailed" =>  array("BEObject" => array("ObjectType",
															"UserCreated",
															"UserModified",
															"Permission",
															"ObjectProperty",
															"LangText",
															"RelatedObject",
															"Annotation",
															"Category"
															)),
				"default" => array("BEObject" => array("ObjectProperty",
									"LangText", "ObjectType", "Annotation",
									"Category", "RelatedObject" )),

				"minimum" => array("BEObject" => array("ObjectType"))
	);

	public function save($data = null, $validate = true, $fieldList = array()) {
		$conf = Configure::getInstance() ;

		$data2 = $data;

		foreach($data2 as $key => $value) {
			if (!is_array($value)){
				unset($data2[$key]);
			}
		}

		if(isset($data['BEObject']) && empty($data['BEObject']['object_type_id'])) {
			$data['BEObject']['object_type_id'] = $conf->objectTypes[Inflector::underscore($this->name)]["id"] ;
		} else if(!isset($data['object_type_id']) || empty($data['object_type_id'])) {
			$data['object_type_id'] = $conf->objectTypes[Inflector::underscore($this->name)]["id"] ;
		}

		if(isset($data[$this->primaryKey]) && empty($data[$this->primaryKey])) {
			unset($data[$this->primaryKey]) ;
		}

		$data = (!empty($data[$this->alias]))? array("BEObject" => $data[$this->alias]) : array("BEObject" => $data);

		$beObject = ClassRegistry::init("BEObject");

		// format data array for HABTM relations in cake way
		if (!empty($beObject->hasAndBelongsToMany)) {
			foreach ($beObject->hasAndBelongsToMany as $key => $val) {
				if (!empty($data[$beObject->alias][$key][$key])) {
					$data[$key][$key] = $data[$beObject->alias][$key][$key];
					unset($data[$beObject->alias][$key]);
				} elseif (!empty($data[$beObject->alias][$key])) {
					$data[$key][$key] = $data[$beObject->alias][$key];
					unset($data[$beObject->alias][$key]);
				} elseif ( (isset($data[$beObject->alias][$key]) && is_array($data[$beObject->alias][$key]))
							|| (isset($data[$beObject->alias][$key][$key]) && is_array($data[$beObject->alias][$key][$key])) ) {
					$data[$key][$key] = array();
				}
			}
		}

		$beObject->create();
		if (!$res = $beObject->save($data, $validate, $fieldList)) {
			return $res;
		}

		$data2["id"] = $beObject->id;
		$res = parent::save($data2, $validate, $fieldList);
		//$res = Model::save($data, $validate, $fieldList) ;
		//$res = ClassRegistry::init("Model")->save($data2, $validate, $fieldList);

		return $res;
	}

}

?>