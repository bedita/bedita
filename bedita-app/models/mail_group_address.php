<?php
class MailGroupAddress extends BEAppModel 
{
	var $belongsTo = array("MailAddress","MailGroup");
	
	protected $modelBindings = array( 
				"detailed" => array("MailAddress" => array("User", "Card"), "MailGroup"),
				"default" => array("MailAddress","MailGroup"),
				"minimum" => array()		
	);
}
?>