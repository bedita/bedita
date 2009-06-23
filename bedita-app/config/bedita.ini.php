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
 * bedita.ini.php - settings, constants, variables for BEdita local installation
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

$config = array() ;
 
$config["projectName"] = ""; // override in bedita.cfg

$config["userVersion"] = "BEdita 3.0"; // don't override

$config["majorVersion"] = "3.0.beta2"; // don't override -- admin/system

 /**
 ** ******************************************
 **  FileSystem Paths, URIs, Files defaults
 ** ******************************************
 */

// BEdita URL
$config['beditaUrl'] = "http://localhost/bedita"; // override in bedita.sys

// Multimedia - files' root folder on filesystem (use DS as Directory Separator, without trailing slash)
$config['mediaRoot'] = ROOT . DS . "media";


// Multimedia - URL prefix (without trailing slash)
$config['mediaUrl'] = 'http://localhost/media';


// Multimedia - image file substituting missing content (as now used in thumb smarty plugin)
$config['imgMissingFile'] = "/img/iconMissingImage_130x85.gif" ;


/**
 ** ******************************************
 **  Content and UI Elements defaults
 ** ******************************************
 */

// User Interface default language [see also 'multilang' below]
$config['Config']['language'] = "eng"; // or "ita", "eng", "spa", "por"

// Set 'multilang' true for user choice [also set 'multilang' true if $config['Config']['language'] is set]
$config['multilang'] = true;
$config['defaultLang'] = "eng"; // default fallback


/* Dates - default presentation format [syntax used by strftime php function]
 * It is used for datepicker calendar. Format has to contain %d,%m,%Y tags
 */
$config['datePattern'] 	= "%m-%d-%Y" ;  //

/* Date patterns for different locales (here language codes.... not completely correct)
 * It is used for datepicker calendar. Format has to contain %d,%m,%Y tags
 */
$config['datePatternLocale'] = array(
	"ita"	=> "%d/%m/%Y"
);

// Default date time pattern
$config['dateTimePattern'] 	= "%m-%d-%Y  %H:%M:%S";  //

$config['dateTimePatternLocale'] = array(
	"ita"	=> "%d/%m/%Y %H:%M:%S" 
);


// Default status of new objects
$config['defaultStatus'] = "draft" ;

// TinyMCE Rich Text Editor for long_text ['true' to enable]
$config['mce'] = true;


// Application messages - temporary messages duration
$config['msgPause'] = 3000;		// milliseconds


// Upload mode ['flash', 'ajax']
$config['uploadType'] = "flash";

// bedita user for unit test
$config['unitTestUserId'] = 1;

// activity timeout used in Home/"connected user" time duration in minutes
$config['activityTimeout'] = 20;

/**
 ** ******************************************
 **  Image and Video defaults
 ** ******************************************
 */
$config['media']['image']['thumbWidth']  = 130;      // px - was $config['thumbWidth']
$config['media']['image']['thumbHeight'] = 85;       // px - was $config['thumbHeight']
$config['media']['image']['thumbMode']   = "crop";   // crop, fill, croponly, stretch - thumb mode
$config['media']['image']['thumbFill']   = "FFFFFF"; // hex - fill color when thumb mode is fill
$config['media']['image']['thumbCrop']   = "C";      // string, crop mode when thumb mode is crop/croponly 'C', 'T', 'B', 'L', 'R', 'TL', 'TR', 'BL', 'BR'
$config['media']['image']['thumbQ']      = 75;       // int, JPEG thumbnail image quality [1-100]
$config['media']['image']['thumbUpscale']= false;     // bool, allow thumbnail upscale
$config['media']['image']['imagemagick'] = "";       // string, path to image_magick executable
$config['media']['image']['over']        = "";       // string, path to overlay image
$config['media']['image']['wmi']['f']    = "";       // string, path to watermark image file
$config['media']['image']['wmi']['a']    = "C";      // string, wm alignment B=bottom, T=top, L=left, R=right, C=centre, *=tile, 2 letters ie TL, or absolute position in px
$config['media']['image']['wmi']['o']    = 100;      // int, wm opacity 0 (transparent) to 100 (opaque)
 
$config['media']['video']['width']       = 300;      // px - was $config['videoWidth']
$config['media']['video']['height']      = 250;      // px - was $config['videoHeight']
$config['media']['video']['thumbWidth']  = 130;      // px - was $config['videoThumbWidth']
$config['media']['video']['thumbHeight'] = 85;       // px - was $config['videoThumbHeight']

$config['media']['audio']['width']       = 300;      // px - was $config['audioWidth']
$config['media']['audio']['height']      = 20;       // px - was $config['audioHeight']




/************************************************************
 * COMPATIBILITY - TO REMOVE - START
************************************************************/

$config['thumbWidth']       = $config['media']['image']['thumbWidth'] ;
$config['thumbHeight']      = $config['media']['image']['thumbHeight'] ;
$config['videoWidth']       = $config['media']['video']['width'];
$config['videoHeight']      = $config['media']['video']['height'] ;
$config['videoThumbWidth']  = $config['media']['video']['thumbWidth'] ;
$config['videoThumbHeight'] = $config['media']['video']['thumbHeight'] ;	
$config['audioWidth']       = $config['media']['audio']['width'] ;	
$config['audioHeight']      = $config['media']['audio']['height'] ;	

/************************************************************
 * COMPATIBILITY - TO REMOVE - END
************************************************************/




/**
 ** ******************************************
 **  Login (backend) and Security Policies
 ** ******************************************
 */

$config['maxLoginAttempts']     = 3;
$config['maxNumDaysInactivity'] = 180;
$config['maxNumDaysValidity']   = 60;

// Password
$config['passwdRegex']    = "/^(?=.*\d)(?=.*([a-z]|[A-Z]))([\x20-\x7E]){6,40}$/";
$config['passwdRegexMsg'] = "Your password must be at least 6 characters long and contain at least one number";


/**
 ** ******************************************
 **  More specific settings
 ** ******************************************
 */


/**
 ** Import PHP constants for smarty templates
 ** (since you cannot access php constants within Smarty) 
 */
$config['DS']        = DS;


/**
 * Modules/objects permissions
 */
define("BEDITA_PERMS_READ",	0x1) ; // read-only module permission
define("BEDITA_PERMS_MODIFY",	0x2) ;
define("BEDITA_PERMS_READ_MODIFY",	BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY) ; // read-write module permission
define("OBJ_PERMS_READ_FRONT",	0x1) ; // frontend access permission
define("OBJ_PERMS_WRITE",		0x2) ; // write permission on object

/**
 * Permission params, for Smarty 
 */
$config['BEDITA_PERMS_READ'] 	= BEDITA_PERMS_READ ;
$config['BEDITA_PERMS_MODIFY'] 	= BEDITA_PERMS_MODIFY ;
$config['BEDITA_PERMS_READ_MODIFY'] = BEDITA_PERMS_READ_MODIFY;
$config['OBJ_PERMS_READ_FRONT'] 	= OBJ_PERMS_READ_FRONT ;
$config['OBJ_PERMS_WRITE'] 	= OBJ_PERMS_WRITE ;


/**
 * BEdita modules
 */
$config['modules'] = array(
	"areas" => array("id" => 1, "label" => "publishing"),
	"admin" => array("id" => 2, "label" => "admin"),
	"translations" => array("id" => 3, "label" => "translations"),
	"documents" => array("id" => 6, "label" => "documents"),
	"news" => array("id" => 7, "label" => "news"),
	"galleries" => array("id" => 8, "label" => "galleries"),
	"events" => array("id" => 10, "label" => "events"),
	"bibliographies" => array("id" => 11, "label" => "bibliographies"),
	"webmarks" => array("id" => 12, "label" => "webmarks"),
	"books" => array("id" => 13, "label" => "books"),
	"questionnaires" => array("id" => 14, "label" => "questionnaires"),
	"addressbook" => array("id" => 16, "label" => "addressbook"),
	"newsletter" => array("id" => 18, "label" => "newsletter"),
	"statistics" => array("id" => 23, "label" => "statistics"),
	"tags" => array("id" => 24, "label" => "tags"),
	"comments" => array("id" => 25, "label" => "comments"),
	"multimedia" => array("id" => 26, "label" => "multimedia"),
);

/**
 * object types, main properties
 */
$config['objectTypes'] = array(
	1			=> array("id" => 1, "name" => "area", "module" => "areas", "model" => "Area"),
	"area"		=> array("id" => 1, "name" => "area", "module" => "areas", "model" => "Area"),
	
	3			=> array("id" => 3, "name" => "section", "module" => "areas", "model" => "Section"),
	"section"	=> array("id" => 3, "name" => "section", "module" => "areas", "model" => "Section"),
	
	10			=> array("id" => 10, "name" => "befile", "module" => "multimedia", "model" => "BEFile"),
	"befile"	=> array("id" => 10, "name" => "befile", "module" => "multimedia", "model" => "BEFile"),
	
	12			=> array("id" => 12, "name" => "image", "module" => "multimedia", "model" => "Image"),
	"image"		=> array("id" => 12, "name" => "image", "module" => "multimedia", "model" => "Image"),
	
	13			=> array("id" => 13, "name" => "comment", "module" => "comments", "model" => "Comment"),
	"comment"	=> array("id" => 13, "name" => "comment", "module" => "comments", "model" => "Comment"),
	
	18			=> array("id" => 18, "name" => "shortnews", "module" => "news", "model" => "ShortNews"),
	"shortnews"	=> array("id" => 18, "name" => "shortnews", "module" => "news", "model" => "ShortNews"),
	
	19			   => array("id" => 19, "name" => "bibliography", "module" => "bibliographies", "model" => "Bibliography"),
	"bibliography" => array("id" => 19, "name" => "bibliography", "module" => "bibliographies", "model" => "Bibliography"),
	
	20			=> array("id" => 20, "name" => "book", "module" => "books", "model" => "Book"),
	"book"		=> array("id" => 20, "name" => "book", "module" => "books", "model" => "Book"),
	
	21			=> array("id" => 21, "name" => "event", "module" => "events", "model" => "Event"),
	"event"		=> array("id" => 21, "name" => "event", "module" => "events", "model" => "Event"),
	
	22			=> array("id" => 22, "name" => "document", "module" => "documents", "model" => "Document"),
	"document"	=> array("id" => 22, "name" => "document", "module" => "documents", "model" => "Document"),
	
	29			=> array("id" => 29, "name" => "gallery", "module" => "galleries", "model" => "Gallery"),
	"gallery"	=> array("id" => 29, "name" => "gallery", "module" => "galleries", "model" => "Gallery"),
	
	31			=> array("id" => 31, "name" => "audio", "module" => "multimedia", "model" => "Audio"),
	"audio"		=> array("id" => 31, "name" => "audio", "module" => "multimedia", "model" => "Audio"),
	
	32			=> array("id" => 32, "name" => "video", "module" => "multimedia", "model" => "Video"),
	"video"		=> array("id" => 32, "name" => "video", "module" => "multimedia", "model" => "Video"),
	
	33			=> array("id" => 33, "name" => "link", "module" => "webmarks", "model" => "Link"),
	"link"		=> array("id" => 33, "name" => "link", "module" => "webmarks", "model" => "Link"),
	
	34			=> array("id" => 34, "name" => "card", "module" => "addressbook", "model" => "Card"),
	"card"		=> array("id" => 34, "name" => "card", "module" => "addressbook", "model" => "Card"),
	
	35			  => array("id" => 35, "name" => "mailmessage", "module" => "newsletter", "model" => "MailMessage"),
	"mailmessage" => array("id" => 35, "name" => "mailmessage", "module" => "newsletter", "model" => "MailMessage"),
	
	36			   => array("id" => 36, "name" => "mailtemplate", "module" => "newsletter", "model" => "MailTemplate"),
	"mailtemplate" => array("id" => 36, "name" => "mailtemplate", "module" => "newsletter", "model" => "MailTemplate"),
	
	37			 => array("id" => 37, "name" => "author", "module" => null, "model" => "Author"),
	"author" 	 => array("id" => 37, "name" => "author", "module" => null, "model" => "Author"),
	
	38			 => array("id" => 38, "name" => "biblioitem", "module" => null, "model" => "BiblioItem"),
	"biblioitem" 	 => array("id" => 38, "name" => "biblioitem", "module" => null, "model" => "BiblioItem"),
	
	39			 => array("id" => 39, "name" => "editornote", "module" => null, "model" => "EditorNote"),
	"editornote" 	 => array("id" => 39, "name" => "editornote", "module" => null, "model" => "EditorNote"),
	
	40			 	=> array("id" => 40, "name" => "question", "module" => "questionnaires", "model" => "Question"),
	"question" 		=> array("id" => 40, "name" => "question", "module" => "questionnaires", "model" => "Question"),
	
	41			 	=> array("id" => 41, "name" => "questionnaire", "module" => "questionnaires", "model" => "Questionnaire"),
	"questionnaire" => array("id" => 41, "name" => "questionnaire", "module" => "questionnaires", "model" => "Questionnaire"),
	
	42			 	=> array("id" => 42, "name" => "questionnaireresult", "module" => "questionnaires", "model" => "QuestionnaireResult"),
	"questionnaireresult" => array("id" => 42, "name" => "questionnaireresult", "module" => "questionnaires", "model" => "QuestionnaireResult"),
	
	// define array of objects that can be related to other
	'related'	=> array("id" => array(18,19,20,21,22,29,34,41)),

	// define array of objects that are leafs of the tree
	'leafs'		=> array("id" => array(18,19,20,21,22,29,34,41))
);


/*
 * Relations - default objects' relation types
 * 
 * Array("defaultObjRelationType" => 
 * 		array(
 * 			"hidden" => define if relation is hidden. Used in object's view in relationship tab,
 * 			"left" => array of object_type_id related to right object_type_id (empty array means all object_type_id) 
 * 			"right => array of object_type_id related to left object_type_id (empty array means all object_type_id)
 * 			
 * OPTIONAL "objectType" => array of object_type_id related to objectType. If defined it's used instead of 'left', 'right'
 * 		)
 *	)
 * 
 */
$config["defaultObjRelationType"] = array(

	"language" => array(
		"hidden" => true,
		"left" 		=> array(),
		"right" 		=> array()	
	),
	"seealso" => array(
		"hidden" => false,
		"left" 		=> array(18,19,20,21,22,29,34),
		"right" 		=> array()	
	),
	"download" => array(
		"hidden" => false,
		"left" 		=> array(10,12,31,32),
		"right" 		=> array()	
	),
	"gallery" => array(
		"hidden" => true,
		"left" 		=> array(29),
		"right" 		=> array()	
	),
	"attach" => array(
		"hidden" => true,
		"left" => array(10,12,31,32),
		"right" => array()
	),
	"link" => array(
		"hidden" => true,
		"left" => array(33),
		"right" => array()
	),
	"author" => array(
		"hidden" => true,
		"left" => array(20),
		"right" => array(37)
	),
	"bibliography" => array(
		"hidden" => true,
		"left" => array(19),
		"right" => array(38)
	),
	"question" => array(
		"hidden" => true,
		"left" => array(41),
		"right" => array(40)
	)
);

// Relations - local objects' relation types
$config["objRelationType"] = array ();

// One-way relation
$config["defaultOneWayRelation"] = array();

// Cfg One-way relation (as in local cfg)
$config["cfgOneWayRelation"] = array();

// Default reserved words [avoided in nickname creation]
$config["defaultReservedWords"] = array("section", "content", "rss", "feed", 
	"download", "xml", "xmlobject", "sitemap", "sitemap.xml", "sitemap_xml", 
	"json", "captchaImage", "saveComment", "search", "tag", "login", "logout");

// Cfg reserved words (as in local cfg)
$config["cfgReservedWords"] = array();

// download - redirect extensions to mediaURL [FrontenController::download]
$config["redirectExtensionsDownload"] = array ("gz", "tar", "zip");
// download - redirect mimetypes to mediaURL [FrontenController::download]
$config["redirectMimeTypesDownload"] = array ();

/**
 * Session handling parameters
 */
$config['session'] = array (
	"sessionUserKey"	=> "BEAuthUser", // Nome con cui salvato in sessione info. utente connesso
) ;


/**
 * question types
 */
$config['questionTypes'] = array(
	"multiple" => "multiple choiche",
	"single_radio" => "radio single choice",
	"single_pulldown" => "pulldown single choice",
	"freetext" => "open answer",
	"checkopen" => "check open",
	"degree" => "degree"
);

/**
 * question difficulty 
 */
$config['questionDifficulty'] = array(
	1 => "very easy",
	2 => "easy",
	3 => "normal",
	4 => "hard",
	5 => "very hard"
);


/**
 * education Level 
 * TODO: i18n!! how??
 */
$config['eduLevel'] = array(
	1 => "scuola primaria",
	2 => "scuola secondaria di primo grado",
	3 => "scuola secondaria di secondo grado",
	4 => "università"
);

/**
 * 
 * Options status select
 * 
 */
$config['statusOptions'] = array(
	"on"	=> "ON",
	"off"	=> "OFF",
	"draft"	=> "DRAFT"
) ;

/**
 * Lang selection options ISO-639-3 - Default language options for contents
 */
$config['langOptions'] = array(
		"ara" => "Arabic",
		"bul" => "Bulgarian",
		"cat" => "Catalan",
		"zho" => "Chinese",
		"hrv" => "Croatian",
		"ces" => "Czech",
		"dan" => "Danish",
		"nld" => "Dutch",
		"eng" => "English",
		"fin" => "Finnish",
		"fra" => "French",
		"deu" => "German",
		"ell" => "Greek",
		"heb" => "Hebrew",
		"hin" => "Hindi",
		"ita" => "Italian",
		"jpn" => "Japanese",
		"kor" => "Korean",
		"lav" => "Latvian",
		"lit" => "Lithuanian",
		"nor" => "Norwegian",
		"pol" => "Polish",
		"por" => "Portuguese",
		"ron" => "Romanian",
		"rus" => "Russian",
		"srp" => "Serbian",
		"slk" => "Slovak",
		"slv" => "Slovenian",
		"spa" => "Spanish",
		"swe" => "Swedish",
		"ukr" => "Ukrainian",
		"vie" => "Vietnamese"
) ;

// ISO-639-3 codes - User interface language options (backend)
$config['langsSystem'] = array(
	"eng"	=> "english",
	"ita"	=> "italiano",
	"deu"	=> "deutsch",
	"por"	=> "portuguěs"

) ;

// maps ISO-639-2 known codes to ISO-639-3
$config['langsSystemMap'] = array(
	"it"	=> "ita",
	"en"	=> "eng",
	"en_us"	=> "eng",
	"en_gb"	=> "eng",
	"es"	=> "spa",
	"pt"	=> "por",
	"pt_br"	=> "por"
) ;

// 'langsIso' empty, overridden if 'langOptionsIso' is true
$config['langsIso'] = array();
// add langs.iso.php to language options for content 
$config['langOptionsIso'] = false;

// media types for multimedia association
$config['mediaTypes'] = array('image','video','audio','text','spreadsheet','presentation','drawing','chart','formula');


/**
 * Variabili utilizza per il riconscimento e gestione URL remoti e file remoti
 */
$config['validate_resorce'] = array(
	'paranoid'	=> true,	/**
							 * Se true, non accetta remote URL se 'allow_url_fopen'
							 * e' a false.
							 * 
							 * False, usa l'URL passato e il MIME type viene passato con i dati. Size non
							 * puo' essere determinata. Opzione utilizzabile nei casi in cui allow_url_fopen
							 * non permette l'uso di file remoti.
							 **/
							
	'URL'	=> '/^\s*[a-z\d\+\-\.]+\:\/\//i',
	/**
	 *  Inserire tutte le regole che si vogliono. L'URL non e' accettato se non passa almeno 1 regola data
	 */
	'allow'	=> array( 
				'/^\s*http:\/\/(.*)\.(html|htm)$/',
				'/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/'
			   ),
	/**
	 * Accepted MIME types for different obj types
	 */
	'mime'	=> array(
				'Image'			=> array('/image\/\.*/'),
				'Audio'			=> array('/audio\/\.*/'),
				'Video'			=> array('/video\/\.*/'),
				'BEFile'		=> array('/application\/\.*/', '/text\/\.*/', '/beexternalsource/')
				)
) ;

/**
 *  Videos from external media provider
 */
$config['media_providers'] = array(
	"youtube"	=> array(
			'/^http:\/\/\w{3}\.youtube\.com\/watch\?v=(.[^&]+)/',
			'/^http:\/\/youtube\.com\/watch\?v=(.[^&]+)/',
			'/^http:\/\/[a-z]{2}\.youtube\.com\/watch\?v=(.[^&]+)/'

	) ,
	"blip"	=> array(
			'/^http:\/\/\w{3}\.blip\.tv\/file\/(\d+)\?{0,1}.*/',
			'/^http:\/\/blip\.tv\/file\/(\d+)\?{0,1}.*/'
	),
	"vimeo" => array(
			'/^http:\/\/\w{3}\.vimeo\.com\/(\d+)/',
			'/^http:\/\/vimeo\.com\/(\d+)/'
	)
) ;

/**
 *  media provider config
 */
$config['provider_params']  = array(
	"youtube"	=> array(
		"width" 	=> 320,
		"height" 	=> 200,
		"urlthumb"	=> "http://i.ytimg.com/vi/%s/default.jpg",
		"embedTag" 	=> "<embed src='http://www.youtube.com/v/%s%s' type='application/x-shockwave-flash' wmode='transparent' width='%d' height='%d'></embed>"
	),
	"blip" => array(
		"width"		=> 320,
		"height"	=> 200,
		"urlinfo" 	=> "http://www.blip.tv/file/%s?skin=json",
		"urlembed"	=> "http://www.blip.tv/players/embed/?posts_id=%s&players_id=-1&skin=json&callback=DoSomethingActions.playerSelector.gotEmbedCode"
	),
	"vimeo" => array(
		"width"		=> 320,
		"height"	=> 200,
		"urlinfo" 	=> "http://vimeo.com/api/clip/%s.%s",
		"urlembed"	=> "http://vimeo.com/api/oembed.json?url=%s"
	)
) ;

/**
 *  Default model bindings for Containable Behaviour 
 */
$config['modelBindings'] = array() ;


/**
 *  Default model bindings for Containable Behaviour 
 */
$config['searchFields'] = array() ;

/**
 * Default css filename for newsletter templates
 */
$config['newsletterCss'] = "base.css";

/**
 * Default value in milliseconds, between autosave of objects (status draft or off)
 */
$config['autosaveTime'] = 120000; // two minutes

/**
 * Specific System settings
 * ALWAYS AT THE END of bedita.ini.php
 */
$appPath = (defined("BEDITA_CORE_PATH"))? BEDITA_CORE_PATH . DS : APP;
if (file_exists ($appPath."config".DS."bedita.sys.php") ) {
	include_once($appPath."config".DS."bedita.sys.php") ;	
}
unset($appPath);
?>