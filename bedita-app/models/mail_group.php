<?php
class MailGroup extends BEAppModel 
{
	
	var $belongsTo = array("Area");
	
	var $hasAndBelongsToMany = array(

			"Card" => array("joinTable"	=> "mail_group_cards"),
			
			"MailMessage" => array("joinTable" => "mail_group_messages")
	
		);
	
	protected $modelBindings = array( 
				"detailed" => array("Area", "Card", "MailMessage"),
				"default" => array("Area"),
				"minimum" => array()		
	);

	/**
	 * get mail groups divided by area
	 *
	 * @param int $area_id, if it's defined, filter by area_id 
	 * @param int $address_id, if it's defined, filter by address_id
	 * @param int $message_id, if it's defined, filter by message_id
	 * @return array
	 */		
	public function getGroupsByArea($area_id=null, $card_id=null, $message_id=null) {
		
		$contain = array();
		if (!empty($area_id))
			$contain[] = "Area.id=" . $area_id;
		if (!empty($card_id))
			$contain[] = "Card.id=" . $card_id;
		if (!empty($message_id))
			$contain[] = "MailMessage.id=" . $message_id;
		
		$groups = $this->find("all", array(
								'contain' => $contain
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
			if (!empty($message_id) && !empty($g["MailMessage"])) {
				$g["MailGroup"]["MailMessage"] = $g["MailMessage"][0];
			}
				
			$areaGroups[$areaList[$g["MailGroup"]["area_id"]]][] = $g["MailGroup"]; 
		
		}

		return $areaGroups;
	}
}
?>