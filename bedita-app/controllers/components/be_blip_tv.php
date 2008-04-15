<?
/**
 * 
 * 
 * @filesource
 * @copyright		Copyright (c) 2006
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 */

class BeBlipTvComponent extends Object {
	var $controller	;
	var $info = null ;
	
	function __construct() {
		parent::__construct() ;
	} 

	/**
	 */
	function startup(&$controller)
	{
		$conf = Configure::getInstance() ;		
		
		$this->controller 	= $controller;
	}
	
	
	/**
	 * Torna i dati di uno specifico video
	 *
	 * @return unknown
	 */
	public function getInfoVideo($id) {
		$conf = Configure::getInstance() ;
		$this->info = null ;
		
		Configure::load($conf->media_providers_default_conf['blip']) ;
		if(!isset($conf->blip)) return false ;
		
		// Preleva le informazioni
		$fp = fopen(sprintf($conf->blip['urlinfo'], $id), "r") ;
		$json = fread($fp, 16*1024) ;
		@fclose($fp) ;
		if(!$json) return false ;
		
		/**
		 *  formatta la stringa
		**/
		// elimina la aprte iniziale e finale che non fa parte della stringa
		$json = preg_replace(array("/^\s*blip_ws_results\s*\(\s*\[\s*/m", "/\s*\]\s*\)\s*;\s*$/mi"), "", $json) ;
	
		// Eliina i commenti, danno errore
		$json = preg_replace('/(\/\*[\s\S]*?\*\/?[\r]?[\n]?[\r\n])/m', '', $json) ;
		
		// cambia gli apici, non gli paicciono...
		$json = preg_replace("/\'/mi", "\"", $json) ;

		// Preleva l'array associativo
		$ret = json_decode($json, true) ;
		if(!($ret = json_decode($json, true))) return false ;
		
		$this->info = $ret['Post'] ;
		
		return $this->info  ;
	}
	
}

/**
 * ESEMPIO:
 * 
 Array
(
    [title] => comedy central tonight
    [description] => 8-10pm
 LIVE

    [tags] => Array
        (
        )

    [datestamp] => 04-13-08 06:06pm
    [postsId] => 830661
    [postsGuid] => EFA4EC3C-09A5-11DD-803A-B9EA042700A9
    [itemType] => file
    [itemId] => 824273
    [url] => http://blip.tv/file/824273
    [mediaUrl] => http://blip.tv/file/get/Rblog-comedyCentralTonight463.flv?source=2
    [thumbnailUrl] => http://panther2.video.blip.tv/Rblog-comedyCentralTonight935.jpg
    [thumbnail120Url] => http://panther2.video.blip.tv/Rblog-comedyCentralTonight935-417.jpg
    [login] => rblog
    [userId] => 74359
    [showName] => r blog
    [blogUrl] => http://www.rosie.com
    [media] => Array
        (
            [url] => http://blip.tv/file/get/Rblog-comedyCentralTonight463.flv?source=2
            [mimeType] => video/x-flv,video/flv
            [duration] => 68
            [width] => 320
            [height] => 240
        )

    [contentRating] => TV-UN
    [advertising] => 
    [Title] => comedy central tonight
    [posts_id] => 830661
    [item_type] => file
    [item_id] => 824273
    [posts_url] => http://blip.tv/file/824273
    [media_src] => /file/get/Rblog-comedyCentralTonight463.flv?source=2
    [Thumbnail] => Array
        (
            [Url] => http://panther2.video.blip.tv/Rblog-comedyCentralTonight935.jpg
        )

    [User] => rblog
)
* 
 */

?>