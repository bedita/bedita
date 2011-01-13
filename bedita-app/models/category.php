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
 * Generic category
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Category extends BEAppModel {
	var $actsAs = array(
			'CompactResult' 		=> array()
	);
	
	var $validate = array(
		'label' => array(
			'rule' => 'notEmpty'
		),
		'status' => array(
			'rule' => 'notEmpty'
		),
		'name' => array(
			'rule' => 'notEmpty'
		)
	) ;

	// static vars used by reorderTag static function
	static $dirTag, $orderTag;
	
	function afterFind($result) {
		foreach ($result as &$res) {
			if(isset($res['name']))
				$res['url_label'] = $this->urlLabel($res['name']);
		}
		return $result;			
	}

	public function tagLabelPresent($label, $exclude_id=null) {
		$name = $this->uniqueLabelName($label);
		$tagDB = $this->find("first", 
			array("conditions" => "object_type_id IS NULL AND name='".addslashes($name)."' " . $this->collateStatment() ) );
		
		if (!empty($exclude_id) && $exclude_id == $tagDB["id"])
			return false;
		
		return !empty($tagDB);
	}

	/**
	 * Define a unique name from label: lowercase, trimmed
	 * TODO: handle cases with spaces and '+' mixed...
	 * @param unknown_type $label
	 */
	public function uniqueLabelName($label) {
		return strtolower(trim($label));		
	}

	private function urlLabel($tagName) {
		return str_replace(" ", "+", $tagName);		
	}
	
	/**
	 * Define default values
	 */		
	function beforeValidate() {
		$data = &$this->data[$this->name] ;
		$data['label'] = trim($data['label']);
		$data['name'] = $this->uniqueLabelName($data['label']);
		return true;
	}
	 	
//	private function checkLabel($label) {
//		if(empty($label))
//			return null;
		
//		$value = htmlentities( strtolower($label), ENT_NOQUOTES, "UTF-8" );
		// replace accent, uml, tilde,... with letter after & in html entities
//		$value = preg_replace("/&(.)(uml);/", "$1e", $value);
//		$value = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $value);
		// remove special chars (first decode html entities)
//		$value = preg_replace("/[^a-z0-9\s]/i", "", html_entity_decode($value,ENT_NOQUOTES,"UTF-8" ) ) ;
		// trim dashes in the beginning and in the end of nickname
//		$value = trim($value);
//		return $value;
//	}
	
	/**
	 * Get all categories of some object type and order them by area
	 *
	 * @param int $objectType
	 * @return array(
	 * 				"area" => array(
	 * 								nomearea => array(categories in that area)),
	 * 				 "noarea" => array(categories aren't in any area)
	 * 				)
	 */
	public function getCategoriesByArea($objectType) {
		
		$categories = $this->findAll("Category.object_type_id=$objectType");
		
		$objModel = ClassRegistry::init("BEObject");
		$areaList = $objModel->find('list', array(
										"conditions" => "object_type_id=" . Configure::read("objectTypes.area.id"), 
										"order" => "title", 
										"fields" => "BEObject.title"
										)
									);
		
		$areaCategory = array();
		
		foreach ($categories as $cat) {
			if (array_key_exists($cat["area_id"],$areaList)) {
				$areaCategory["area"][$areaList[$cat["area_id"]]][] = $cat; 
			} else {
				$areaCategory["noarea"][] = $cat;
			}
		}
		
		return $areaCategory;
	}
	
	
	private function collateStatment() {
		$res = "";
		// #MYSQL
		if($this->getDriver() == "mysql") {
			$res = "collate utf8_bin";
		}
		return $res;
	}
	
	/**
	 * save a list of comma separated tag
	 *
	 * @param comma separated string $tagList 
	 * @return array of tags' id
	 */
	public function saveTagList($tagList) {
		$arrIdTag = array();
		if (!empty($tagList)) {
			$tags = explode(",", $tagList);
			
			foreach ($tags as $tag) {
				$tag = trim($tag);
				
				if (!empty($tag))  {
					$name = $this->uniqueLabelName($tag);
					
					
					$tagDB = $this->find("first", array(
													"conditions" => "object_type_id IS NULL AND name='".addslashes($name)."' " . 
														$this->collateStatment()
													)
									);
					if (empty($tagDB)) {
						$tagDB["label"] = $tag;
						$tagDB["name"] = $name;
						$tagDB["status"] = "on";
						$this->create();
						if (!$this->save($tagDB)) {
							throw new BeditaException(__("Error saving tags", true));
						}
					}
					$id_tag = (!empty($tagDB["id"]))? $tagDB["id"] : $this->getLastInsertID();
					if (!in_array($id_tag,$arrIdTag)) {
						$arrIdTag[$id_tag] = $id_tag;
					}
				}  
			}
		}
		return $arrIdTag;
	}
	
	/**
	 * return list of tags with their weight
	 *
	 * @param bool $cloud, if it's true return css class for cloud view
	 * @param int $coeff, coeffiecient for calculate the distribution
	 * @return array
	 */
	public function getTags($showOrphans=true, $status=null, $cloud=false, $coeff=12, $order="label", $dir=1) {
		
		$conditions = array();
		$conditions[] = "Category.object_type_id IS NULL";
		if(!empty($status)) {
			$conditions["Category.status"] = $status;
		}
		
		$orderSql = ($order != "weight")? $order : "label";
		$dirSql = ($dir)? "ASC" : "DESC";
		
		$allTags = $this->find('all', array(
										'conditions'=> $conditions,
										'order' 	=> array("Category." . $orderSql => $dirSql)
										)
								);
		$tags = array();
		foreach ($allTags as $t) {
			$tags[$t['id']] = $t;
		}

		// #CUSTOM QUERY
		$sql = "SELECT categories.id, COUNT(object_categories.category_id) AS weight
				FROM categories, object_categories
				WHERE categories.object_type_id IS NULL
				AND categories.id = object_categories.category_id";
		if (!empty($status)) {
			$statusCond = (is_array($status))? implode("','", $status) : $status;
			$sql .= " AND categories.status IN ('" . $statusCond . "')";
		}
		$sql .= " GROUP BY categories.id, categories.label ORDER BY categories.label ASC";
		
		$res = $this->query($sql);

		if ($cloud) {
			// #CUSTOM QUERY
			$sqlMax = "SELECT MAX(weight) AS max, MIN(weight) AS min FROM (" . $sql . ") tab";
			$maxmin = $this->query($sqlMax);
			$max = $maxmin[0][0]["max"];		
			$min = $maxmin[0][0]["min"];
			$distribution = ($max - $min) / $coeff;
		}
		
		foreach ($res as $r) {
			$key = !empty($r['categories']['id']) ? $r['categories']['id'] : $r[0]['id'] ;
			$w = $r[0]['weight'];
			$tags[$key]['weight'] = $w;
			if ($cloud) {
				if ($w == $min)
					$tags[$key]['class'] = "smallestTag";
				elseif ($w == $max)
					$tags[$key]['class']  = "largestTag";
				elseif ($w > ($min + ($distribution * 2)))
					$tags[$key]['class']  = "largeTag";
				elseif ($w > ($min + $distribution))
					$tags[$key]['class']  = "mediumTag";
				else 
					$tags[$key]['class']  = "smallTag";
			}	
		}
		
		// remove orphans or set weight = 0, create the non-associative array
		$tagsArray = array();
		foreach ($tags as $k => $t) {
			$tags[$k]['url_label'] = $this->urlLabel($t['name']);
			if(!isset($t['weight'])) {
				if($showOrphans === false) {
					unset($tags[$k]);		
				} else {
					$tags[$k]['weight'] = 0;		
					$tagsArray[]= $tags[$k];
				}
			} else {
				$tagsArray[]= $tags[$k];
			}
		}
		
		// if order by weight reorder tags
		if ($order == "weight") {
			Category::$orderTag = $order;
			Category::$dirTag = $dir;
			usort($tagsArray, array('Category', 'reorderTag'));
		}
		
		return $tagsArray;
	}
	
	
	public function getContentsByTag($label) {
		// bind association on the fly
		$hasAndBelongsToMany = array(
			'BEObject' =>
				array(
					'className'				=> 'BEObject',
					'joinTable'    			=> 'object_categories',
					'foreignKey'   			=> 'category_id',
					'associationForeignKey'	=> 'object_id',
					'unique'				=> true
						)
				);
				
		$this->bindModel( array(
				'hasAndBelongsToMany' 	=> $hasAndBelongsToMany
				) 
			);
		
		// don't compact find result
		$this->bviorCompactResults = false;
		$name = $this->uniqueLabelName($label);
		$tag = $this->find("first", array(
										"conditions" => "object_type_id IS NULL AND name='".addslashes($name)."' ". 
												$this->collateStatment(), 
										"contain" => array("BEObject" => array("ObjectType"))
									)
						);
		
		// reset to default compact result
		$this->bviorCompactResults = true;
		
		return empty($tag["BEObject"]) ? array() : $tag["BEObject"];
	}

	/**
	 * USED for multimedia objects
	 * check if exists $mediatype in categories for an object type. If not, create the category
	 *
	 * @param int $object_type_id
	 * @return mixed, false if not mediatype in the form else return array of Category
	 */
	public function checkMediaType($object_type_id, $mediatype) {
		
		if (empty($mediatype)) {
			return false;
		}
		
		$category = $this->find("first",
			array(
				"conditions" => array(
					"name" => $mediatype, 
					"object_type_id" => $object_type_id
				)
			)
		);
		if(empty($category)) { // if media category doesn't exists, create it
			$data = array(
				"name"=>$mediatype,
				"label"=>ucfirst($mediatype),
				"object_type_id"=>$object_type_id,
				"status"=>"on"
			);
			$this->create();
			if(!$this->save($data)) {
				throw new BeditaException(__("Error saving category", true), $this->validationErrors);
			}
			$category['id']=$this->id;
		}
		$categoryArr = array($category['id']=>$category['id']);
		return $categoryArr;
	}
	
	/**
	 * compare two array elements defined by $orderTag var and return -1,0,1 
	 *	$dirTag is used for define order of comparison 
	 * 
	 * @param array $e1
	 * @param array $e2
	 * @return int (-1,0,1)
	 */
	private static function reorderTag($e1, $e2) {
		$d1 = $e1[Category::$orderTag];
		$d2 = $e2[Category::$orderTag];
		return (Category::$dirTag)? strcmp($d1,$d2) : strcmp($d2,$d1);
	}
	
	
	/**
	 * Search for category names, create if not already present, and
	 * return array of corresponding id
	 *
	 * @param array $names, category names to search/create
	 * @param int $objTypeId, category object type id
	 * @return array of corresponding id-category
	 */
	public function findCreateCategories(array &$names, $objTypeId) {
		$res = array();
		// if not exists create
		foreach ($names as $n) {
			$this->create();
			$n = trim($n);
			$this->bviorCompactResults = false;
			$idCat = $this->field('id', array('name' => $n, 'object_type_id' => $objTypeId));
			$this->bviorCompactResults = true;
			if(empty($idCat)) {
				$dataCat = array('name'=> $n,'label' => $n,
					'object_type_id' => $objTypeId, 'status'=>'on');
				if(!$this->save($dataCat)) {
					throw new BeditaException(__("Error saving category") . ": " . print_r($dataCat, true));
				}
				$idCat = $this->id;
			}
			$res[] = $idCat;
		}
		return $res;		
	}
}
?>