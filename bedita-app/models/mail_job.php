<?php
class MailJob extends BEAppModel 
{
	var $belongsTo = array("MailMessage", "MailAddress");
}
?>