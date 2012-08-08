<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
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
 * custom model bindings for BEdita objects (defaults defined in Model of BEdita object)
 */
//$config['modelBindings'] = array(
	//'Document' => array("BEObject" => array("LangText","RelatedObject", "GeoTag")),
	//'Event' => ...
	//...
//);

/**
 * frontend cookie names 
 */
$config["cookieName"] = array(
	"langSelect" => "basicExampleLang"
);

?>