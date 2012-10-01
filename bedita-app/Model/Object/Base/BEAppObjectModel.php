<?php

App::uses('BEAppModel', 'Model');

/**
 * BEdita base app object class. BEdita objects should extend BEAppObjectModel
 */
class BEAppObjectModel extends BEAppModel {
	var $recursive 	= 2 ;

	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave',
			'RevisionObject',
			'ForeignDependenceSave' => array('BEObject'),
			'DeleteObject' 			=> 'objects',
			'Notify'
	);

	var $hasOne= array(
			'BEObject' =>
			array(
				'className'		=> 'BEObject',
				'conditions'   => '',
				'foreignKey'	=> 'id',
				'dependent'		=> true
			)
		);

	public $objectTypesGroups = array();

	/**
	 * Overrides field, don't use CompactResult in field()
	 *
	 * @param string $name
	 * @param array $conditions
	 * @param string $order
	 */
	public function field($name, $conditions = null, $order = null) {

		$compactEnabled = $this->Behaviors->enabled('CompactResult');
		if ($compactEnabled) {
			$this->Behaviors->disable('CompactResult');
		}
		$res = parent::field($name, $conditions, $order);
		if ($compactEnabled) {
			$this->Behaviors->enable('CompactResult');
		}
		return $res;
	}

	/**
	 * Overrides saveField, don't use CompactResult in saveField()
	 *
	 * @param string $name
	 * @param array $conditions
	 * @param string $order
	 */
	public function saveField($name, $value, $validate = false) {

		$dependanceEnabled = $this->Behaviors->enabled('ForeignDependenceSave');
		if ($dependanceEnabled) {
			$this->Behaviors->disable('ForeignDependenceSave');
		}
		$res = parent::saveField($name, $value, $validate);
		if ($dependanceEnabled) {
			$this->Behaviors->enable('ForeignDependenceSave');
		}
		return $res;
	}

	function save($data = null, $validate = true, $fieldList = array()) {
		$conf = Configure::getInstance() ;

		if(isset($data['BEObject']) && empty($data['BEObject']['object_type_id'])) {
			$data['BEObject']['object_type_id'] = $conf->objectTypes[Inflector::underscore($this->name)]["id"] ;
		} else if(!isset($data['object_type_id']) || empty($data['object_type_id'])) {
			$data['object_type_id'] = $conf->objectTypes[Inflector::underscore($this->name)]["id"] ;
		}

		// Se c'e' la chiave primaria vuota la toglie
		if(isset($data[$this->primaryKey]) && empty($data[$this->primaryKey])) {
			unset($data[$this->primaryKey]) ;
		}

		$data = (!empty($data[$this->alias]))? $data : array($this->alias => $data);

		// format data array for HABTM relations in cake way
		if (!empty($this->hasAndBelongsToMany)) {
			foreach ($this->hasAndBelongsToMany as $key => $val) {
				if (!empty($data[$this->alias][$key][$key])) {
					$data[$key][$key] = $data[$this->alias][$key][$key];
					unset($data[$this->alias][$key]);
				} elseif (!empty($data[$this->alias][$key])) {
					$data[$key][$key] = $data[$this->alias][$key];
					unset($data[$this->alias][$key]);
				} elseif ( (isset($data[$this->alias][$key]) && is_array($data[$this->alias][$key]))
							|| (isset($data[$this->alias][$key][$key]) && is_array($data[$this->alias][$key][$key])) ) {
					$data[$key][$key] = array();
				}
			}
		}

		$result = parent::save($data, $validate, $fieldList) ;

		return $result ;
	}

	/**
	 * Clone a BEdita object.
	 * It should be called from a BEdita object model as Document, Section, etc...
	 *
	 * @param int $id, the BEdita object id
	 * @param array $options, see BEAppObjectModel::arrangeDataForClone
	 * @return type
	 */
	public function cloneObject($id, array $options = array()) {
		$this->containLevel("detailed");
		$data = $this->findById($id);
		$this->arrangeDataForClone($data, $options);
		$this->create();
		return $this->save($data);
	}

	/**
	 * Arrange an array to cloning a BEdita object
	 *
	 * @param array $data, should come from a find
	 * @param array $options, default values are:
	 *				"nicknameSuffix" => "", suffix to append at the original object nickname
	 *				"keepTitle" => false, true to keep the original object title
	 *				"keepUserCreated" => false, true to keep the original user created
	 */
	public function arrangeDataForClone(array &$data, array $options = array()) {
		$defaultOptions = array("nicknameSuffix" => "", "keepTitle" => false, "keepUserCreated" => false);
		$options = array_merge($defaultOptions, $options);
		$toUnset = array("id", "ObjectType", "SearchText", "UserCreated", "UserModified", "Version");
		if (!$options["keepUserCreated"]) {
			$toUnset[] = "user_created";
		}
		foreach ($toUnset as $label) {
			if (isset($data[$label])) {
				unset($data[$label]);
			}
		}
		if (isset($data["nickname"])) {
			$data["nickname"] .= $options["nicknameSuffix"];
		}
		if (!$options["keepTitle"]) {
			$data["title"] .= " - " . __("copy");
		}
		if (!empty($data["Permission"]) && is_array($data["Permission"])) {
			foreach ($data["Permission"] as &$perm) {
				if (isset($perm["object_id"])) {
					unset($perm["object_id"]);
				}
				unset($perm["ugid"]);
				unset($perm["id"]);
			}
		}
		if (!empty($data["ObjectProperty"])) {
			$objectProperty = array();
			foreach ($data["ObjectProperty"] as $op) {
				if (!empty($op["value"]["property_value"])) {
					$objectProperty[] = array(
						"property_type" => $op["property_type"],
						"property_id" => $op["id"],
						"property_value" => $op["value"]["property_value"]
					);
				}
			}
			$data["ObjectProperty"] = $objectProperty;
		}
		if (!empty($data["RelatedObject"])) {
			$relatedObject = array();
			foreach ($data["RelatedObject"] as $key => $value) {
				$relatedObject[$value["switch"]][] = array(
					"id" => $value["object_id"],
					"switch" => $value["switch"],
					"priority" => $value["priority"]
				);
			}
			$data["RelatedObject"] = $relatedObject;
		}
	}

	protected function updateHasManyAssoc() {

		foreach ($this->hasMany as $name => $assoc) {

			if (isset($this->data[$this->name][$name])) {
				$model 		= ClassRegistry::init($assoc['className']) ;

				// delete previous associations
				$id 		= (isset($this->data[$this->name]['id'])) ? $this->data[$this->name]['id'] : $this->getInsertID() ;
				$foreignK	= $assoc['foreignKey'] ;

				$model->deleteAll(array($foreignK => $id));

				// if there isn't data to save then exit
				if (!isset($this->data[$this->name][$name]) || !(is_array($this->data[$this->name][$name]) && count($this->data[$this->name][$name])))
					continue ;

				// save new associations
				$size = count($this->data[$this->name][$name]) ;
				for ($i=0; $i < $size ; $i++) {
					$model->create();
					$data 			 = &$this->data[$this->name][$name][$i] ;
					$data[$foreignK] = $id ;
					if(!$model->save($data)) {
						throw new BeditaException(__("Error saving associated data"), $data);
					}
				}
			}
		}

		return true ;
	}

    /**
     * default values for Contents
     */
    protected function validateContent() {
    	$this->checkDate('start_date');
    	$this->checkDate('end_date');
    	$this->checkDuration('duration');
        return true ;
    }

    public function checkType($objTypeId) {
    	return ($objTypeId == Configure::read("objectTypes.".Inflector::underscore($this->name).".id"));
    }

    public function getTypeId() {
        return Configure::read("objectTypes.".Inflector::underscore($this->name).".id");
    }
}

?>