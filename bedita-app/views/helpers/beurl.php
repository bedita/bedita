<?php
/**
 * Beurl helper library.
 *
 * Torna url di base (html->here - html->base)
 *
 * @package		
 * @subpackage	
 */
class BeurlHelper extends Helper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html');
		
	/**
	* 
	*/
	function here() {
		$newUrl = str_replace($this->Html->base, "", $this->Html->here) ;
		if($newUrl != "/") {
			$pos = strpos($newUrl,"/");
			if(!$pos || $pos > 0) 
				$newUrl = "/" . $newUrl;
		}
		return $newUrl ;
	}
}

?>