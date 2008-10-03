<?php
/**
 ** @author ChannelWeb srl - BEdita staff
 ** 
 ** bedita.ini.php - settings, constants, variables for BEdita local installation
 ** 
 */
$config = array() ;
 
 
 
//////////////////////////////////////////////////////
// EDITABLE STUFF                      ///////////////
//////////////////////////////////////////////////////


/**
 ** ******************************************
 **  FileSystem Paths, URIs, Files defaults
 ** ******************************************
 */

// Multimedia - files' root folder on filesystem (use DS as Directory Separator, without trailing slash)
$config['mediaRoot'] = ROOT . DS . "media";


// Multimedia - URL prefix (without trailing slash)
$config['mediaUrl'] = 'http://localhost/media';


// Multimedia - cache folder (without trailing slashes, inside 'mediaRoot')
$config['imgCache'] = 'imgcache';


// Multimedia - image file substituting missing content (as now used in thumb smarty plugin)
$config['imgMissingFile'] = "/img/iconMissingImage_130x85.gif" ;


// FileSystem directory for temporary file storage
$config['tmp'] = "/tmp" ;







/**
 ** ******************************************
 **  Content and UI Elements defaults
 ** ******************************************
 */

// User Interface default language [see also 'multilang' below]
$config['Config']['language'] = "ita"; // or "eng", "spa", "por"

// Set 'multilang' true for user choice [also set 'multilang' true if $config['Config']['language'] is set]
$config['multilang'] = true;
$config['defaultLang'] = "ita"; // default fallback


// Dates - default presentation format [syntax used by strftime php function]
$config['datePattern'] 	= "%d/%m/%Y" ;  //

// Date patterns for different locales (here language codes.... not completely correct)
$config['datePatternLocale'] = array(
	"eng"	=> "%m-%d-%Y",
);

// Default date time pattern
$config['dateTimePattern'] 	= "%d/%m/%Y %H:%M:%S" ;  //

$config['dateTimePatternLocale'] = array(
	"eng"	=> "%m-%d-%Y  %H:%M:%S"
);

// Dates - validation format [day=dd, month=mm, year=yyyy]
$config['dateFormatValidation'] = "dd/mm/yyyy" ;

// Texts in documents ['html', 'txt', 'txtParsed']
$config['type'] = "txt" ;  // ------ SISTEMARE ------------
//$config['defaultTxtType'] = "txt" ;


// Status of new objects
$config['status'] = "draft" ;  // ------ SISTEMARE ------------
//$config['defaultStatus'] = "draft" ;


// TinyMCE Rich Text Editor for long_text ['true' to enable]
$config['mce'] = true;


// Application messages - temporary messages duration
$config['msgPause'] = 3000;		// milliseconds


// Upload mode ['flash', 'ajax']
$config['uploadType'] = "flash";


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
$config['media']['image']['thumbUpscale']= true;     // bool, allow thumbnail upscale
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
 * COMPATIBILITA' - RIMUOVERE - START
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
 * COMPATIBILITA' - RIMUOVERE - END
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


// Groups
$config['authorizedGroups'] = array ('administrator', 'editor', 'reader');

// Predefined groups
$config['basicGroups']      = array ('administrator', 'editor', 'reader', 'guest');



/**
 ** ******************************************
 **  More specific settings
 ** ******************************************
 */

// Relations - local objects' relation types
$config["objRelationType"] = array ();

// One-way relation
$config["defaultOneWayRelation"] = array("link");

// Cfg One-way relation (as in local cfg)
$config["cfgOneWayRelation"] = array();

// Cfg One-way relation (as in local cfg)
$config["cfgOneWayRelation"] = array();

// Default reserved words [avoided in nickname creation]
$config["defaultReservedWords"] = array("section", "content", "rss", "feed");

// Cfg reserved words (as in local cfg)
$config["cfgReservedWords"] = array();


///////////////////////////////////////////////////////
// DO NOT EDIT BELOW THIS LINE//
///////////////////////////////////////////////////////

/**
 ** Import PHP constants for smarty templates
 ** (since you cannot access php constants within Smarty) 
 */
$config['DS']        = DS;


/**
 * Costanti per la definizione dei permessi
 */
define("BEDITA_PERMS_READ",		0x1) ;
define("BEDITA_PERMS_MODIFY",	0x2) ;
define("BEDITA_PERMS_DELETE",	0x4) ;
define("BEDITA_PERMS_CREATE",	0x8) ;

define("BEDITA_PERMS_READ_MODIFY",	BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY) ;

/**
 * Costanti per la definizione delle tipologie
 * di domande
 */
define("BEDITA_DOMANDA_MULTIPLA",		0x1) ;
define("BEDITA_DOMANDA_SINGOLA",		0x2) ;
define("BEDITA_DOMANDA_TXTLIBERO",		0x3) ;
define("BEDITA_DOMANDA_CHECKOPEN",		0x4) ;
define("BEDITA_DOMANDA_GRADO",			0x5) ;
define("BEDITA_DOMANDA_TXTSEMPLICE",	0x6) ;



/**
 * definisce i tipi i oggetti ammessi
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
	
	33			=> array("id" => 33, "name" => "link", "module" => null, "model" => "Link"),
	"link"		=> array("id" => 33, "name" => "link", "module" => null, "model" => "Link"),
	
	34			=> array("id" => 34, "name" => "card", "module" => "addressbook", "model" => "Card"),
	"card"		=> array("id" => 34, "name" => "card", "module" => "addressbook", "model" => "Card"),
	
	35			  => array("id" => 35, "name" => "mailmessage", "module" => "newsletter", "model" => "MailMessage"),
	"mailmessage" => array("id" => 35, "name" => "mailmessage", "module" => "newsletter", "model" => "MailMessage"),
	
	36			   => array("id" => 36, "name" => "mailtemplate", "module" => "newsletter", "model" => "MailTemplate"),
	"mailtemplate" => array("id" => 36, "name" => "mailtemplate", "module" => "newsletter", "model" => "MailTemplate"),
	
	// define array of objects that can be related to other
	'related'	=> array("id" => array(18,19,20,21,22,29,34)),

	// define array of objects that are leafs of the tree
	'leafs'		=> array("id" => array(18,19,20,21,22,29,34))
);

/**
 * Definisce le variabili utilizzate per la gestione delle sessioni
 */
$config['session'] = array (
	"sessionUserKey"	=> "BEAuthUser", 		// Nome con cui salvato in sessione info. utente connesso
) ;


/**
 * Permessi di default
 */
$config['permissions'] = array(
	'all'	=> array(
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
		array('guest', 			'group', (BEDITA_PERMS_READ)),
	),
	$config['objectTypes']['section']['id']	=> array(
		array('guest', 			'group', (BEDITA_PERMS_READ)),
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
	),
	$config['objectTypes']['area']['id']	=> array(
		array('guest', 			'group', (BEDITA_PERMS_READ)),
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
	),
	$config['objectTypes']['document']['id']	=> array(
		array('guest', 			'group', (BEDITA_PERMS_READ)),
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
	),
	$config['objectTypes']['image']['id']	=> array(
		array('guest', 			'group', (BEDITA_PERMS_READ)),
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
	),
	$config['objectTypes']['audio']['id']	=> array(
		array('guest', 			'group', (BEDITA_PERMS_READ)),
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
	),
	$config['objectTypes']['video']['id']	=> array(
		array('guest', 			'group', (BEDITA_PERMS_READ)),
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
	),
	$config['objectTypes']['befile']['id']	=> array(
		array('guest', 			'group', (BEDITA_PERMS_READ)),
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
	),
	$config['objectTypes']['gallery']['id']	=> array(
		array('guest', 			'group', (BEDITA_PERMS_READ)),
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
	),
	
);


/**
 * Tipologia di default, domande
 */
$config['questionTypeDefault'] = BEDITA_DOMANDA_TXTSEMPLICE ;

/**
 * 
 * Options selezione status
 * 
 */
$config['statusOptions'] = array(
	"on"	=> "ON",
	"off"	=> "OFF",
	"draft"	=> "DRAFT"
) ;

/**
 * Lang selection options ISO-639-3 - Language options for contents
 */
$config['langOptions'] = array(
	"ita"	=> "italiano",
	"eng"	=> "english",
	"spa"	=> "espa&ntilde;ol",
	"por"	=> "portugu&ecirc;s",
	"fra"	=> "fran&ccedil;ais",
	"deu"	=> "deutsch"
) ;

// ISO-639-3 codes - User interface language options (backend)
$config['langsSystem'] = array(
	"ita"	=> "italiano",
	"eng"	=> "english",
	"por"	=> "portugu&ecirc;s",
	"deu"	=> "deutsch"
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

/**
 * 
 * Options selezione tipologia Cutom properties
 * 
 */
$config['customPropTypeOptions'] = array(
	"integer"	=> "integer",
	"bool"		=> "boolean",
	"float"		=> "float",
	"string"	=> "string",
	"stream"	=> "stream"
) ;

/**
 * Options radio button tipi di documenti da seelzionare
 */
$config['docTypeOptions'] = array(
	"22"	=> "Documento",
	"23"	=> "Link oggetto BEdita",
	"24"	=> "Link",
) ;


/**
 * Variabili utilizzate da Smarty per definire permessi
 * 
 */
/**
 * Costanti per la definizione dei permessi
 */
$config['BEDITA_PERMS_READ'] 	= BEDITA_PERMS_READ ;
$config['BEDITA_PERMS_MODIFY'] 	= BEDITA_PERMS_MODIFY ;
$config['BEDITA_PERMS_DELETE'] 	= BEDITA_PERMS_DELETE ;
$config['BEDITA_PERMS_CREATE'] 	= BEDITA_PERMS_CREATE ;
$config['BEDITA_PERMS_READ_MODIFY'] = BEDITA_PERMS_READ_MODIFY;

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
	 * ELenco dei MIME accettati per i diversi tipi di oggetti
	 */
	'mime'	=> array(
				'Image'			=> array('/image\/jpeg/', '/image\/gif/', '/image\/png/'),
				'Audio'			=> array('/audio\/\.*/'),
				'Video'			=> array('/video\/\.+/'),
				'BEFile'		=> array('/application\/\.*/', '/text\/\.*/')
				),
	/**
	 * Possono essere usati i file che accompagnano l'associazione oppure indicare fiel diversi
	 * cambiando il path
	 */
	'mime.types'	=> dirname(__FILE__) . DS . "mime.types" ,
	'magic'			=> dirname(__FILE__) . DS . "magic" ,
) ;

/**
 *  For insert video from external media provider
 */
$config['media_providers'] = array(
	"youtube"	=> array(
			'/^http:\/\/\w{3}\.youtube\.com\/watch\?v=(.[^&]+)/',
			'/http:\/\/youtube\.com\/watch\?v=(.[^&]+)/',
			'/http:\/\/[a-z]{2}\.youtube\.com\/watch\?v=(.[^&]+)/'

	) ,
	"blip"	=> array(
			'/^http:\/\/\w{3}\.blip\.tv\/file\/(\d+)\?{0,1}.*/',
			'/http:\/\/blip\.tv\/file\/(\d+)\?{0,1}.*/'
	) 
) ;

/**
 *  file conf for media provider
 */
$config['media_providers_default_conf']  = array(
	"youtube"	=> "mediaprovider.youtube.ini",
	"blip"		=> "mediaprovider.blip.ini"
) ;

/**
 *  Default model bindings for Containable Behaviour 
 */
$config['modelBindings'] = array() ;


/**
 *  Default model bindings for Containable Behaviour 
 */
$config['searchFields'] = array() ;

?>