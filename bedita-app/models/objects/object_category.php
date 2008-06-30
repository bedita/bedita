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
class ObjectCategory extends BEAppModel {
	var $actsAs = array(
			'CompactResult' 		=> array()
	);
	
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
		App::import('Model','Area');
		$this->Area = new Area(); 
		$categories = $this->findAll("ObjectCategory.object_type_id=$objectType");
		$this->Area->displayField = 'public_name';
		$areaList = $this->Area->find('list', array("order" => "public_name"));
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
						$arrIdTag[] = $id_tag;
					}
				}  
			}
		}
		return $arrIdTag;
	}
	
	public function getWeighedTags() {

		$sql = "SELECT DISTINCT object_categories.id, object_categories.label, 
					   COUNT(content_bases_object_categories.object_category_id) AS weight
				FROM object_categories,content_bases_object_categories
				WHERE object_categories.object_type_id IS NULL
				AND object_categories.id = content_bases_object_categories.object_category_id
				GROUP BY object_categories.id
				ORDER BY object_categories.label ASC
				";
		$res = $this->query($sql);
		$tags = array();
		if (!empty($res)) {
			foreach ($res as $t) {
				$tags[] = array_merge($t["object_categories"],$t[0]);
			}
		}

		return $tags;
	}
	
	public function getContentsByTag($label) {
		// bind association on th fly
		$hasAndBelongsToMany = array(
			'ContentBase' =>
				array(
					'className'				=> 'ContentBase',
					'joinTable'    			=> 'content_bases_object_categories',
					'foreignKey'   			=> 'object_category_id',
					'associationForeignKey'	=> 'content_base_id',
					'unique'				=> true
						)
				);
				
		$this->bindModel( array(
				'hasAndBelongsToMany' 	=> $hasAndBelongsToMany
				) 
			);
		
		// don't compact find result
		$this->bviorCompactResults = false;
		
		$tag = $this->find("first", array("conditions" => array("label" => $label, "object_type_id IS NULL")));
		
		$beObject = ClassRegistry::init("BEObject");
		
		foreach ($tag["ContentBase"] as $c) {
			
			$o = $beObject->find("first", array(
									"conditions" => array("BEObject.id" => $c["id"]),
									"restrict"	=> array("ObjectType")
									)
							);
			
			$contents[] = array_merge( $c, $o["BEObject"], array("ObjectType" => $o["ObjectType"]) ) ; 
			
		}
		
		// reset to default compact result
		$this->bviorCompactResults = true;
		
		return $contents;
	}
}
?>