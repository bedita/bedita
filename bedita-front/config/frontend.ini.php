<?php
/**
  * @author ste@channelweb.it
 * 
 *  frontend.ini.php - constants and global variables for bedita frontend + backend overrides
 * 
 */
 
require_once(BEDITA_CORE_PATH . DS . "config" . DS . "bedita.ini.php") ;
if (file_exists (BEDITA_CORE_PATH . DS . "config" . DS . "bedita.cfg.php") ) {
	include(BEDITA_CORE_PATH . DS . "config" . DS . "bedita.cfg.php") ;	
}

//////////////////////////////////////////////////////
// EDITABLE STUFF         ///////////////
//////////////////////////////////////////////////////

$config['draft']            = false;
$config['frontendNickname'] = 'nomefrontend';
$config['frontendAreaId'] 	= 1;
$config['frontendUser']     = array ("userid" => null);
$config['frontendLang']     = 'ita';
$config['frontendLangs']    = array (
									"ita"	=> "italiano",
								/*	
									"eng"	=> "english",
									"spa"	=> "espa&ntilde;ol",
									"por"	=> "portugu&ecirc;s",
									"fra"	=> "fran&ccedil;oise",
									"deu"	=> "deutsch"
								*/
								);
								
$config['frontendLangsMap'] = array(
	"it"	=> "ita",
	"en"	=> "eng",
	"en_us"	=> "eng",
	"en-us"	=> "eng",
	"en_gb"	=> "eng"
) ;
								
/*
 * model bindings
 */
$config['modelBindings'] = array(

	'Area' => array("BEObject" => array("LangText")),
	'Section' => array("BEObject" => array("LangText")),
 	'Document' => array("BEObject" => array("LangText", "UserCreated","RelatedObject", "Category")),
	'Event' => array("BEObject" => array("LangText","RelatedObject", "Category"), "DateItem"),
	'Image' => array("BEObject" => array("LangText"), "Content", "Stream"),
	'Audio' => array("BEObject" => array("LangText"), "Content"),
	'Video' => array("BEObject" => array("LangText"), "Content", "Stream"),
	'BEFile' => array("BEObject" => array("LangText"), "Content"),
	'Gallery' => array("BEObject" => array("LangText")),
	'Comment' => array("BEObject" => array("RelatedObject"), "Content"),
	'ShortNews' => array("BEObject" => array("LangText","RelatedObject", "Category"))

) ;

$config["cookieName"] = array(
	"langSelect" => "nomeFrontendLang"
);

$config['blip'] = array(
	"width"		=> 320,
	"height"	=> 200,
	"urlinfo" 	=> "http://www.blip.tv/file/%s?skin=json",
	"urlembed" 	=> "http://blip.tv/syndication/copypaste/?item_type=file&id=%s&skin=json&callback=DoSomethingActions.legacySelector.gotEmbedCode",
	"urlembed"	=> "http://www.blip.tv/players/embed/?posts_id=%s&players_id=-1&skin=json&callback=DoSomethingActions.playerSelector.gotEmbedCode"
) ;

$config['youtube'] = array(
	"width"		=> 320,
	"height"	=> 200,
) ;

?>