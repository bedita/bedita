<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * frontend.ini.php - constants and global variables for bedita frontend + backend overrides 
 *  
 * @version			$Revision: 2061 $
 * @modifiedby 		$LastChangedBy: dante $
 * @lastmodified	$LastChangedDate: 2009-07-03 16:44:07 +0200 (ven, 03 lug 2009) $
 * 
 * $Id: frontend.ini.php 2061 2009-07-03 14:44:07Z dante $
 */

require_once(BEDITA_CORE_PATH . DS . "config" . DS . "bedita.ini.php") ;
if (file_exists (BEDITA_CORE_PATH . DS . "config" . DS . "bedita.cfg.php") ) {
	include(BEDITA_CORE_PATH . DS . "config" . DS . "bedita.cfg.php") ;	
}
if (file_exists (APP. "config" . DS . "mapping.cfg.php") ) {
	include(APP. "config" . DS . "mapping.cfg.php") ;	
}

//////////////////////////////////////////////////////
// EDITABLE STUFF         ///////////////
//////////////////////////////////////////////////////

/**
 * Publication id referenced by frontend, 
 * change if different from id = 1 (default)
 */
$config['frontendAreaId'] 	= 1;


/**
 * show or not objects with status = draft
 * default: show only objects with status = ON
 */ 
$config['draft']            = false;


/**
 * staging site ? default: false -> production site
 */  
$config['staging'] 			= false;


/**
 * check the objects publication date and throw a 404 error if the publication date of the object requested 
 * is expired or is in the future
 */   
$config['filterPublicationDate'] = true;

/**
 * array of frontend groups that can access frontend
 * leave empty to define permissions at object level
 */
$config['authorizedGroups'] = array();


/**
 * user validation delegated to user himself with email confirm (false)
 * or moderated by administrators in User module (true or administrator's email)
 */
$config['userModerateSignup'] = false;


/**
 * default frontend language
 */
$config['frontendLang']     = 'eng';

/**
 * supported frontend languages
 */
$config['frontendLangs']    = array (
									"eng"	=> "english",
									"ita"	=> "italiano",
								/*	
									"spa"	=> "espa&ntilde;ol",
									"por"	=> "portugu&ecirc;s",
									"fra"	=> "fran&ccedil;oise",
									"deu"	=> "deutsch"
								*/
								);

/**
 * maps of languages to autodetecting language choice 
 */
$config['frontendLangsMap'] = array(
	"it"	=> "ita",
	"en"	=> "eng",
	"en_us"	=> "eng",
	"en-us"	=> "eng",
	"en_gb"	=> "eng"
) ;
								

/**
 * show all contents in sitemap. If it's false show only sections' tree
 */ 
$config['sitemapAllContent'] = true;


/**
 * default model bindings for BEdita objects
 */
$config['modelBindings'] = array(

	'Area' => array("BEObject" => array("LangText")),
	'Application' => array("BEObject" => array("LangText"), "Content", "Stream"),
	'Audio' => array("BEObject" => array("LangText"), "Content"),
	'BEFile' => array("BEObject" => array("LangText"), "Content"),
	'Card' => array("BEObject" => array("LangText","RelatedObject"), "GeoTag"),
	'Comment' => array("BEObject" => array("RelatedObject"), "GeoTag"),
	'Document' => array("BEObject" => array("LangText", "UserCreated","RelatedObject", "Category", "Annotation"), "GeoTag"),
	'Event' => array("BEObject" => array("LangText", "UserCreated", "RelatedObject", "Category", "Annotation"), "DateItem", "GeoTag"),
	'Gallery' => array("BEObject" => array("LangText", "UserCreated", "RelatedObject", "Category", "Annotation")),
	'Image' => array("BEObject" => array("LangText"), "Content", "Stream"),
	'Link' => array("BEObject" => array("LangText","RelatedObject")),
	'Section' => array("BEObject" => array("LangText"), "Tree"),
	'ShortNews' => array("BEObject" => array("LangText", "UserCreated", "RelatedObject", "Category")),
	'Video' => array("BEObject" => array("LangText"), "Content", "Stream"),

) ;

/**
 * frontend cookie names 
 */
$config["cookieName"] = array(
	"langSelect" => "basicExampleLang"
);

/**
 * save history navigation
 
 * "sessionEntry" => number of history items in session
 * "showDuplicates" => false to not show duplicates in history session 
 * "trackNotLogged" => true save history for all users (not logged too)
 */
//$config["history"] = array(
//	"sessionEntry" => 5,
//	"showDuplicates" => false,
//	"trackNotLogged" => false
//);

?>