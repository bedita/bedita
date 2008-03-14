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
}
?>