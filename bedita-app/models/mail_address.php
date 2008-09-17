<?php
class MailAddress extends BEAppModel 
{	
	var $belongsTo = array("User", "Card");
	
	var $hasAndBelongsToMany = array(

			"MailGroup" => array(
							"joinTable"	=> "mail_group_addresses"
						)
	
		);
	
	var $validate = array(
			"email" => array(
				"rule" => "email",
				"required" => true,
				"message" => "Please supply a valid email address."
				)
		);
	

	// save join with groups
	function afterSave($created) {
			
		if (!empty($this->data["joinGroup"])) {
			
			if (empty($this->id))
				throw new BeditaException(__("Error saving mail address", true), "Missing model id in afterSave.");
			
			$mailGroupAddress = ClassRegistry::init("MailGroupAddress");
			
			$mailGroupAddress->deleteAll(array("mail_address_id" => $this->id));
				
			foreach ($this->data["joinGroup"] as $joinData) {
				
				// rebuild active join
				if (!empty($joinData["mail_group_id"])) {
					$joinData["mail_address_id"] = $this->id;
					
					if (empty($joinData["hash"])) {
						$groupname = $this->MailGroup->field("group_name", array("id" => $joinData["mail_group_id"]));
						$joinData["hash"] = md5(microtime() . $groupname);
					}
					
					if (empty($joinData["created"]))
						unset($joinData["created"]);
										
					$mailGroupAddress->create();
					if (!$mailGroupAddress->save($joinData))
						throw new BeditaException(__("Error creating join between address and groups", true), "Saving error");

				}
			}
			
		}

	}
}
?>