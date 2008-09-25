<?php
class MailGroupCard extends BEAppModel 
{
	var $belongsTo = array("MailGroup", "Card");
	
	protected $modelBindings = array( 
				"detailed" => array("Card" => array("User"), "MailGroup"),
				"default" => array("Card","MailGroup"),
				"minimum" => array()		
	);
}
?>