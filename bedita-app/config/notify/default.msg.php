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
$notify["editor_note"]["eng"] = array(
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

$notify["editor_note"]["ita"] = array(
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
	"\ncontent '[\$title]', created by you, has been modified by [\$author]" .
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

// Message for newsletter subscribe
$notify["newsletterConfirmSubscribe"]["eng"] = array(
	"subject" => "[BEdita] subscribe to newsletter \"[\$title]\"",
	"mail_body" => "Hi [\$user], " .
					"\nyou are now subscribed to \"[\$title]\" newsletter." .
					"\n\nTo confirm your subscrition and activate it click on the following link" .
					"\n[\$url]",
	"viewsMsg" => "You are now subscribed to <b>[\$title]</b>. Soon you'll recieve an email to confirm subscription."
);

$notify["newsletterSubscribed"]["eng"] = array(
	"subject" => "[BEdita] subscribe confirmation to newsletter \"[\$title]\"",
	"mail_body" => "Hi [\$user], " .
					"\n\nyour subscription is now active, soon you'll receive the \"[\$title]\" newsletter.",
	"viewsMsg" => "You are now subscribed to newsletter <b>[\$title]</b>."
);

// Message for newsletter unsubscribe
$notify["newsletterConfirmUnsubscribe"]["eng"] = array(
	"subject" => "[BEdita] unsubscribe to newsletter \"[\$title]\"",
	"mail_body" => "Hi [\$user], " .
					"\n\nTo delete your subscrition at \"[\$title]\" click on the following link" .
					"\n[\$url]",
	"viewsMsg" => "Soon you'll recieve an email to confirm your unsubscription from <b>\"[\$title]\"</b>."
);

$notify["newsletterUnsubscribed"]["eng"] = array(
	"subject" => "[BEdita] unsubscribed to newsletter \"[\$title]\"",
	"mail_body" => "Hi [\$user], " .
					"\n\nyou have been unsubscribed from \"[\$title]\"",
	"viewsMsg" => "You have been unsubscribed from <b>\"[\$title]\"</b>."
);

// Message for recover password
$notify["recoverPassword"]["eng"] = array(
	"subject" => "[BEdita] username and password recovering",
	"mail_body" => "Hi [\$title], " .
					"\nyou have requested to recover username and/or password." .
					"\n\nYour username is: [\$user]" .
					"\n\nIf you don't remember your password click on the link below and follow the instructions" .
					"\n[\$url]",
	"viewsMsg" => "An email has been sent to you. Check your email and follow the instructions."
);

$notify["recoverPasswordChange"]["eng"] = array(
	"subject" => "[BEdita] password changed",
	"mail_body" => "Hi [\$user], " .
					"\nyou password has been changed." .
					"\n\nTo login go to" .
					"\n[\$url]",
	"viewsMsg" => "<a href='[\$url]'>Your password has been changed. Click here to login</a>"
);

// Message for user sign up
$notify["userSignUp"]["eng"] = array(
	"subject" => "[BEdita] user registration",
	"mail_body" => "Hi [\$user], " .
					"\nyou have been registered at [\$title]." .
					"\n\nIn order to activate your account please click on the following link:" .
					"\n\n[\$url]",
	"viewsMsg" => "An email has been sent to you. Please, check your email and follow the instructions to activate your account."
);

$notify["userSignUpActivation"]["eng"] = array(
	"subject" => "[BEdita] user activated",
	"mail_body" => "Hi [\$user], " .
					"\nyour account at [\$title] is now active." .
					"\n\nTo login go to" .
					"\n[\$url]",
	"viewsMsg" => "<a href='[\$url]'>Your account is now active. Click here to login</a>"
);
?>
