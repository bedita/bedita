<?
/**
 * Short description for file.
 *
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *							1785 E. Sahara Avenue, Suite 490-204
 *							Las Vegas, Nevada 89104
 *
 *  Licensed under The MIT License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @author giangi@qwerg.com
 * 
 *  bedita.ini.php - constants and global variables for BEdita application
 * 
 */
$config = array() ;
 
/**
 * Costanti per la definizione dei permessi
 */
define("BEDITA_PERMS_READ",		0x1) ;
define("BEDITA_PERMS_MODIFY",	0x2) ;
define("BEDITA_PERMS_DELETE",	0x4) ;
define("BEDITA_PERMS_CREATE",	0x8) ;

/**
 * Costanti per la definizione delel tipologie
 * di domande
 */
define("BEDITA_DOMANDA_MULTIPLA",		0x1) ;
define("BEDITA_DOMANDA_SINGOLA",		0x2) ;
define("BEDITA_DOMANDA_TXTLIBERO",		0x3) ;
define("BEDITA_DOMANDA_CHECKOPEN",		0x4) ;
define("BEDITA_DOMANDA_GRADO",			0x5) ;
define("BEDITA_DOMANDA_TXTSEMPLICE",	0x6) ;

/**
 * Codice lingua di default
 */
$config['lang'] = "it" ;

/**
 * Formato di default per i testi dei diversi tipi di contenuto.
 * Valori possibili:
 * 'html', 'txt', 'txtParsed'
 */
$config['formato'] = "txt" ;

/**
 * Status di default per gli oggetti da creare
 */
$config['status'] = "draft" ;

/**
 * Formato visualizzazione data
 */
$config['date_format'] = "%d-%m-%Y" ;



/**
 * directory temporanea
 */
$config['tmp'] = "/tmp" ;

/**
 * definisce i tipi i oggetti ammessi
 */
$config['objectTypes'] = array(
	'area'				=> 1,
	'newsletter'		=> 2,
	'section'			=> 3,

	'questionnaire'		=> 4,
	'faq'				=> 5,
	'gallery'			=> 29,
	'cartigli'			=> 6,

	'scroll'			=> 7,
	'timeline'			=> 8,
	'community'			=> 9,

	'file'				=> 10,
	'audiovideo'		=> 11,
	'image'				=> 12,
	
	'comment'			=> 13,
	'faqquestion'		=> 14,
	'question'			=> 15,

	'answer'			=> 16,
	'objectuser'		=> 17,
	'shortnews'			=> 18,

	'bibliography'		=> 19,
	'book'				=> 20,
	'event'				=> 21,

	'document'			=> 22,
	'documentPTRobject'	=> 23,
	'documentPTRextern'	=> 24,

	'documentPTRfile'	=> 25,
	'documentPTRservice'=> 26,
	'documentRule'		=> 27,
	
	'author'			=> 28,
	'biblioitem'		=> 30
) ;

/**
 * Permessi di default
 */
$config['permissions'] = array(
	'all'	=> array(
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
		array('guest', 			'group', (BEDITA_PERMS_READ)),
	),
	$config['objectTypes']['area']	=> array(
		array('guest', 			'group', (BEDITA_PERMS_READ)),
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
	),
	$config['objectTypes']['document']	=> array(
		array('guest', 			'group', (BEDITA_PERMS_READ)),
		array('administrator', 	'group', (BEDITA_PERMS_READ|BEDITA_PERMS_CREATE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_DELETE)),
	),
);

/**
 * Tipologia di default, domande
 * 
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
	"draft"	=> "DRAFT",
) ;

/**
 * 
 * Options selezione lingua
 * 
 */
$config['langOptions'] = array(
	"it"	=> "italiano",
	"en"	=> "inglese",
	"fr"	=> "francese",
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


?>