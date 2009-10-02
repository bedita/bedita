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
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

class AppModel extends Model{

	var $actsAs 	= array("Containable");
}

/**
 * Bedita model base class
 */
class BEAppModel extends AppModel {

	protected $modelBindings = array();
	
	/**
	 * Merge record result in one array
	 * 
	 * @param array record	record data
	 * @return array		record merged to single array
	 */
	function am($record) {
		$tmp = array() ;
		foreach ($record as $key => $val) {
			if(is_array($val)) $tmp = array_merge($tmp, $val) ;
			else $tmp[$key] = $val ;
		}
		
		return $tmp ;
	}

	/**
	 * Get SQL date format
	 *
	 * @param unknown_type $value
	 * @return unknown
	 */
	public function getDefaultDateFormat($value = null) {
		if(is_integer($value)) return date("Y-m-d", $value) ;
		
		if(is_string($value) && !empty($value)) {
			$conf = Configure::getInstance() ;			
			$d_pos = strpos($conf->dateFormatValidation,'dd');
			$m_pos = strpos($conf->dateFormatValidation,'mm');
			$y_pos = strpos($conf->dateFormatValidation,'yyyy');
			$value = substr($value, $y_pos, 4) . "-" . substr($value, $m_pos, 2) . "-" . substr($value, $d_pos, 2);
			return $value ;
		}
		
		return null ;
	}
	
	/**
	 * Check date field in $this->data[ModelName][$key] -> set to null if empty or call getDefaultDateFormat
	 *
	 * @param string $key
	 */
	protected function checkDate($key) {
		$data = &$this->data[$this->name];
		if(empty($data[$key])) {
			$data[$key] = null;
		} else {
			$data[$key] = $this->getDefaultDateFormat($data[$key]);
		}
	}

	/**
	 * Check float/double field in $this->data[ModelName][$key] -> set to null if empty
	 *
	 * @param string $key
	 */
	protected function checkFloat($key) {
		$data = &$this->data[$this->name];
		if(empty($data[$key])) {
			$data[$key] = null;
		}
	}

	/**
	 * Check integer/generic number in $this->data[ModelName][$key] -> set to null if empty
	 *
	 * @param string $key
	 */
	protected function checkNumber($key) {
		$data = &$this->data[$this->name];
		if(empty($data[$key])) {
			$data[$key] = null;
		}
	}
	
	protected function checkDuration($key) {
		$data = &$this->data[$this->name];
		if(empty($data[$key]) || !is_numeric($data[$key])) {
			$data[$key] = null;
		} else {
			$data[$key] = $data[$key]*60;
		}
	}
		
	/**
	 * Object search Toolbar
	 *
	 * @param integer 	$page		
	 * @param integer 	$dimPage	
	 * @param mixed 	$sqlCondition	sql search condition
	 * @param boolean 	$recursive	TRUE, retrieve connected objects
	 * @param mixed 	$sqlCondition	sql search condition
	 * @return array
	 */
	function toolbar($page = null, $dimPage = null, $size=0) {
						
		$toolbar = array("first" => 0, "prev" => 0, "next" => 0, "last" => 0, "size" => 0, "pages" => 0, "page" => 0, "dim" => 0) ;
		
		if(!$page || empty($page)) $page = 1 ;
		if(!$dimPage || empty($dimPage)) $dimPage = $size ;
		
		$pageCount = $size / $dimPage ;
		settype($pageCount,"integer");
		if($size % $dimPage) $pageCount++ ;
		
		$toolbar["pages"] 	= $pageCount ;
		$toolbar["page"]  	= $page ;
		$toolbar["dim"]  	= $dimPage ;
		
		if($page == 1) {
			if($page >= $pageCount) {
				// Una sola
				
			} else {
				// Prima pagina
				$toolbar["next"] = $page+1 ;
				$toolbar["last"] = $pageCount ;
			} 
		} else {
			if($page >= $pageCount) {
				// Ultima
				$toolbar["first"] = 1 ;
				$toolbar["prev"] = $page-1 ;
			} else {
				// Pagina di mezzo
				$toolbar["next"] = $page+1 ;
				$toolbar["last"] = $pageCount ;
				$toolbar["first"] = 1 ;
				$toolbar["prev"] = $page-1 ;
			}
		}

		$toolbar["start"]	= (($page-1)*$dimPage)+1 ;
		$toolbar["end"] 	= $page * $dimPage ;
		if($toolbar["end"] > $size) $toolbar["end"] = $size ;
		
		$toolbar["size"] = $size ;
		
		return $toolbar ;	
	}

	/**
	 * SQL limit clausole
	 *
	 * @param int $page, page num
	 * @param int $dim, global size/count
	 * @return string
	 */
	protected function getLimitClausole($page = 1, $dim = 100000) {
		$offset = ($page > 1) ? (($page -1) * $dim) : null;
		return isset($offset) ? "$offset, $dim" : "$dim" ;
	}

	public function containLevel($level = "minimum") {
		if(!isset($this->modelBindings[$level])) {
			throw new BeditaException("Contain level not found: $level");
		}
		$this->contain($this->modelBindings[$level]);
	}
	
	/**
	 * perform an objects search
	 * 
	 * @param integer $id		root id, if it's set perform search on the tree
	 * @param string $userid	user: null (default) => no permission check. ' ' => guest/anonymous user,
	 * @param string $status	object status
	 * @param array  $filter	example of filter: 
	 * 							"object_type_id" => array(21,22,...),
	 *							"ModelName.fieldname => "value",
	 * 							"query" => "text to search"
	 * 							....
	 *
	 *							reserved filter words:
	 *							"category" => "val" search by category id or category name
	 *							"relation" => "val" search by object_relations swicth
	 *							"rel_object_id" => "val" search object relateds to a particular object (object_relation object_id)
	 *							...
	 *							see all in BuildFilter behavior
	 *
	 * @param string $order		field to order result (id, status, modified..)
	 * @param boolean $dir		true (default), ascending, otherwiese descending.
	 * @param integer $page		Page number (for pagination)
	 * @param integer $dim		Page dim (for pagination)
	 * @param boolean $all		true: all tree levels (discendents), false: only first level (children)
	 * @param array $excludeIds Array of id's to exclude
	 */
	public function findObjects($id=null, $userid=null, $status=null, $filter=array(), $order=null, $dir=true, $page=1, $dim=100000, $all=false, $excludeIds=array()) {
		
		$fields  = "DISTINCT `BEObject`.*, `Content`.*" ;
		$from = "objects as `BEObject` LEFT OUTER JOIN contents as `Content` ON `BEObject`.id=`Content`.id";
		$conditions = array();
		$groupClausole = "GROUP BY `BEObject`.id";
		
		if (!empty($status))
			$conditions[] = array('`BEObject`.status' => $status) ;
		
		if(!empty($excludeIds))
			$conditions["NOT"] = array(array("`BEObject`.id" => $excludeIds));
		
		// get specific query elements
		if (!$this->Behaviors->attached('BuildFilter')) {
			$this->Behaviors->attach('BuildFilter');
		}
		
		list($otherFields, $otherFrom, $otherConditions, $otherGroup, $otherOrder) = $this->getSqlItems($filter);

		if (!empty($otherFields))
			$fields = $fields . $otherFields;
			
		$conditions = array_merge($conditions, $otherConditions);
		$from .= $otherFrom;
		$groupClausole .= $otherGroup; 
		
		if (!empty($id)) {
			$fields .= ", `Tree`.*";
			$from .= ", trees AS `Tree`";
			$conditions[] = " `Tree`.`id`=`BEObject`.`id`" ;
//			if (!empty($userid))
//				$conditions[] 	= " prmsUserByID ('{$userid}', Tree.id, ".BEDITA_PERMS_READ.") > 0 " ;
			
			if($all)
				$conditions[] = " path LIKE (CONCAT((SELECT path FROM trees WHERE id = {$id}), '/%')) " ;
			else
				$conditions[] = array("`Tree`.parent_id" => $id) ;
			
			if(empty($order)) {
				$order = "`Tree`.priority";
				$section = ClassRegistry::init("Section");
				$priorityOrder = $section->field("priority_order", array("id" => $id));
				if(empty($priorityOrder))
					$priorityOrder = "asc";
				$dir = ($priorityOrder == "asc");
			}
				
		} else {
//			if (!empty($userid))
//				$conditions[] 	= " prmsUserByID ('{$userid}', `BEObject`.id, ".BEDITA_PERMS_READ.") > 0 " ;
		}
		
		// if $order is empty and not performing search then set a default order
		if (empty($order) && empty($filter["search"])) {
			$order = "`BEObject`.id";
			$dir = false;
		}
	
		// build sql conditions
		$db 		 =& ConnectionManager::getDataSource($this->useDbConfig);
		$sqlClausole = $db->conditions($conditions, true, true) ;

		$ordClausole = "";
		if(is_string($order) && strlen($order)) {
			$beObject = ClassRegistry::init("BEObject");
			if ($beObject->hasField($order))
				$order = "`BEObject`." . $order;
			$ordItem = "{$order} " . ((!$dir)? " DESC " : "");
			if(!empty($otherOrder)) {
				$ordClausole = "ORDER BY " . $ordItem .", " . $otherOrder;
			} else {
				$ordClausole = " ORDER BY {$order} " . ((!$dir)? " DESC " : "") ;
			}
		} elseif (!empty($otherOrder)) {
			$ordClausole = "ORDER BY {$otherOrder}";
		}
		
		$limit 	= $this->getLimitClausole($page, $dim) ;
		$query = "SELECT {$fields} FROM {$from} {$sqlClausole} {$groupClausole} {$ordClausole} LIMIT {$limit}";
		$tmp  	= $this->query($query) ;

		if ($tmp === false)
			throw new BeditaException(__("Error finding objects", true));
		
		$queryCount = "SELECT COUNT(DISTINCT `BEObject`.id) AS count FROM {$from} {$sqlClausole}";

		$tmpCount = $this->query($queryCount);
		if ($tmpCount === false)
			throw new BeditaException(__("Error counting objects", true));
		
		$size = (empty($tmpCount[0][0]["count"]))? 0 : $tmpCount[0][0]["count"];
		
		$recordset = array(
			"items"		=> array(),
			"toolbar"	=> $this->toolbar($page, $dim, $size) );
		for ($i =0; $i < count($tmp); $i++) {
			$tmpToAdd = array();
			if (!empty($tmp[$i]["Content"]) && empty($tmp[$i]["Content"]["id"]))
				unset($tmp[$i]["Content"]);
				
			if (!empty($tmp[$i]["RelatedObject"])) {
				$tmpToAdd["RelatedObject"] = $tmp[$i]["RelatedObject"];
				unset($tmp[$i]["RelatedObject"]);
			}
			
			if (!empty($tmp[$i]["ReferenceObject"])) {
				$tmpToAdd["ReferenceObject"] = $tmp[$i]["ReferenceObject"];
				unset($tmp[$i]["ReferenceObject"]);
			}
			
			if (!empty($tmp[$i]["DateItem"])) {
				$tmpToAdd["DateItem"] = $tmp[$i]["DateItem"];
				unset($tmp[$i]["DateItem"]);
			}
			
			$recordset['items'][] = array_merge($this->am($tmp[$i]), $tmpToAdd);
		}
		
		return $recordset ;
	}

}

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
/**
 * Class user for views
 */
class _emptyAfterFindView {
	function afterFind($result) { return $result ; }
}

class BEAppObjectModel extends BEAppModel {
	var $recursive 	= 2 ;
	
	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject'),
			'DeleteObject' 			=> 'objects',
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
			$data['BEObject']['object_type_id'] = $conf->objectTypes[strtolower($this->name)]["id"] ;
		} else if(!isset($data['object_type_id']) || empty($data['object_type_id'])) {
			$data['object_type_id'] = $conf->objectTypes[strtolower($this->name)]["id"] ;
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
		
		/**
		 * TODO: verify
		 * reset value of BEObject->{BEObject->primaryKey} to enable to save successively.
		 */
		/*
		if(isset($this->BEObject)) {
			$this->BEObject->{$this->BEObject->primaryKey} = false ;
		}
		*/
		return $result ;
	}
	
	function __clone() {
		$className 	= get_class($this) ;
		$_this		= new $className() ;

		if(@empty($this->{$this->primaryKey})) {
			$this->copyPropertiesFromObj($_this);

			return ;
		}

		$i = 0 ;

		// Get object data
		$data = $this->findById($this->{$this->primaryKey}) ;

		// Prepare data
		$_this->_formatDataForClone($data, $this) ;

		/**
		 * If first field is not an array, it saves correctly.
		 * functions model:save -->  model::set --> model::countDim
		 */
		$tmp = array("title" => $data["title"]) ;
		$data = am($tmp, $data) ;

		// Salva i dati, in caso d'errore, esce
		if(!$_this->save($data)) {
			$this->copyPropertiesFromObj($_this);

			return ;
		}

		$this->copyPropertiesFromObj($_this);
	}

	private function copyPropertiesFromObj(&$obj) {
		foreach ($obj as $key => $item) {
			$this->{$key} = $item ;
		}
	}

	/**
	 * Prepare data to create clone
	 *
	 * @param array $data		Data to prepare
	 * @param object $source	Source object
	 */
	protected function _formatDataForClone(&$data, $source = null) {

		$labels = array($this->primaryKey, 'user_created', 'user_modified', 'Index') ;
		foreach($labels as $label) unset($data[$label]) ;

		/**
		 * Gestione di dati specifici a diversi tipi di oggetti.
	 	*/
		for($i=0; isset($data['calendars']) && $i < count($data['calendars']) ; $i++) {
			unset($data['calendars'][$i]['id']) ;
		}

		for($i=0; isset($data['links']) && $i < count($data['links']) ; $i++) {
			unset($data['links'][$i]['id']) ;
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
					if(!$model->save($data)) 
						return false ;
					
				}
			}
		}
		
		return true ;
	}
	
    /**
     * default values for Contents
     */     
    protected function validateContent() {
    	$this->checkDate('start');
    	$this->checkDate('end');
    	$this->checkDuration('duration');
        return true ;
    }

    public function checkType($objTypeId) {
    	return ($objTypeId == Configure::read("objectTypes.".strtolower($this->name).".id"));
    }
    
    public function getTypeId() {
        return Configure::read("objectTypes.".strtolower($this->name).".id");
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Bedita simple object
**/

class BeditaSimpleObjectModel extends BEAppObjectModel {

	public $searchFields = array("title" => 10 , "description" => 6);		
	public $useTable = 'objects';

	public $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave',
	); 
	
	public $hasOne= array();
}

/**
 * Bedita content model relations
**/

class BeditaContentModel extends BEAppObjectModel {
	
	public $searchFields = array("title" => 10 , "description" => 6, 
		"subject" => 4, "abstract" => 4, "body" => 4);	
	
	function beforeValidate() {
    	return $this->validateContent();
    }
		
}

/**
 * Bedita annotation model
**/

class BeditaAnnotationModel extends BEAppObjectModel {
	
	public $searchFields = array("title" => 10 , "description" => 6, 
		"body" => 4, "author" => 3);	

	var $belongsTo = array(
		"ReferenceObject" =>
			array(
				'className'		=> 'BEObject',
				'foreignKey'	=> 'object_id',
			),
	);
	
	var $actsAs 	= array(
			'CompactResult' 		=> array("ReferenceObject"),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject'),
			'DeleteObject' 			=> 'objects',
	); 
	
	protected $modelBindings = array( 
		"detailed" =>  array("BEObject" => array("ObjectType","UserCreated"), "ReferenceObject"),
		"default" =>  array("BEObject" => array("ObjectType","UserCreated"), "ReferenceObject"),
		"minimum" => array("BEObject" => array("ObjectType"))
	);
	
}



class BeditaSimpleStreamModel extends BEAppObjectModel {

	public $searchFields = array("title" => 10 , "description" => 6, 
		"subject" => 4, "abstract" => 4, "body" => 4, "name" => 6);	

	protected $modelBindings = array( 
				"detailed" => array("BEObject" => array("ObjectType",
														"Permissions",
														"UserCreated", 
														"UserModified",
														"RelatedObject",
														"Annotation",
														"Category"),
									"Content"),
				"default" => array("BEObject" => array(	"ObjectProperty", 
														"LangText", 
														"ObjectType",
														"Annotation",
														"Category"), 
									"Content"),
				"minimum" => array("BEObject" => array("ObjectType","Category"), "Content")		
	);
	
	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave'		=> array(),
			'ForeignDependenceSave' => array('BEObject', 'Content'),
			'DeleteObject' 			=> 'objects',
	); 

	var $hasOne= array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' =>
				array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
	);

    function beforeValidate() {
        return $this->validateContent();
    }
        
	function __clone() {
		throw new BEditaCloneModelException($this);
	}		
}


class BeditaStreamModel extends BEAppObjectModel {

	public $searchFields = array("title" => 10 , "description" => 6, 
		"subject" => 4, "abstract" => 4, "body" => 4, "name" => 6);	
	
	protected $modelBindings = array( 
				"detailed" => array("BEObject" => array("ObjectType",
														"Permissions",
														"UserCreated", 
														"UserModified",
														"RelatedObject",
														"Category",
														"ObjectProperty",
														"Annotation"),
									"Content", "Stream"),
				"default" => array("BEObject" => array(	"ObjectProperty", 
														"LangText", 
														"ObjectType",
														"Category",
														"Annotation"), 
									"Content", "Stream"),
				"minimum" => array("BEObject" => array("ObjectType","Category"),"Content", "Stream")		
	);
	
	
	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave'		=> array(),
			'ForeignDependenceSave' => array('BEObject', 'Content', 'Stream'),
			'DeleteObject' 			=> 'objects',
	); 

	var $hasOne= array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' =>
				array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Stream' =>
				array(
					'className'		=> 'Stream',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
	);
	
    function beforeValidate() {
        return $this->validateContent();
    }
        
	function __clone() {
		throw new BEditaCloneModelException($this);
	}		
}

/**
 * Base class for products
 *
 */
class BeditaProductModel extends BEAppObjectModel {
	
	public $searchFields = array("title" => 10 , "description" => 6, 
		"abstract" => 4, "body" => 4);	
	
		protected $modelBindings = array( 
				"detailed" => array("BEObject" => array("ObjectType",
														"Permissions",
														"UserCreated", 
														"UserModified",
														"RelatedObject",
														"Category",
														"Annotation"),
									"Product"),
				"default" => array("BEObject" => array(	"ObjectProperty", 
														"LangText", 
														"ObjectType"), 
									"Product"),
				"minimum" => array("BEObject" => array("ObjectType"),"Product")		
	);
	
	
	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave'		=> array(),
			'ForeignDependenceSave' => array('BEObject', 'Product'),
			'DeleteObject' 			=> 'objects',
	); 

	var $hasOne= array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Product' =>
				array(
					'className'		=> 'Product',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				)
	);	
}


////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 */
class BeditaCollectionModel extends BEAppObjectModel {

	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject'),
			'DeleteDependentObject'	=> array('section'),
			'DeleteObject' 			=> 'objects'
	); 
	var $recursive 	= 2 ;

	var $hasOne = array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				)
	) ;			
	
	/**
	 * If true recursive clonation (children clonation)
	 *
	 * @var boolean
	 */
	var $recursionClone = true ;

	/**
	 * ID of the object to clone
	 *
	 * @var integer
	 */
	var $oldID ;


	/**
	 * Get children of the object whose id is $id.
	 * If $userid is present, get objects for whom $userid has permitions, if $userid is '' anonymous user.
	 * Result filtered by object type/s $filter, and status $status
	 *
	 * @param integer $id		
	 * @param string $userid	If null: no permitions check. If '': user guest.
	 * 							Default: no permitions check.
	 * @param string $status	Status of the objects to get
	 * @param array $filter		Types of the objects to get. Ex.:
	 * 							1, 3, 22 ... publications, sections, documents.
	 * 							Default: all.
	 * @param integer $page		Number of page to select
	 * @param integer $dim		Dimension of the page
	 */
	function getChildren($id = null, $userid = null, $status = null, $filter = false, $page = 1, $dim = 100000) {
		if(!class_exists('Tree')) loadModel('Tree');
		$tree 	= new Tree();

		$tree->setRoot($this->id);
		return $tree->getChildren($id, $userid, $status, $filter, $page, $dim)  ;
	}


	function __clone() {
		if(!class_exists('Tree')) loadModel('Tree');

		$oldID 		= $this->id ;
		$recursion 	= (isset($this->recursionClone)) ? $this->recursionClone : true ;

		parent::__clone();
		$this->oldID 			= $oldID ;
		$this->recursionClone 	= $recursion ;

		// Clone children recursively
		if($this->recursionClone) {
			$this->insertChildrenClone() ;
		}
	}

	protected function insertChildrenClone() {
		$conf  	= Configure::getInstance() ;

		$tree 	= new Tree();

		// Get contents list
		if(!($queries = $tree->getAll($this->oldID))) throw new BEditaErrorCloneException("BEAppCollectionModel::getItems") ;

		// create new associations
		for ($i=0; $i < count($queries[0]["children"]) ; $i++) {
			$className	= $conf->objectTypes[$queries[0]["children"][$i]['object_type_id']]["model"] ;
			$item 		= new $className() ;
			$item->id	= $queries[0]['children'][$i]['id'] ;

			$clone = clone $item ;

			if(!$tree->appendChild($clone->id, $this->id)) {
				throw new BEditaErrorCloneException("BEAppCollectionModel::appendChild") ;
			}
		}

	}

	function appendChild($id, $idParent = null, $priority = null) {
		if(!class_exists('Tree')) loadModel('Tree');

		$tree = new Tree();
		$ret = $tree->appendChild($id, (isset($idParent)?$idParent:$this->id)) ;
		if($priority!=null)
			$tree->setPriority($id,$priority,(isset($idParent)?$idParent:$this->id)) ;
		return $ret ;
	}

}

////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 *
 * Exception on cloning a not clonable object
 *
 */
class BEditaCloneModelException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($model, $code  = 0) {

        // make sure everything is assigned properly
        parent::__construct($model->name, $code);
    }
}

/**
 *
 * Exception on clonation
 *
 */
class BEditaErrorCloneException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($msg, $code  = 0) {

        // make sure everything is assigned properly
        parent::__construct($model->name, $code);
    }
}

?>