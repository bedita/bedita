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
$config['sitemapAllContent'] = true;
$config['staging'] 			= false;
$config['draft']            = false;
$config['filterPublicationDate'] = true;
$config['frontendAreaId'] 	= 1;
$config['frontendUser']     = array ("userid" => null);
$config['authorizedGroups'] = array();
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
 	'Document' => array("BEObject" => array("LangText", "UserCreated","RelatedObject", "Annotation", "Category")),
	'Event' => array("BEObject" => array("LangText","RelatedObject", "Category"), "DateItem"),
	'Image' => array("BEObject" => array("LangText"), "Content", "Stream"),
	'Audio' => array("BEObject" => array("LangText"), "Content"),
	'Video' => array("BEObject" => array("LangText"), "Content", "Stream"),
	'BEFile' => array("BEObject" => array("LangText"), "Content"),
	'Gallery' => array("BEObject" => array("LangText", "RelatedObject")),
	'Comment' => array("BEObject", "GeoTag"),
	'ShortNews' => array("BEObject" => array("LangText","RelatedObject", "Category")),
	'Card' => array("BEObject" => array("LangText","RelatedObject"), "GeoTag")

) ;

$config["cookieName"] = array(
	"langSelect" => "nomeFrontendLang"
);


?>