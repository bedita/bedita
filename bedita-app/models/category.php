<?php
/**
 *
 * @filesource
 * @copyright		Copyright (c) 2008
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license
 * @author 		ste ste@channelweb.it			
*/
class Category extends BEAppModel {
	var $actsAs = array(
			'CompactResult' 		=> array()
	);
	
	var $validate = array(
		'label' 			=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
		'status' 			=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
	) ;

	// static vars used by reorderTag static function
	static $dirTag, $orderTag;
	
	function afterFind($result) {
		foreach ($result as &$res) {
			if(isset($res['label']))
				$res['url_label'] = str_replace(" ", "+", $res['label']);
		}
		return $result;			
	}
	
	/**
	 * Definisce i valori di default.
	 */		
	function beforeValidate() {
		if(isset($this->data[$this->name])) 
			$data = &$this->data[$this->name] ;
		else 
			$data = &$this->data ;
		$data['label'] = $this->checkLabel($data['label']);
		return true;
	}
	 	
	private function checkLabel($label) {
		if(empty($label))
			return null;
		
		$value = htmlentities( strtolower($label), ENT_NOQUOTES, "UTF-8" );
		// replace accent, uml, tilde,... with letter after & in html entities
		$value = preg_replace("/&(.)(uml);/", "$1e", $value);
		$value = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $value);
		// remove special chars (first decode html entities)
		$value = preg_replace("/[^a-z0-9\s]/i", "", html_entity_decode($value,ENT_NOQUOTES,"UTF-8" ) ) ;
		// trim dashes in the beginning and in the end of nickname
		$value = trim($value);
		return $value;
	}
	
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
										"conditions" => "object_type_id=" . Configure::read("objectTypes.area"), 
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
													"conditions" => "label='".$tag."' AND object_type_id IS NULL"
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
	 * @param bool $cloud, if it's true return css class for cloud view
	 * @param int $coeff, coeffiecient for calculate the distribution
	 * @return array
	 */
	public function getTags($showOrphans=true, $status=null, $cloud=false, $coeff=12, $order="label", $dir=1) {
		
		$conditions = array();
		$conditions[] = "Category.object_type_id IS NULL";
		if(!empty($status)) {
				if(is_array($status)) {
					$c = "Category.status IN (";
					for($i=0 ; $i < count($status); $i++) {
						$c .= (($i > 0) ? "," : "") . "'$status[$i]'";
					}
					$c .= ")";
				} else {
					$c = "Category.status = '$status'";
				}
				$conditions[] = $c;
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

		$sql = "SELECT categories.id, COUNT(object_categories.category_id) AS weight
				FROM categories, object_categories
				WHERE categories.object_type_id IS NULL
				AND categories.id = object_categories.category_id
				GROUP BY categories.id
				ORDER BY categories.label ASC";
		
		$res = $this->query($sql);

		if ($cloud) {
			$sqlMax = "SELECT MAX(weight) AS max, MIN(weight) AS min FROM (" . $sql . ") tab";
			$maxmin = $this->query($sqlMax);
			$max = $maxmin[0][0]["max"];		
			$min = $maxmin[0][0]["min"];
			$distribution = ($max - $min) / $coeff;
		}
		
		foreach ($res as $r) {
			$key = $r['categories']['id'];
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
			$tags[$k]['url_label'] = str_replace(" ", "+", $t['label']);
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
		// bind association on th fly
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
										"conditions" => array("label" => $label, "object_type_id IS NULL"),
										"contain" => array("BEObject" => array("ObjectType"))
									)
						);
		
		// reset to default compact result
		$this->bviorCompactResults = true;
		
		return $tag["BEObject"];
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
	
}
?>