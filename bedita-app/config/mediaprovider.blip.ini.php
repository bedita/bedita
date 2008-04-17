<?php
/**
  * @author giangi@qwerg.com ste@channelweb.it
 * 
 *  constants and global variables for media provider
 * 
 */
$config = array() ;
 
$config['blip'] = array(
	"width"		=> 320,
	"height"	=> 200,

	"urlinfo" 	=> "http://www.blip.tv/file/%s?skin=json",
	//"urlembed" 	=> "http://www.blip.tv/file/%s?skin=rss",
	"urlembed" 	=> "http://blip.tv/syndication/copypaste/?item_type=file&id=%s&skin=json&callback=DoSomethingActions.legacySelector.gotEmbedCode",
	"urlembed"	=> "http://www.blip.tv/players/embed/?posts_id=%s&players_id=-1&skin=json&callback=DoSomethingActions.playerSelector.gotEmbedCode"
) ;

?>