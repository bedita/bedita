<?php
/**
  * @author giangi@qwerg.com ste@channelweb.it
 * 
 *  constants and global variables for media provider
 * 
 */
$config = array() ;
 
$config['youtube'] = array(
	"width"		=> 320,
	"height"	=> 200,
	
	"urlthumb" => "http://i.ytimg.com/vi/%s/default.jpg",
	"embedTag" => "<embed src='http://www.youtube.com/v/%s%s' type='application/x-shockwave-flash' wmode='transparent' width='%d' height='%d'></embed>",

	/**
 	* comment, assume default value
 	*/
	// "rel"		=> 0,		// 0/1 altri video relativi a quello corrente (quando si seleziona menu). Default: 0
	// "loop"	=> 0, 		// 0/1, default: 0
	// "autoplay"	=> 0,		// 0/1. Default: 0
	// "fmt"		=> 18,		// Migliora la qualit�. Default: empty
) ;

?>