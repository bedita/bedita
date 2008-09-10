<?php
class MailAddress extends BEAppModel 
{
	
	var $belongsTo = array("User");
	
	var $hasAndBelongsToMany = array(

			"MailGroup" => array(
							"joinTable"	=> "mail_group_addresses"		
						)
	
		);
	
}
?>