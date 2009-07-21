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
 * @link			http://www.bedita.com
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
	
	
	function setup(&$model, $settings=array()) {
	}
	
	/**
	 * set conditions, from, fields, group and order from $filter
	 *
	 * @param array $filter
	 * @return array
	 */
	function getSqlItems(&$model, $filter) {
		$this->initVars($filter);
		
		$beObject = ClassRegistry::init("BEObject");
		
		foreach ($this->filter as $key => $val) {
			
			if (method_exists($this, $key . "Filter")) {
				$this->{$key . "Filter"}($val);
			} else {
			
				if ($beObject->hasField($key))
					$key = "`BEObject`." . $key;
				
				$this->fields .= ", " . $key;
				if (is_array($val)) {
					$this->conditions[] = $key . " IN (" . implode(",", $val) . ")";
				} elseif (!empty($val)) {
					$this->conditions[] = (preg_match("/^[<|>]/", $val))? "(".$key." ".$val.")" : $key . "='" . $val . "'";
				}
	
				if (count($arr = explode(".", $key)) == 2 ) {
					$modelName = $arr[0];
					if (!strstr($modelName,"BEObject") && $modelName != "Content") {
						$model = ClassRegistry::init($modelName);
						$f_str = $model->useTable . " as `" . $model->alias . "`";
						// create join with BEObject
						if (empty($this->from) || !strstr($this->from, $f_str)) {
							$this->from .= ", " . $f_str;
							if (empty($model->hasOne["BEObject"]) && $model->hasField("object_id"))
								$this->conditions[] = "`BEObject`.id=`" . $model->alias . "`.object_id";
							else
								$this->conditions[] = "`BEObject`.id=`" . $model->alias . "`.id";							
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
	
	private function object_userFilter() {
		$this->fields .= ", `ObjectUser`.user_id AS user_id";
		$this->from = " LEFT OUTER JOIN object_users AS `ObjectUser` ON `BEObject`.id=`ObjectUser`.object_id" . $this->from;
	}
	
	private function count_annotationFilter($value) {
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
			$refObj_type_id = Configure::read("objectTypes." . strtolower($annotationModel->name) . ".id");
			$numOf = "num_of_" . Inflector::underscore($annotationModel->name);
			$this->fields .= ", SUM(" . $numOf . ") AS " . $numOf;
			$from = " LEFT OUTER JOIN (
						SELECT DISTINCT `BEObject`.id, COUNT(`" . $annotationModel->name . "`.id) AS " . $numOf ."
						FROM objects AS `BEObject` 
						LEFT OUTER JOIN annotations AS `" . $annotationModel->name . "` ON `BEObject`.id=`" . $annotationModel->name . "`.object_id
						RIGHT OUTER JOIN objects AS `RefObj`ON (`RefObj`.id = `" . $annotationModel->name . "`.id AND `RefObj`.object_type_id=" . $refObj_type_id . ")
					";
			if (!empty($object_type_id)) {
				$from .= (is_array($object_type_id))? "WHERE `BEObject`.object_type_id IN (" . implode(",", $object_type_id) . ")" : "WHERE `BEObject`.object_type_id=".$object_type_id;
			}
			
			$from .= " GROUP BY `BEObject`.id
					) AS `".$annotationModel->name."` ON `".$annotationModel->name."`.id = `BEObject`.id";
			
			$this->from = $from . $this->from;
		}
	}
	
	private function mediatypeFilter() {
		$this->fields .= ", `Category`.name AS mediatype";
		$this->from = " LEFT OUTER JOIN object_categories AS `ObjectCategory` ON `BEObject`.id=`ObjectCategory`.object_id
				LEFT OUTER JOIN categories AS `Category` ON `ObjectCategory`.category_id=`Category`.id"
				. $this->from;
	}
	
	private function queryFilter($value) {
		$this->fields .= ", `SearchText`.`object_id` AS `oid`, SUM( MATCH (`SearchText`.`content`) AGAINST ('" . $value . "') * `SearchText`.`relevance` ) AS `points`";
		$this->from .= ", search_texts AS `SearchText`";
		$this->conditions[] = "`SearchText`.`object_id` = `BEObject`.`id` AND `SearchText`.`lang` = `BEObject`.`lang` AND MATCH (`SearchText`.`content`) AGAINST ('" . $value . "')";
		$this->order .= "points DESC ";
	}
	
	private function categoryFilter($value) {
		$cat_field = (is_numeric($value))? "id" : "name";
		if (!strstr($this->from, `Category`) && !array_key_exists("mediatype", $this->filter))
			$this->from .= ", categories AS `Category`, object_categories AS `ObjectCategory`";
		$this->conditions[] = "`Category`." . $cat_field . "='" . $value . "' 
						AND `ObjectCategory`.object_id=`BEObject`.id
						AND `ObjectCategory`.category_id=`Category`.id
						AND `Category`.object_type_id IS NOT NULL";
	}
	
	private function tagFilter($value) {
		$cat_field = (is_numeric($value))? "id" : "name";
		if (!strstr($this->from, `Category`) && !array_key_exists("mediatype", $this->filter))
			$this->from .= ", categories AS `Category`, object_categories AS `ObjectCategory`";
		$this->conditions[] = "`Category`." . $cat_field . "='" . $value . "' 
						AND `ObjectCategory`.object_id=`BEObject`.id
						AND `ObjectCategory`.category_id=`Category`.id
						AND `Category`.object_type_id IS NULL";
	}
	
	private function rel_detailFilter($value) {
		if (!empty($value)) {
			if (!isset($this->filter["ObjectRelation.switch"]))
				$this->filter["ObjectRelation.switch"] = "";
			$this->fields .= ", `RelatedObject`.*";
			$this->from .= ", objects AS `RelatedObject`";
			$this->conditions[] = "`ObjectRelation`.object_id=`RelatedObject`.id";
			$this->order .= ( (!empty($this->order))? "," : "" ) . "ObjectRelation.priority";
		}		
	}
	
	private function ref_object_detailsFilter($value) {
		if (!empty($value)) {
			$this->fields .= ", `ReferenceObject`.*";
			$this->from .= ", objects AS `ReferenceObject`";
			$this->conditions[] = "`" . ClassRegistry::init($value)->alias . "`.object_id=`ReferenceObject`.id";
		}
	}
	
	private function mail_groupFilter($value) {
		$this->from .= ", mail_group_cards AS `MailGroupCard`";
		$this->conditions[] = "`MailGroupCard`.mail_group_id='" . $value . "' 
						AND `MailGroupCard`.card_id=`BEObject`.id";
	}

	private function user_createdFilter() {
		$this->fields .= ", `User`.userid, `User`.realname";
		$this->from .= ", users AS `User`";
		$this->conditions[] = "`User`.id=`BEObject`.user_created";
	}
}
 
?>