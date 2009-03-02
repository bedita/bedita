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
	var $helpers = array('Form', 'Html');
	
	var $conf = null ;

	function __construct() {
		$this->conf 	= Configure::getInstance() ;
	}
	
	/**
	 * get img tag for thumbnail
	 */
	function thumbnail(&$obj, $htmlAttributes = array(), $URLonly=false ) {
		if(!isset($obj['provider'])) return "" ;
		
		$media = null ;
		switch ($obj['provider']) {
			case 'youtube': $media = new YoutubeMedia($this) ; break ;
			case 'blip': 	 $media = new BlipMedia($this) ; break ;
			default: "" ;
		}
		return $media->thumbnail($obj, $htmlAttributes, $URLonly) ;
	}
	
	/**
	 * get embed video
	 */
	function embed(&$obj, $attributes = array() ) {
		if(!isset($obj['provider'])) return "" ;
		
		$media = null ;
		switch ($obj['provider']) {
			case 'youtube': $media = new YoutubeMedia($this) ; break ;
			case 'blip': 	 $media = new BlipMedia($this) ; break ;
			default: "" ;
		}
		return $media->embed($obj, $attributes) ;
	}
	
	/**
	 * getsource embed url
	 */
	function sourceEmbed(&$obj ) {
		if(!isset($obj['provider'])) return "" ;
		
		$media = null ;
		switch ($obj['provider']) {
			case 'youtube': $media = new YoutubeMedia($this) ; break ;
			case 'blip': 	 $media = new BlipMedia($this) ; break ;
			default: "" ;
		}
		return $media->sourceEmbed($obj) ;
	}
};

/**
 * Class for youtube media provider
 */
class YoutubeMedia {
	var $helper = null ;
	
	function __construct(&$helper) {
		$this->helper = $helper ;
	}
	
	function thumbnail(&$obj, &$htmlAttributes, $URLonly) {
		$this->conf 	= Configure::getInstance() ;
		if (!empty($obj["thumbnail"]) && preg_match(Configure::read("validate_resorce.URL"), $obj["thumbnail"]))
			$src = $obj["thumbnail"];
		else
			$src = sprintf($this->conf->youtube["urlthumb"], $obj['uid']);
		return (!$URLonly)? $this->helper->Html->image($src, $htmlAttributes) : $src;
	}
	
	/**
	 * For change config file, set "conf" in attributes
	 *
	 * @param unknown_type $obj
	 * @param unknown_type $attributes
	 * @return unknown
	 */
	function embed(&$obj, &$attributes) {
		$this->conf 	= Configure::getInstance() ;
		if(!isset($this->conf->provider_params["youtube"])) 
			return "" ;
		
		// formatta le variabili
		$attributes = array_merge($this->conf->provider_params["youtube"], $attributes) ;
		$width = $attributes['width'] ;
		$height = $attributes['height'] ;
		$embedTag = $attributes["embedTag"];
		
		unset($attributes["embedTag"]);
		unset($attributes["urlthumb"]);
		unset($attributes['conf']) ;
		unset($attributes['width']) ;
		unset($attributes['height']) ;
		$params = "" ;
		foreach ($attributes as $key => $value) {
			$params .= "&$key=$value" ;
		}

		return trim(sprintf($embedTag, $obj['uid'], $params, $width, $height)) ;
	}
	
	/**
	 * For change config file, set "conf" in attributes
	 *
	 * @param unknown_type $obj
	 * @return unknown
	 */
	function sourceEmbed(&$obj) {
		return $obj['path'] ;
	}
}  ;

/**
 * Class for Blip.tv media provider
 */
class BlipMedia {
	var $helper = null ;
	
	function __construct(&$helper) {		
		$this->helper = $helper ;		
	}
	
	function thumbnail(&$obj, &$htmlAttributes, $URLonly) {
		if (empty($obj["thumbnail"])) {
			if(!class_exists("BeBlipTvComponent")){
				App::import('Component', "BeBlipTv");
			}
			$Component = new BeBlipTvComponent();
			$Component->getInfoVideo($obj['uid']) ;
			
			$src = sprintf($Component->info['thumbnailUrl'], $obj['uid']);
		} elseif (preg_match(Configure::read("validate_resorce.URL"), $obj["thumbnail"])) {
			$src = $obj["thumbnail"];
		}
		return (!$URLonly)? $this->helper->Html->image($src, $htmlAttributes) : $src;
	}
	
	/**
	 * For change config file, set "conf" in attributes
	 *
	 * @param unknown_type $obj
	 * @param unknown_type $attributes
	 * @return unknown
	 */
	function embed(&$obj, &$attributes) {
		$this->conf 	= Configure::getInstance() ;
		
		if(!class_exists("BeBlipTvComponent")){
			App::import('Component', "BeBlipTv");
		}
		$Component = new BeBlipTvComponent();
		$Component->getEmbedVideo($obj['uid'], $attributes) ;
	
		return $Component->embed ;
	}
	
	/**
	 * For change config file, set "conf" in attributes
	 *
	 * @param unknown_type $obj
	 * @return unknown
	 */
	function sourceEmbed(&$obj) {
		$this->conf = Configure::getInstance() ;
		
		if(!class_exists("BeBlipTvComponent")){
			App::import('Component', "BeBlipTv");
		}
		$Component = new BeBlipTvComponent();
		
		$info = $Component->getInfoVideo($obj['uid']);
	
		if(preg_match("/^http:\/\/blip.tv\/file\/get\/.*\.flv/",$info["mediaUrl"],$matched)) {
			return $matched[0] ;
		}

		return "" ;
	}
	
}  ;

?>