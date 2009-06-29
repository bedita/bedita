<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

/**
 *  frontend.ini.php - constants and global variables for bedita frontend + backend overrides 
 */
 
require_once(BEDITA_CORE_PATH . DS . "config" . DS . "bedita.ini.php") ;
if (file_exists (BEDITA_CORE_PATH . DS . "config" . DS . "bedita.cfg.php") ) {
	include(BEDITA_CORE_PATH . DS . "config" . DS . "bedita.cfg.php") ;	
}

//////////////////////////////////////////////////////
// EDITABLE STUFF         ///////////////
//////////////////////////////////////////////////////

/**
 * show all contents in sitemap. If it's false show only sections' tree
 */ 
$config['sitemapAllContent'] = true;

/**
 * staging site
 */  
$config['staging'] 			= false;

/**
 * show or not objects with status = draft
 */ 
$config['draft']            = false;

/**
 * check the objects publication date and throw a 404 error if the publication date of the object requested 
 * is expired or is in the future
 */   
$config['filterPublicationDate'] = true;

/**
 * Publication id referenced by frontend
 */
$config['frontendAreaId'] 	= 1;
//$config['frontendUser']     = array ("userid" => null); not used anymore...

/**
 * array of frontend groups that can access to frontend reserved areas
 * leave empty to permitt the access at all frontend groups
 */
$config['authorizedGroups'] = array();

/**
 * default frontend language
 */
$config['frontendLang']     = 'ita';

/**
 * supported frontend languages
 */
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
 * default model bindings for BEdita objects
 */
$config['modelBindings'] = array(

	'Area' => array("BEObject" => array("LangText")),
	'Section' => array("BEObject" => array("LangText")),
 	'Document' => array("BEObject" => array("LangText", "RelatedObject", "Annotation", "Category")),
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

/**
 * frontend cookie name 
 */
$config["cookieName"] = array(
	"langSelect" => "nomeFrontendLang"
);


?>