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
};

/**
 * Class for youtube media provider
 */
class YoutubeMedia {
	var $helper = null ;
	
	var $thumbTag	= "http://i.ytimg.com/vi/%s/default.jpg" ;
	
	function __construct(&$helper) {
		$this->helper = $helper ;
	}
	
	function thumbnail(&$obj, &$htmlAttributes) {		
		return $this->helper->Html->image(sprintf($this->thumbTag, $obj['uid']), $htmlAttributes) ;
	}
}  ;
?>