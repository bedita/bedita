<?
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
		return str_replace($this->Html->base, "", $this->Html->here) ;
	}
}

?>