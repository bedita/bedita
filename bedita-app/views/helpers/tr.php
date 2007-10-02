<?
class TrHelper extends Helper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html');
			
	function t($s, $return = false) {
		return __($s, $return);
	}
	
	/**
	* Normal translation using i18n in cake php
	*/
	function translate($s, $return = false) {
		return __($s, $return);
	}

	/**
	* translate html->link url...
	*/
	function link($s, $u) {
		$tr = __($s, true);
		return $this->Html->link($tr, $u);
	}
	
	/**
	* Normal translation using i18n in cake php
	*/
	function translatePlural($s, $plural, $count, $return = false) {
		return __($s, $plural, $count, $return);
	}
}
?>