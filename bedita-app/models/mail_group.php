<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 * Mail group object
 */
class MailGroup extends BEAppModel 
{
	
	var $belongsTo = array("Area");
	
	var $hasAndBelongsToMany = array(

			"Card" => array(
				"joinTable"	=> "mail_group_cards",
				"with" => "MailGroupCard"
			),
			
			"MailMessage" => array("joinTable" => "mail_group_messages")
	
		);

    protected $modelBindings = array(
        'detailed' => array('Area', 'Card', 'MailMessage'),
        'default' => array('Area'),
        'minimum' => array()
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

	/**
	 * return url for publication
	 * 
	 * @param int $mail_group_id
	 * @return string
	 */
	public function getPublicationUrlByGroup($mail_group_id) {
		$pub_id = $this->field("area_id", array("id" => $mail_group_id));
		$areaModel = ClassRegistry::init("Area");
		return $areaModel->field("public_url", array("id" => $pub_id));
	}
}
