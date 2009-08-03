<?php
/**
 ** ******************************************
 **  Default notification messages
 *   You can override this messages in local.msg.php
 *   Create a copy of this file in this folder, name it local.msg.php
 *   and change messages...
 * 
 * Placehoder used/available:
 * 	 [$user] -- BEdita username 
 *   [$author] -- comment/note author
 *   [$title] -- content title, or referenced object title (notes/comments)
 *   [$text]  -- note or comment text / account data 
 *   [$url]  -- content URL
 *   [$beditaUrl]  -- BEdita URL
 *    
 ** ******************************************
 */

// Message for new note
$notify["editornote"]["eng"] = array(
	"subject" => "[BEdita] new note on '[\$title]'",

	"mail_body" => "Hi [\$user], " .
	"\nthis is an automatic notification email from BEdita at " .
	"\n [\$beditaUrl]" .
	"\nA new note has been added by [\$author] on '[\$title]' "	.
	"\n\n ------------" .
	"\n[\$text]" .
	"\n ------------" .
	"\n Look at [\$url]"
);

$notify["editornote"]["ita"] = array(
	"subject" => "[BEdita] nuova nota su '[\$title]'",

	"mail_body" => "Ciao [\$user], " .
	"\nquesto e' un messaggio automatico di notifica da BEdita." .
	"\nE' stata aggiunta una nuova nota dall'utente [\$author] sul contenuto '[\$title]' "	.
	"\n\n ------------" .
	"\n[\$text]" .
	"\n ------------" .
	"\n\n Vedi: [\$url]"
);


// Message for new comment
$notify["comment"]["eng"] = array(
	"subject" => "[BEdita] new comment on '[\$title]'",

	"mail_body" => "Hi [\$user], " .
	"\nthis is an automatic notification email from BEdita system at " .
	"\n [\$beditaUrl]" .
	"\nA new comment has been added by [\$author] on '[\$title]' "	.
	"\n\n ------------" .
	"\n[\$text]" .
	"\n ------------" .
	"\n\n Look at [\$url]"
);

// Message for new user
$notify["contentChange"]["eng"] = array(
	"subject" => "[BEdita] content '[\$title]' changed",

	"mail_body" => "Hi [\$user], " .
	"\ncontent [\$title]', created by you, has been modified by [\$author]" .
	"\n Look at [\$url]" .
	"\n BEdita system on [\$beditaUrl]"
);


// Message for new user
$notify["newUser"]["eng"] = array(
	"subject" => "[BEdita] new account",

	"mail_body" => "Hi [\$user], " .
	"\na new account has been create for you on BEdita at " .
	"\n[\$beditaUrl]" .
	"\n\nAccount data\n----------" .
	"\n[\$text]" 
	
);

// Message for updated user
$notify["updateUser"]["eng"] = array(
	"subject" => "[BEdita] account changed",

	"mail_body" => "Hi [\$user], " .
	"\nyour account has been changed on BEdita at " .
	"\n[\$beditaUrl]" .
	"\n\nAccount data\n----------\n" .
	"\n[\$text]" 
);

?>
