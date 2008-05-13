<?php
/**
  * @author ste@channelweb.it
 * 
 *  frontend.ini.php - constants and global variables for bedita frontend + backend overrides
 * 
 */
 
require_once(BEDITA_CORE_PATH . DS . "config" . DS . "bedita.ini.php") ;

//////////////////////////////////////////////////////
// EDITABLE STUFF         ///////////////
//////////////////////////////////////////////////////

$config['authorizedGroups'] = array('frontend');

$config['draft']            = false;
$config['frontendNickname'] = 'nomefrontend';
$config['frontendAreaId'] = 1;
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

/*
 * model bindings
 */
$config['modelBindings'] = array(

	'Area' => array("BEObject" => array("LangText")),
	'Section' => array("BEObject" => array("LangText")),
 	'Document' => array("BEObject" => array("LangText" ),
				"ContentBase" => array("*"), "Content","BaseDocument"),
	'Event' => array("BEObject" => array("LangText"),
				"ContentBase" => array("*"), "Content","BaseDocument","EventDateItem"),
	'Image' => array("BEObject" => array("LangText"),
				"ContentBase", "Stream"),
	'Audio' => array("BEObject" => array("LangText"),
				"ContentBase", "Stream"),
	'Video' => array("BEObject" => array("LangText"),
				"ContentBase", "Stream"),
	'BEFile' => array("BEObject" => array("LangText"),
				"ContentBase", "Stream"),
	'Gallery' => array("BEObject" => array("LangText"))

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