<?php
class MailGroup extends BEAppModel 
{
	
	var $belongsTo = array("Area");
	
	var $hasAndBelongsToMany = array(

			"Card" => array(
							"joinTable"	=> "mail_group_cards"		
						)
	
		);
	
	protected $modelBindings = array( 
				"detailed" => array("Area", "Card"),
				"default" => array("Area"),
				"minimum" => array()		
	);

	/**
	 * get mail groups divided by area
	 *
	 * @param int $area_id, if it's defined, filter by area_id 
	 * @param int $address_id, if it's defined, filter by address_id
	 * @return array
	 */		
	public function getGroupsByArea($area_id=null, $card_id=null) {
		
		$areaCond = (!empty($area_id))? "Area.id=" . $area_id : "Area";
		$cardCond = (!empty($card_id))? "Card.id=" . $card_id : "Card";
			
		$groups = $this->find("all", array(
								'contain' => array($areaCond, $cardCond)
								)
						);

		$areaGroups = array();
		
		$objModel = ClassRegistry::init("BEObject");
		$areaList = $objModel->find('list', array(
							"conditions" => "object_type_id=" . Configure::read("objectTypes.area.id"), 
							"order" => "title", 
							"fields" => "BEObject.title")
							);
		
		foreach ($groups as $g) {
			
			if (!empty($card_id) && !empty($g["Card"])) {
				$g["MailGroup"]["subscribed"] = true;
				$g["MailGroup"]["MailGroupCard"] = $g["Card"][0]["MailGroupCard"];
			}
				
			$areaGroups[$areaList[$g["MailGroup"]["area_id"]]][] = $g["MailGroup"]; 
		
		}
		
		return $areaGroups;
	}
}
?>