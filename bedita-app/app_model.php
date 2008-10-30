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
 * @link			http://www.bedita.com
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
	 * Collassa il risultato di un record in un array unico
	 * 
	 * @param array record	record da collassare
	 * 
	 * @return array		record collassato
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
	 * Default text format
	 *
	 * @param unknown_type $value
	 * @return unknown
	 */
	protected function getDefaultTextFormat($value = null) {
		$labels = array('html', 'txt', 'txtParsed') ;
		if(isset($value) && in_array($value, $labels)) 
			return $value ;
		$conf = Configure::getInstance() ;
		return ((isset($conf->type))?$conf->type:'') ;
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
	 * 							"search" => "text to search"
	 * 							....
	 *
	 *							reserved filter words:
	 *							"category" => "val" search by category id or category name
	 *							"relation" => "val" search by object_relations swicth
	 *							"rel_object_id" => "val" search object relateds to a particular object (object_relation object_id)
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
		
		if (!empty($id)) {
			$fields .= ", `Tree`.*";
			$from .= ", trees AS `Tree`";
			$conditions[] = " `Tree`.`id`=`BEObject`.`id`" ;
			if (!empty($userid))
				$conditions[] 	= " prmsUserByID ('{$userid}', Tree.id, ".BEDITA_PERMS_READ.") > 0 " ;
			
			if($all)
				$conditions[] = " path LIKE (CONCAT((SELECT path FROM trees WHERE id = {$id}), '/%')) " ;
			else
				$conditions[] = array("parent_id" => $id) ;
		} else {
			if (!empty($userid))
				$conditions[] 	= " prmsUserByID ('{$userid}', `BEObject`.id, ".BEDITA_PERMS_READ.") > 0 " ;
		}

		if (!empty($status))
			$conditions[] = array('status' => $status) ;
		
		if(!empty($excludeIds))
			$conditions["NOT"] = array(array("`BEObject`.id" => $excludeIds));
			
		list($otherFields, $otherFrom, $otherConditions, $otherGroup, $otherOrder) = $this->getSqlItems($filter);
		
		if (!empty($otherFields))
			$fields = $fields . $otherFields;
			
		$conditions = array_merge($conditions, $otherConditions);
		$from .= $otherFrom;
		$groupClausole .= $otherGroup; 
		
		// build sql conditions
		$db 		 =& ConnectionManager::getDataSource($this->useDbConfig);
		$sqlClausole = $db->conditions($conditions, true, true) ;

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
		} else {
			$ordClausole = "";
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
			if (!empty($tmp[$i]["Content"]) && empty($tmp[$i]["Content"]["id"]))
				unset($tmp[$i]["Content"]);
			$recordset['items'][] = $this->am($tmp[$i]);
		}

		return $recordset ;
	}

	/**
	 * set conditions, from, fields, group and order from $filter
	 *
	 * @param array $filter
	 * @return array
	 */
	private function getSqlItems($filter) {
		$conditions = array();
		$from = "";
		$fields = "";
		$group = "";
		$order = "";
		
		if (array_key_exists("search", $filter)) {
			$fields = ", `SearchText`.`object_id` AS `oid`, SUM( MATCH (`SearchText`.`content`) AGAINST ('".$filter["search"]."') * `SearchText`.`relevance` ) AS `points`";
			$from .= ", search_texts AS `SearchText`";
			$conditions[] = "`SearchText`.`object_id` = `BEObject`.`id` AND MATCH (`SearchText`.`content`) AGAINST ('".$filter["search"]."')";
//			$group  .= ", `SearchText`.`object_id`";
			$order .= "points DESC ";
			unset($filter["search"]);	
		}
		
		if (array_key_exists("category", $filter)) {
			$cat_field = (is_numeric($filter["category"]))? "id" : "name";
			$from .= ", categories AS `Category`, object_categories AS `ObjectCategory`";
			$conditions[] = "`Category`." . $cat_field . "='" . $filter["category"] . "' 
							AND `ObjectCategory`.object_id=`BEObject`.id
							AND `ObjectCategory`.category_id=`Category`.id
							AND `Category`.object_type_id IS NOT NULL";
			unset($filter["category"]);
		}
		
		if (array_key_exists("relation", $filter)) {
			$filter["ObjectRelation.switch"] = $filter["relation"];
			unset($filter["relation"]);
		}
			
		if (array_key_exists("rel_object_id", $filter)) {
			$filter["ObjectRelation.object_id"] = $filter["rel_object_id"];
			unset($filter["rel_object_id"]);
		}
		
		$beObject = ClassRegistry::init("BEObject");
		
		foreach ($filter as $key => $val) {
			if ($beObject->hasField($key))
				$key = "`BEObject`." . $key;
			
			$fields .= ", " . $key;
			if (is_array($val)) 
				$conditions[] = $key . " IN (" . implode(",", $val) . ")";
			elseif (!empty($val))
				$conditions[] = $key . "='" . $val . "'";

			if (count($arr = explode(".", $key)) == 2 ) {
				$modelName = $arr[0];
				if (!strstr($modelName,"BEObject") && $modelName != "Content") {
					$model = ClassRegistry::init($modelName);
					$f_str = $model->useTable . " as `" . $model->alias . "`";
					// create join with BEObject
					if (empty($from) || !strstr($from, $f_str)) {
						$from .= ", " . $f_str;
						$conditions[] = "`BEObject`.id=`" . $model->alias . "`.id";
					}
				}
				
			}
		}
		return array($fields, $from ,$conditions, $group, $order);
	}
}

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
/**
 * Classe utilizata per le viste nn fa niente 
 * un solo metodo: afterFind
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
		
	/**
 	* Sovrascrive e poi chiama la funzione del parent xch� deve settare
 	* ove necessario, il tipo di oggetto da salvare
 	*/
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
		 * @todo VERIFICARE se nn da problemi.
		 * azzero il valore di BEObject->{BEObject->primaryKey} per 
		 * permettere salvataggi sucessivi.
		 */
		/*
		if(isset($this->BEObject)) {
			$this->BEObject->{$this->BEObject->primaryKey} = false ;
		}
		*/
		return $result ;
	}
	
	/**
	 * Un oggetto crea un clone di se stesso.
	 *
	 */
	function __clone() {
		$className 	= get_class($this) ;
		$_this		= new $className() ;

		// Se non e' settato nessun oggetto, esce
		if(@empty($this->{$this->primaryKey})) {
			$this->copyPropertiesFromObj($_this);

			return ;
		}

		$i = 0 ;

		// Preleva i dati dell'oggetto
		$data = $this->findById($this->{$this->primaryKey}) ;

		// Formatta i dati
		$_this->_formatDataForClone($data, $this) ;

		/**
		 * NOTA.
		 * Se il primo campo dati non e' un array, il salvataggio funziona correttamente.
		 * funzioni model:save -->  model::set --> model::countDim
		 */
		$tmp = array("title" => $data["title"]) ;
		$data = am($tmp, $data) ;

		// Salva i dati, in caso d'errore, esce
		if(!$_this->save($data)) {
			$this->copyPropertiesFromObj($_this);

			return ;
		}

		// copia i permessi
		if(!isset($this->Permission)) {
			if(!class_exists('Permission')){
				loadModel('Permission');
			}
			$this->Permission = new Permission() ;
		}
		$this->Permission->clonePermissions($this->{$this->primaryKey}, $this->{$_this->primaryKey}) ;

		// copia le proprieta' oggetto clonato
		$this->copyPropertiesFromObj($_this);
	}

	private function copyPropertiesFromObj(&$obj) {
		foreach ($obj as $key => $item) {
			$this->{$key} = $item ;
		}
	}

	/**
	 * Formatta i dati per la creazione di un clone, ogni tipo
	 * di oggetto esegue operazioni specifiche richiamando, sempre
	 * parent::_formatDataForClone.
	 *
	 * @param array $data		Dati da formattare
	 * @param object $source	Oggetto sorgente
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
		
		// Scorre le associazioni hasMany
		foreach ($this->hasMany as $name => $assoc) {
			$db 		=& ConnectionManager::getDataSource($this->useDbConfig);
			$model 		= new $assoc['className']() ; 
			
			// Cancella le precedenti associazioni
			$table 		= (isset($model->useTable)) ? $model->useTable : ($db->name($db->fullTableName($assoc->className))) ;
			$id 		= (isset($this->data[$this->name]['id'])) ? $this->data[$this->name]['id'] : $this->getInsertID() ;		
			$foreignK	= $assoc['foreignKey'] ;
			
			$db->query("DELETE FROM {$table} WHERE {$foreignK} = '{$id}'");
			
			// Se non ci sono dati da salvare esce
			if (!isset($this->data[$this->name][$name]) || !(is_array($this->data[$this->name][$name]) && count($this->data[$this->name][$name]))) 
				continue ;
			
			// Salva le nuove associazioni
			$size = count($this->data[$this->name][$name]) ;
			for ($i=0; $i < $size ; $i++) {
				$modelTmp	 	 = new $assoc['className']() ; 
				$data 			 = &$this->data[$this->name][$name][$i] ;
				$data[$foreignK] = $id ; 
				if(!$modelTmp->save($data)) 
					return false ;
				
				unset($modelTmp);
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
 * Bedita content model relations
**/

class BeditaContentModel extends BEAppObjectModel {
	
	public $searchFields = array("title" => 10 , "description" => 6, 
		"subject" => 4, "abstract" => 4, "body" => 4);	
	
	function beforeValidate() {
    	return $this->validateContent();
    }
		
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
														"Category"),
									"Content"),
				"default" => array("BEObject" => array(	"CustomProperties", 
														"LangText", 
														"ObjectType"), 
									"Content"),
				"minimum" => array("BEObject" => array("ObjectType"), "Content")		
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
														"Category"),
									"Content", "Stream"),
				"default" => array("BEObject" => array(	"CustomProperties", 
														"LangText", 
														"ObjectType"), 
									"Content", "Stream"),
				"minimum" => array("BEObject" => array("ObjectType"),"Content", "Stream")		
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


////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 */
class BeditaCollectionModel extends BEAppObjectModel {

	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject'),
			'DeleteDependentObject'	=> array('section'),
			'DeleteObject' 			=> 'objects',
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
	 * Se true esegue la clonazione anche dei figli, altrimenti no
	 *
	 * @var boolean
	 */
	var $recursionClone = true ;

	/**
	 * ID del'oggetto da clonare
	 *
	 * @var integer
	 */
	var $oldID ;


	/**
	 * Preleva i figli di cui id e' radice.
	 * Se l'userid e' presente, preleva solo gli oggetti di cui ha i permessi, se '' � un utente anonimo,
	 * altrimenti li prende tutti.
	 * Si possono selezionare i tipi di oggetti da prelevare.
	 *
	 * @param integer $id		id della radice da selezionare.
	 * @param string $userid	l'utente che accede. Se null: non controlla i permessi. Se '': utente guest.
	 * 							Default: non verifica i permessi.
	 * @param string $status	Prende oggetti solo con lo status passato
	 * @param array $filter		definisce i tipi gli oggetti da prelevare. Es.:
	 * 							1, 3, 22 ... aree, sezioni, documenti.
	 * 							Default: tutti.
	 * @param integer $page		Numero di pagina da selezionare
	 * @param integer $dim		Dimensione della pagina
	 */
	function getChildren($id = null, $userid = null, $status = null, $filter = false, $page = 1, $dim = 100000) {
		if(!class_exists('Tree')) loadModel('Tree');
		$tree 	=& new Tree();

		$tree->setRoot($this->id);
		return $tree->getChildren($id, $userid, $status, $filter, $page, $dim)  ;
	}


	function __clone() {
		if(!class_exists('Tree')) loadModel('Tree');

		$oldID 		= $this->id ;
		$recursion 	= (isset($this->recursionClone)) ? $this->recursionClone : true ;

		// Clona l'oggetto
		parent::__clone();
		$this->oldID 			= $oldID ;
		$this->recursionClone 	= $recursion ;

		// Clona ricorsivamente i figli
		if($this->recursionClone) {
			$this->insertChildrenClone() ;
		}
	}

	protected function insertChildrenClone() {
		$conf  	= Configure::getInstance() ;

		$tree 	=& new Tree();

		// Preleva l'elenco dei contenuti
		if(!($queries = $tree->getAll($this->oldID))) throw new BEditaErrorCloneException("BEAppCollectionModel::getItems") ;

		// crea le nuove associazioni
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

		$tree =& new Tree();
		$ret = $tree->appendChild($id, (isset($idParent)?$idParent:$this->id)) ;
		if($priority!=null)
			$tree->setPriority($id,$priority,(isset($idParent)?$idParent:$this->id)) ;
		return $ret ;
	}

	function removeChildren($idParent = null) {
		if(!class_exists('Tree')) loadModel('Tree');

		$tree =& new Tree();
		$ret = $tree->removeChildren((isset($idParent)?$idParent:$this->id)) ;
		
		return $ret ;
	}

}

////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 *
 * Eccezione sollevata quando i tenta di clonare un oggetto che non si puo' clonare
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
 * Eccezione sollevata quando avviene un errore nella clonazione
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