<?php
class MailGroup extends BEAppModel 
{
	
	var $belongsTo = array("Area");
	
	var $hasAndBelongsToMany = array(

			"MailAddress" => array(
							"joinTable"	=> "mail_group_addresses"		
						)
	
		);
	

	/**
	 * get mail groups divided by area
	 *
	 * @param int $area_id, if it's defined, filter by area_id 
	 * @param int $address_id, if it's defined, filter by address_id
	 * @return array
	 */		
	public function getGroupsByArea($area_id=null, $address_id=null) {
		
		$areaCond = (!empty($area_id))? "Area.id=" . $area_id : "Area";
		$addressCond = (!empty($address_id))? "MailAddress.id=" . $address_id : "MailAddress";
			
		$groups = $this->find("all", array(
								'contain' => array($areaCond, $addressCond)
								)
						);

		$areaGroups = array();
		
		$objModel = ClassRegistry::init("BEObject");
		$areaList = $objModel->find('list', array(
							"conditions" => "object_type_id=" . Configure::read("objectTypes.area"), 
							"order" => "title", 
							"fields" => "BEObject.title")
							);
		
		foreach ($groups as $g) {
			
			if (!empty($address_id) && !empty($g["MailAddress"])) {
				$g["MailGroup"]["subscribed"] = true;
				$g["MailGroup"]["MailGroupAddress"] = $g["MailAddress"][0]["MailGroupAddress"];
			}
				
			$areaGroups[$areaList[$g["MailGroup"]["area_id"]]][] = $g["MailGroup"]; 
		
		}
		
		return $areaGroups;
	}
}
?>