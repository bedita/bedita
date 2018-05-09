<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2015 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * Category model
 * 
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
				$res['url_label'] = $res['name'];
		}
		return $result;			
	}

	public function tagLabelPresent($label, $exclude_id=null) {
		$tagDB = $this->find("first", 
			array("conditions" => "object_type_id IS NULL AND name='".addslashes($label)."' " . $this->collateStatment() ) );
		
		if (!empty($exclude_id) && $exclude_id == $tagDB["id"])
			return false;
		
		return !empty($tagDB);
	}

	/**
	 * Get tag label from unique name
	 * 
	 * @param string $name
	 */
	public function tagLabelFromName($name) {
		$tagDB = $this->find("first", 
			array("conditions" => "object_type_id IS NULL AND name='$name'"));
		return !empty($tagDB) ? $tagDB['label'] : "";
	}
	
	
	/**
	 * Define a unique name from label: lowercase, trimmed, etc...
	 * 
	 * @param string $label
	 */
	public function uniqueLabelName($label) {
		$baseName = $name = BeLib::getInstance()->friendlyUrlString($label);

		// search for already used label
		
		// suffix counter
		$i = 1;
		
		// if it's a category
		if (!empty($this->data[$this->alias]["object_type_id"])) {

			// if name is in mediaTypes
			if (in_array($name, Configure::read("mediaTypes"))) {
				// if multimedia object return baseName
				if (in_array($this->data[$this->alias]["object_type_id"], Configure::read("objectTypes.multimedia.id"))) {
					return $baseName;
				} else {
					$name = $baseName . "-" . $i++;
				}
			}

			$conditions[] = "object_type_id IS NOT NULL";

			
		// if it's a tag
		} else {
			$conditions[] = "object_type_id IS NULL";
		}

		$conditions["name"] = $name;
		// exclude itself if already present
		if (!empty($this->data[$this->alias]["id"])) {
			$conditions["NOT"]["id"] = $this->data[$this->alias]["id"];
		}

		$count = $this->find("count", array("conditions" => $conditions));
		
		if ($count > 0) {
			$freeName = false;
			while (!$freeName) {
				$conditions["name"] = $baseName . "-" . $i++;
				$count = $this->find("count", array("conditions" => $conditions));
				if ($count == 0) {
					$freeName = true;
					$name = $conditions["name"];
				}
			}
		}

		return $name;
	}
	
	/**
	 * Define default values
	 */		
	function beforeValidate() {
		$data = &$this->data[$this->name];
		$name = '';
		// if new tag/category
		if (empty($data['id'])) {
			if (!empty($data['label'])) {
				$name = $this->uniqueLabelName($data["label"]);
			}
		// if it's an existing tag/category
		} else {
			if (isset($data['name'])) {
				$data['name'] = trim($data['name']);
				if (!empty($data['name'])) {
					$name = $data['name'];
				} elseif (!empty($data['label'])) {
					$name = $data['label'];
				}
			}
		}

		if (!empty($name)) {
			$data['name'] = $this->uniqueLabelName($name);
		}

		return true;
	}
	
	/**
	 * Get all categories of some object type and order them by area
	 *
	 * @param int $objectTypeId Object type ID
	 * @param array $options The options (i.e. ['merge' => true, 'publicationTitle' => <The publication title>])
	 * @return array(
	 * 		'area' => array(<areaname> => array(categories in that area), ...),
	 *		'noarea' => array(categories not in areas)
	 * 	)
	 */
	public function getCategoriesByArea($objectTypeId, $options = array()) {
		$categories = $this->find('all', array(
			'conditions' => array('Category.object_type_id' => $objectTypeId), 'order' => 'label'
		));

		$objModel = ClassRegistry::init('BEObject');
		$areaList = $objModel->find('list', array(
			'conditions' => array('BEObject.object_type_id' => Configure::read('objectTypes.area.id')),
			'order' => 'title',
			'fields' => 'BEObject.title',
		));

		$areaCategory = array();
		foreach ($categories as $cat) {
			if (array_key_exists($cat['area_id'], $areaList)) {
				$areaCategory['area'][$areaList[$cat["area_id"]]][] = $cat; 
			} else {
				$areaCategory['noarea'][] = $cat;
			}
		}
		if (empty($options['merge'])) {
			return $areaCategory;
		}

		$categories = array();
		if (!empty($areaCategory['noarea'])) {
			$categories = $areaCategory['noarea'];
		}
		if (!empty($options['publicationTitle'])) {
			$publicationTitle = $options['publicationTitle'];
			if(!empty($areaCategories['area'][$publicationTitle])) {
				$categories = array_merge($categories, $areaCategory['area'][$publicationTitle]);
			}
		}

		return $categories;
	}
	
	/**
	 * Get all categories defined for object type id
	 *
	 * @param int $objectType
	 * @return array of (
	 * 				"area" => array(
	 * 								nomearea => array(categories in that area)),
	 * 				 "noarea" => array(categories aren't in any area)
	 * 				)
	 */
	public function objectCategories($objectTypeId) {
	    $categories = $this->find("all", array(
	        "conditions" => array("Category.object_type_id" => $objectTypeId),
	        "order" => "label"));
	    return $categories;
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
					
					$tagDB = $this->find("first", array(
													"conditions" => "object_type_id IS NULL AND label='".addslashes($tag)."' " .
														$this->collateStatment()
													)
									);
					if (empty($tagDB)) {
						$tagDB["label"] = $tag;
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
	 * @param array $options
	 *				"showOrphans" => true,  show all tags also not associated
	 *				"status" => null,		string or array (on, off, draft). if area_id is setted "status" is used also to related objects
	 *				"cloud" => false,		true to set a css class for cloud view
	 *				"coeff" => 12,			coeffiecient for calculate the distribution
	 *				"order" => "label",		order by field
	 *				"dir" => 1,				asc(1), desc(0)
	 *				"area_id"=> null		get tags only associated to objects that are in "area_id" publication
	 * 
	 * @return array
	 */
	public function getTags(array $options = array()) {

		$options = array_merge(
			array("showOrphans" => true, "status" => null, "cloud" => false, 
				"coeff" => 12, "order" => "label", "dir" => 1, "area_id"=> null, "section_id" => null),
			(array)$options
		);
		
		$conditions = array();
		$conditions[] = "Category.object_type_id IS NULL";
		if(!empty($options["status"])) {
			$conditions["Category.status"] = $options["status"];
		}
		
		$orderSql = ($options["order"] != "weight")? $options["order"] : "label";
		$dirSql = ($options["dir"])? "ASC" : "DESC";

		$joinsBEObject = array();
		$joins = array();

		// get tags associated to objects that are in $area_id publication or $section_id section
		if (!empty($options["area_id"]) || !empty($options["section_id"])) {
			$joinsBEObject = array(
					'table' => 'objects',
					'alias' => 'BEObject',
					'type' => 'inner',
					'conditions'=> array(
						'BEObject.id = ObjectCategory.object_id'
					)
				);
			if (!empty($options["status"])) {
				$joinsBEObject["conditions"]['BEObject.status'] = $options["status"];
			}

			$treeCondition = array('Tree.id = BEObject.id');
			if(!empty($options["section_id"])) {
				$treeCondition['Tree.parent_id'] = $options["section_id"];
			} else {
				$treeCondition['Tree.area_id'] = $options["area_id"];
			}
			
			$joinsTree = array(
				'table' => 'trees',
				'alias' => 'Tree',
				'type' => 'inner',
				'conditions'=> $treeCondition
			);

			$joins = array(
				array(
					'table' => 'object_categories',
					'alias' => 'ObjectCategory',
					'type' => 'inner',
					'conditions'=> array('ObjectCategory.category_id = Category.id')
				),
				$joinsBEObject,
				$joinsTree
			);
		}

		$allTags = $this->find('all', array(
			'conditions'=> $conditions,
			'order' 	=> array("Category." . $orderSql => $dirSql),
			'group' => $this->fieldsString("Category"),
			'joins' => $joins
		));
		
		$tags = array();
		foreach ($allTags as $t) {
			$tags[$t['id']] = $t;
		}

		$category_ids = Set::extract('/id', $allTags);
		
		$objCatModel = ClassRegistry::init("ObjectCategory");
		$joins = (empty($joinsBEObject))? array() : array($joinsBEObject, $joinsTree);
		$res = $objCatModel->find("all", array(
			'fields' => array("DISTINCT ObjectCategory.category_id", "ObjectCategory.object_id"),
			'conditions' => array("ObjectCategory.category_id" => $category_ids),
			'joins' => $joins
		));
		
		// calculate weights
		$weights = array(0);
		foreach ($res as $val) {
			if (empty($weights[$val["ObjectCategory"]["category_id"]])) {
				$weights[$val["ObjectCategory"]["category_id"]] = 1;
			} else {
				$weights[$val["ObjectCategory"]["category_id"]]++;
			}
		}
		
		if ($options["cloud"]) {
			$max = max($weights);
			$min = min($weights);
			$distribution = ($max - $min) / $options["coeff"];
		}
		
		foreach ($res as $r) {
			$key = !empty($r['ObjectCategory']['category_id']) ? $r['ObjectCategory']['category_id'] : $r[0]['category_id'] ;
			$w = $weights[$r["ObjectCategory"]["category_id"]];
			$tags[$key]['weight'] = $w;
			if ($options["cloud"]) {
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
			$tags[$k]['url_label'] = $t['name'];
			if(!isset($t['weight'])) {
				if($options["showOrphans"] === false) {
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
		if ($options["order"] == "weight") {
			Category::$orderTag = $options["order"];
			Category::$dirTag = $options["dir"];
			usort($tagsArray, array('Category', 'reorderTag'));
		}
//		pr($tagsArray);exit;
		return $tagsArray;
	}
	
	
	public function getContentsByTag($name) {
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
	
	/**
	 * Add category to object usgin both category and object id
	 * 
	 * @return true on success, false on failure
	 */
	public function addObjectCategory($categoryId, $objectId) {
	    $res = true;
	    $objCatModel = ClassRegistry::init("ObjectCategory");
	    $data = $objCatModel->find("all", array(
	            'fields' => array("DISTINCT ObjectCategory.category_id", "ObjectCategory.object_id"),
	            'conditions' => array(
	                "ObjectCategory.category_id" => $categoryId, 
	                "ObjectCategory.object_id" => $objectId)
	    ));
	    if(empty($data)) {
	        // #CUSTOM QUERY
	        $q = "INSERT INTO object_categories (category_id, object_id)" .
	            " VALUES ({$categoryId}, {$objectId})";
	        $res = $this->query($q);
	    }
	    return $res;
	}

	/**
     * append mediatype to objects array
     *
     * @param array $objects
     * @param array $options
     */
    public function appendMediatype(array $objects, $options = array()) {
        $this->Behaviors->disable('CompactResult');
        $objectCategory = ClassRegistry::init('ObjectCategory');
        foreach ($objects as &$obj) {
            $categoryId = $objectCategory->field('category_id', array('object_id' => $obj['id']));
            if (!empty($categoryId)) {
                $obj['mediatype'] = $this->field('name', array('id' => $categoryId));
            }
        }
        $this->Behaviors->enable('CompactResult');
        return $objects;
    }

    /**
     * Get category id from name and object type id
     * 
     * @param $name, string unique name of category
     * @param $objectTypeId, int object type id
     * @return proerty id on success, null if no proerty id was found
     */
    public function categoryId($name, $objectTypeId) {
        $this->Behaviors->disable('CompactResult');
        $res = $this->field('id', array(
                'object_type_id' => $objectTypeId,
                'name' => $name));
        $this->Behaviors->enable('CompactResult');
        return $res;
    }

}
