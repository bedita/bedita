<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008, 2010 ChannelWeb Srl, Chialab Srl
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
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BuildFilterBehavior extends ModelBehavior {
 	
	private $fields = "";
	private $from = "";
	private $conditions = array();
	private $group = "";
	private $order = "";
	private $filter = array();
	protected $startQuote = ""; // internal use: start quote
	protected $endQuote = ""; // internal use: end quote
	private $model = "";
	
	
	function setup(&$model, $settings=array()) {
    	$this->model = $model;
		if(empty($this->sQ)) {
			$db = ConnectionManager::getDataSource($model->useDbConfig);
			$this->startQuote = $db->startQuote;
			$this->endQuote = $db->endQuote;
    	}
	}
	
	/**
	 * set conditions, from, fields, group and order from $filter
	 *
	 * @param array $filter
	 * @return array
	 */
	function getSqlItems(&$model, $filter) {
		$this->initVars($filter);
		$s = $this->startQuote;
		$e = $this->endQuote;
		
		$beObject = ClassRegistry::init("BEObject");
		
		// *CUSTOM QUERY*
		foreach ($this->filter as $key => $val) {
			
			if (method_exists($this, $key . "Filter")) {
				$this->{$key . "Filter"}($s, $e, $val);
			} else {
			
				if ($beObject->hasField($key)) {
					$key = "{$s}BEObject{$e}." . $s . $key . $e;
				} else {
					
					if(strstr($key, ".*")) {
						$mod = str_replace(".*", "", $key);
						$this->group .= "," . $this->model->fieldsString($mod);
						$key = $s . $mod . $e . ".*";
					} else {
						$key = $s . str_replace(".", "{$e}.{$s}", $key) . $e;
					}					
				}
				
				$this->fields .= ", " . $key;
				if (is_array($val)) {
					$this->conditions[] = $key . " IN (" . implode(",", $val) . ")";
				} elseif (!empty($val)) {
					$this->conditions[] = (preg_match("/^[<|>]/", $val))? "(".$key." ".$val.")" : $key . "='" . $val . "'";
				}
	
				if (count($arr = explode(".", $key)) == 2 ) {
					$modelName = str_replace(array($s, $e) ,"",$arr[0]);
					if (!strstr($modelName,"BEObject") && $modelName != "Content") {
						$model = ClassRegistry::init($modelName);
						$f_str = $s. $model->useTable . $e. " as " . $s. $model->alias . $e. "";
						// create join with BEObject
						if (empty($this->from) || !strstr($this->from, $f_str)) {
							$this->from .= ", " . $f_str;
							if (empty($model->hasOne["BEObject"]) && $model->hasField("object_id") && $model->alias != "ObjectRelation")
								$this->conditions[] = "{$s}BEObject{$e}.{$s}id{$e}={$s}" . $model->alias . "{$e}.{$s}object_id{$e}";
							else
								$this->conditions[] = "{$s}BEObject{$e}.{$s}id{$e}={$s}" . $model->alias . "{$e}.{$s}id{$e}";							
						}
					}
					
				}
				
			}
		}
		return array($this->fields, $this->from ,$this->conditions, $this->group, $this->order);
	}
	
	private function initVars(array $filter) {
		$this->fields = "";
		$this->from = "";
		$this->conditions = array();
		$this->group = "";
		$this->order = "";
		
		if (array_key_exists("relation", $filter)) {
			$filter["ObjectRelation.switch"] = $filter["relation"];
			unset($filter["relation"]);
		}
			
		if (array_key_exists("rel_object_id", $filter)) {
			$filter["ObjectRelation.object_id"] = $filter["rel_object_id"];
			unset($filter["rel_object_id"]);
		}
		if (array_key_exists("ref_object_details", $filter)) {
			$mod = $filter["ref_object_details"];
			$found = false;
			foreach ($filter as $k => $v) {
				if(strstr($k, $mod.".")) {
					$found = true;
				}
			}
			if(!$found) {
				$filter[$mod.".*"] = "";
			}
		}
		$this->filter = $filter;
	}
	
	private function object_userFilter($s, $e) {
		$this->fields .= ", ObjectUser.user_id AS user_id";
		$this->from = " LEFT OUTER JOIN object_users AS ObjectUser ON BEObject.id=ObjectUser.object_id" . $this->from;
	}
	
	private function count_annotationFilter($s, $e, $value) {
		if (!is_array($value)) {
			$value = array($value);
		}
		
		if (!empty($this->filter["object_type_id"])) {
			$object_type_id = $this->filter["object_type_id"];
		} elseif (!empty($this->filter["BEObject.object_type_id"])) {
			$object_type_id = $this->filter["BEObject.object_type_id"];
		}
		
		foreach ($value as $key => $annotationType) {
			$annotationModel = ClassRegistry::init($annotationType);
			$refObj_type_id = Configure::read("objectTypes." . Inflector::underscore($annotationModel->name) . ".id");
			$numOf = "num_of_" . Inflector::underscore($annotationModel->name);
			$this->fields .= ", SUM(" . $numOf . ") AS " . $numOf;
			$from = " LEFT OUTER JOIN (
						SELECT DISTINCT {$s}BEObject{$e}.{$s}id{$e}, COUNT({$s}" . $annotationModel->name . "{$e}.{$s}id{$e}) AS " . $numOf ."
						FROM {$s}objects{$e} AS {$s}BEObject{$e} 
						LEFT OUTER JOIN {$s}annotations{$e} AS {$s}" . $annotationModel->name . "{$e} ON {$s}BEObject{$e}.{$s}id{$e}={$s}" . $annotationModel->name . "{$e}.{$s}object_id{$e}
						RIGHT OUTER JOIN {$s}objects{$e} AS {$s}RefObj{$e} ON ({$s}RefObj{$e}.{$s}id{$e} = {$s}" . $annotationModel->name . "{$e}.{$s}id{$e} AND {$s}RefObj{$e}.{$s}object_type_id{$e}=" . $refObj_type_id . ")
					";
			if (!empty($object_type_id)) {
				$from .= (is_array($object_type_id))? "WHERE {$s}BEObject{$e}.{$s}object_type_id{$e} IN (" . implode(",", $object_type_id) . ")" : "WHERE {$s}BEObject{$e}.{$s}object_type_id{$e}=".$object_type_id;
			}
			
			$from .= " GROUP BY {$s}BEObject{$e}.{$s}id{$e}
					) AS {$s}".$annotationModel->name."{$e} ON {$s}".$annotationModel->name."{$e}.{$s}id{$e} = {$s}BEObject{$e}.{$s}id{$e}";
			
			$this->from = $from . $this->from;
		}
	}
	
	private function mediatypeFilter($s, $e) {
		$this->fields .= ", Category.name AS mediatype";
		$this->from = " LEFT OUTER JOIN object_categories AS ObjectCategory ON BEObject.id=ObjectCategory.object_id
				LEFT OUTER JOIN categories AS Category ON ObjectCategory.category_id=Category.id AND Category.object_type_id IS NOT NULL"
				. $this->from;
	}
	
	private function queryFilter($s, $e, $value) {
		$this->fields .= ", SearchText.object_id AS oid, SUM( MATCH (SearchText.content) AGAINST ('" . $value . "') * SearchText.relevance ) AS points";
		$this->from .= ", search_texts AS SearchText";
		$this->conditions[] = "SearchText.object_id = BEObject.id AND SearchText.lang = BEObject.lang AND MATCH (SearchText.content) AGAINST ('" . $value . "')";
		$this->order .= "points DESC ";
	}
	
	private function categoryFilter($s, $e, $value) {
		$cat_field = (is_numeric($value))? "id" : "name";
		if (!strstr($this->from, Category) && !array_key_exists("mediatype", $this->filter))
			$this->from .= ", categories AS Category, object_categories AS ObjectCategory";
		$this->conditions[] = "Category." . $cat_field . "='" . $value . "' 
						AND ObjectCategory.object_id=BEObject.id
						AND ObjectCategory.category_id=Category.id
						AND Category.object_type_id IS NOT NULL";
	}
	
	private function tagFilter($s, $e, $value) {
		$cat_field = (is_numeric($value))? "id" : "name";
		if (!strstr($this->from, Category) && !array_key_exists("mediatype", $this->filter))
			$this->from .= ", categories AS Category, object_categories AS ObjectCategory";
		$this->conditions[] = "Category." . $cat_field . "='" . $value . "' 
						AND ObjectCategory.object_id=BEObject.id
						AND ObjectCategory.category_id=Category.id
						AND Category.object_type_id IS NULL";
	}
	
	private function rel_detailFilter($s, $e, $value) {
		if (!empty($value)) {
			if (!isset($this->filter["ObjectRelation.switch"]))
				$this->filter["ObjectRelation.switch"] = "";
			$this->fields .= ", RelatedObject.*";
			$this->from .= ", objects AS RelatedObject";
			$this->conditions[] = "ObjectRelation.object_id=RelatedObject.id";
			$this->order .= ( (!empty($this->order))? "," : "" ) . "ObjectRelation.priority";
		}		
	}
	
	private function ref_object_detailsFilter($s, $e, $value) {
		if (!empty($value)) {
			$refFields = $this->model->fieldsString("BEObject", "ReferenceObject");
			$this->fields .= ", $refFields";
			$this->from .= ", {$s}objects{$e} AS {$s}ReferenceObject{$e}";
			$this->conditions[] = $s . ClassRegistry::init($value)->alias . "{$e}.{$s}object_id{$s}={$s}ReferenceObject{$e}.{$s}id{$e}";
			$this->group .= "," . $refFields;
		}
	}
	
	private function mail_groupFilter($s, $e, $value) {
		$this->from .= ", mail_group_cards AS MailGroupCard";
		$this->conditions[] = "MailGroupCard.mail_group_id='" . $value . "' 
						AND MailGroupCard.card_id=BEObject.id";
	}

	private function user_createdFilter($s, $e) {
		$locFields = ", {$s}User{$e}.{$s}userid{$e}, {$s}User{$e}.{$s}realname{$e}";
		$this->fields .= $locFields;
		$this->from .= ", {$s}users{$e} AS {$s}User{$e}";
		$this->conditions[] = "{$s}User{$e}.{$s}id{$e}={$s}BEObject{$e}.{$s}user_created{$e}";
		$this->group .= $locFields;
	}

	/**
	 * count objects' permissions
	 *
	 * @param mixed $value, if it's integer then count $value permissions
	 */
	private function count_permissionFilter($s, $e, $value) {
		$this->fields .= ", COUNT({$s}Permission{$e}.{$s}id{$e}) AS num_of_permission";
		$from = " LEFT OUTER JOIN {$s}permissions{$e} as {$s}Permission{$e} ON {$s}Permission{$e}.{$s}object_id{$e} = {$s}BEObject{$e}.{$s}id{$e}";
		if (is_numeric($value)) {
			$from .= " AND {$s}Permission{$e}.{$s}flag{$e} = " . $value;
		}
		$this->from = $from . $this->from;
	}
}
 
?>