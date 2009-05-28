<?php
/**
 *
 * Create thumbnail and embed code for Video From external media provider
 *
 * @package
 * @subpackage
 * @author  giangi@qwerg.com
 */
class MediaProviderHelper extends AppHelper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html','Youtube','Blip','Vimeo');
	
	var $conf = null ;

	function __construct() {
		$this->conf 	= Configure::getInstance() ;
	}
	
	/**
	 * get img tag for thumbnail
	 */
	function thumbnail(&$obj, $htmlAttributes = array(), $URLonly=false ) {
		if (!empty($obj["thumbnail"]) && preg_match(Configure::read("validate_resorce.URL"), $obj["thumbnail"]))
			return (!$URLonly)? $this->Html->image($obj["thumbnail"], $htmlAttributes) : $obj["thumbnail"];
		
		if (!$helperName = $this->getHelperName($obj))
			return "";
		
		return $this->{$helperName}->thumbnail($obj, $htmlAttributes, $URLonly);
	}
	
	/**
	 * get embed video
	 */
	function embed(&$obj, $attributes = array() ) {
		if (!$helperName = $this->getHelperName($obj))
			return "";
			
		return $this->{$helperName}->embed($obj, $attributes) ;
	}
	
	/**
	 * get source url
	 */
	function sourceEmbed(&$obj ) {
		if (!$helperName = $this->getHelperName($obj))
			return "";
		
		return $this->{$helperName}->sourceEmbed($obj) ;
	}
	
	private function getHelperName(&$obj) {
		if(!isset($obj['provider'])) 
			return false ;
		$helperName = Inflector::camelize($obj['provider']);
		if (!isset($this->{$helperName})) {
			return false;
		}
		return $helperName;
	}
}

?>