<?php
class MailGroup extends BEAppModel 
{
	
	var $belongsTo = array("Area");
	
	var $hasAndBelongsToMany = array(

			"MailAddress" => array(
							"joinTable"	=> "mail_group_addresses"		
						)
	
		);
	

}
?>