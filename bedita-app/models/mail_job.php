<?php
class MailJob extends BEAppModel 
{
	var $belongsTo = array("MailMessage", "Card");
	
	protected $modelBindings = array( 
				"detailed" =>  array("MailMessage" => array("BEObject", "Content"), "Card"),
				
				"default" => array("MailMessage", "Card"),

				"minimum" => array()
	);
}
?>