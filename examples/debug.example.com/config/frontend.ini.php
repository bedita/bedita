<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2011 ChannelWeb Srl, Chialab Srl
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
 * frontend.ini.php - constants and global variables for bedita frontend + backend overrides 
 *  
 * @version			$Revision: 2061 $
 * @modifiedby 		$LastChangedBy: dante $
 * @lastmodified	$LastChangedDate: 2009-07-03 16:44:07 +0200 (ven, 03 lug 2009) $
 * 
 * $Id: frontend.ini.php 2061 2009-07-03 14:44:07Z dante $
 */

require BEDITA_CORE_PATH . DS . "config" . DS . "bedita.ini.php";

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
 * external auth params
 */
/*$config['extAuthParams'] = array(
	'facebook' => array(
		'kies' => array(
			'appId' => 'aaa',
			'secret' => 'bbb'
		),
		'permissions' => 'email, user_birthday',
		'groups' => array('frontend', 'editor', 'administrator'),
		'createCard' => true
	),
	'twitter' => array(
		'kies' => array(
			'consumerKey' => 'ccc',
			'consumerSecret' => 'ddd'
		),
		'groups' => array('frontend', 'editor', 'administrator'),
		'createCard' => true
	),
	'google' => array(
		'kies' => array(
			'clientId' => 'eee',
			'clientSecret' => 'fff'
		),
		'scopes' => array(
			'email',
			'profile'
		),
		'groups' => array('frontend', 'editor', 'administrator'),
		'createCard' => true
	)
);*/
								

/**
 * show all contents in sitemap. If it's false show only sections' tree
 */ 
$config['sitemapAllContent'] = true;


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
	"langSelect" => "debugExampleLang"
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

// frontend.cfg 
if(file_exists(	APP. "config" . DS . "frontend.cfg.php")) {
	include APP. "config" . DS . "frontend.cfg.php";
}

?>