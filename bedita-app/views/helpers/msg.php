<?php
/**
 * Session user msg helper
 * 
 * @package		
 * @subpackage	
 */
class MsgHelper extends SessionHelper {

	function userMsg($key) {
		if ($this->__active === true) {
			if (parent::check('Message.' . $key)) {
				$flash = parent::read('Message.' . $key);
				$out = $flash['message'];
				parent::del('Message.' . $key);
				return $out;
			}
		}
		return false;
	}
		
}
?>