<?
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
	function thumbnail(&$obj, $htmlAttributes = array() ) {
		if(!isset($obj['provider'])) return "" ;
		
		$media = null ;
		switch ($obj['provider']) {
			case 'youtube': $media = new YoutubeMedia($this) ; break ;
			default: "" ;
		}
		return $media->thumbnail($obj, $htmlAttributes) ;
	}
	
	/**
	 * get embed video
	 */
	function embed(&$obj, $attributes = array() ) {
		if(!isset($obj['provider'])) return "" ;
		
		$media = null ;
		switch ($obj['provider']) {
			case 'youtube': $media = new YoutubeMedia($this) ; break ;
			default: "" ;
		}
		return $media->embed($obj, $attributes) ;
	}
	
};

/**
 * Class for youtube media provider
 */
class YoutubeMedia {
	var $helper = null ;
	
	var $thumbTag	= "http://i.ytimg.com/vi/%s/default.jpg" ;
	var $embedTag	= '
<object width="%d" height="%d">
<param name="movie" value="http://www.youtube.com/v/%s%s"></param>
<param name="wmode" value="transparent"></param>
<embed src="http://www.youtube.com/v/%s%s" type="application/x-shockwave-flash" wmode="transparent" width="%d" height="%d"></embed>
</object>	
	';
	
	function __construct(&$helper) {
		$this->helper = $helper ;
	}
	
	function thumbnail(&$obj, &$htmlAttributes) {		
		return $this->helper->Html->image(sprintf($this->thumbTag, $obj['uid']), $htmlAttributes) ;
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
		
		// Definisce il file di configurazione da caricare
		$config = $this->conf->media_providers_default_conf['youtube'] ;
		if(isset($attributes["configure"])) {
			$config = $attributes["configure"] ;
		}
		Configure::load($config) ;
		if(!isset($this->conf->youtube)) return "" ;
		
		// formatta le variabili
		$attributes = array_merge($this->conf->youtube, $attributes) ;
		$widht = $attributes['width'] ;
		$height = $attributes['height'] ;
		unset($attributes['conf']) ;
		unset($attributes['width']) ;
		unset($attributes['height']) ;
		$params = "" ;
		foreach ($attributes as $key => $value) {
			$params .= "&$key=$value" ;
		}

		return trim(sprintf($this->embedTag, $widht, $height, $obj['uid'], $params,  $obj['uid'], $params, $widht, $height)) ;
	}
	
}  ;
?>