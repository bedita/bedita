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
 * frontend.ini.php - bedita frontend parameters + backend overrides 
 *  
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

require BEDITA_CORE_PATH . DS . "config" . DS . "bedita.ini.php";
include APP. "config" . DS . "mapping.cfg.php";	

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

/**
 * user validation delegated to user himself with email confirm (false)
 * or moderated by administrators in User module (true or administrator's email)
 */
$config['authorizedGroups'] = array();


/**
 * user validation delegated to user himself with email confirm (false)
 * or moderated by administrators in User module (true)
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
								 	"eng"	=> array("en", "english"),
								 	"ita"	=> array("it", "italiano"),
								/*	
									"spa"	=> array("es", "espa&ntilde;ol"),
									"por"	=> array("pt", "portugu&ecirc;s"),
									"fra"	=> array("fr", "fran&ccedil;oise"),
									"deu"	=> array("de", "deutsch"),
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
	'Application' => array("BEObject" => array("LangText"), "Content", "Stream"),
	'Audio' => array("BEObject" => array("LangText"), "Content"),
	'BEFile' => array("BEObject" => array("LangText"), "Content"),
	'Card' => array("BEObject" => array("LangText","RelatedObject"), "GeoTag"),
	'Comment' => array("BEObject" => array("RelatedObject"), "GeoTag"),
	'Document' => array("BEObject" => array("LangText","UserCreated","RelatedObject", "Category", "Annotation")),
	'Event' => array("BEObject" => array("LangText","UserCreated","RelatedObject", "Category"), "DateItem"),
	'Gallery' => array("BEObject" => array("LangText","RelatedObject", "Category")),
	'Image' => array("BEObject" => array("LangText"), "Content", "Stream"),
	'Link' => array("BEObject" => array("LangText","RelatedObject")),
	'Section' => array("BEObject" => array("LangText")),
	'ShortNews' => array("BEObject" => array("LangText","UserCreated","RelatedObject", "Category")),
	'Video' => array("BEObject" => array("LangText"), "Content", "Stream"),

) ;

/**
 * frontend cookie names 
 */
$config["cookieName"] = array(
	"langSelect" => "basicExampleLang"
);

?>