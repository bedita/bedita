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
class Card extends BEAppObjectModel {

	public $searchFields = array("title" => 8 , "description" => 4, 
		"company_name" => 3, "city" => 4);

	var $actsAs 	= array(
			'CompactResult' 		=> array("MailGroup", "GeoTag")
	); 
	
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"ObjectProperty",
															"LangText",
															"RelatedObject",
															"Category",
															"User"), 
									"MailGroup", "GeoTag"),

				"default" => array("BEObject" => array("ObjectProperty", 
									"LangText", "ObjectType", 
									"Category", "RelatedObject" )),

				"minimum" => array("BEObject" => array("ObjectType"))		
		);
	
	var $hasAndBelongsToMany = array(
			"MailGroup" => array(
							"joinTable"	=> "mail_group_cards"
						)
		);
	
	var $hasMany = array(
			'GeoTag' =>
				array(
					'foreignKey'	=> 'object_id',
					'dependent'		=> true
				)
		) ;

	
	var $validate = array(
		'email' => array(
			'rule' => array('email', true), // attempt to verify that the host for the address is valid
			'allowEmpty' => true,
			'message' => 'Please supply a valid email address.'
 		),
 		'email2' => array(
			'rule' => array('email', true),
			'allowEmpty' => true,
			'message' => 'Please supply a valid email address.'
 		),
 		'newsletter_email' => array(
			'rule' => array('email', true),
			'allowEmpty' => true,
			'message' => 'Please supply a valid email address.'
 		)
 	);
		
	function beforeValidate() {
		
		$this->checkDate('birthdate');
		$this->checkDate('deathdate');

		return true;
	}
	
	
	function afterSave($created) {
		
		// save join with mail groups
		if (!empty($this->data["Card"]["joinGroup"])) {
		
			if (empty($this->id))
				throw new BeditaException(__("Error saving card", true), "Missing model id in afterSave.");
			
			$mailGroupCard = ClassRegistry::init("MailGroupCard");
			
			$mailGroupCard->deleteAll(array("card_id" => $this->id));
				
			foreach ($this->data["Card"]["joinGroup"] as $joinData) {
				
				// rebuild active join
				if (!empty($joinData["mail_group_id"])) {
					$joinData["card_id"] = $this->id;
					
					if (empty($joinData["hash"])) {
						$groupname = $this->MailGroup->field("group_name", array("id" => $joinData["mail_group_id"]));
						$joinData["hash"] = md5($this->id . microtime() . $groupname);
					}
					
					if (empty($joinData["created"]))
						unset($joinData["created"]);
										
					$mailGroupCard->create();
					if (!$mailGroupCard->save($joinData))
						throw new BeditaException(__("Error creating join between card and groups", true), "Saving error");

				}
			}
			
		}
		
		// save geotag
		return $this->updateHasManyAssoc();

	}
}
?>
