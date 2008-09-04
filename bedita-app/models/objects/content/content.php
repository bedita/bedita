<?php
/**
 *
 * @filesource
 * @copyright		Copyright (c) 2007
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license
 * @author 		giangi giangi@qwerg.com, ste ste@channelweb.it	
 * 		
 * 		BEdita content, base class for all 'content types'
 */
class Content extends BEAppModel
{

	
	/**
	 * Definisce i valori di default.
	 */		
	function beforeValidate() {
		if(isset($this->data[$this->name])) $data = &$this->data[$this->name] ;
		else $data = &$this->data ;
		
	 	$default = array(
			'start' 			=> array('getDefaultDateFormat', (isset($data['start']) && !empty($data['start']))?$data['start']:time()),
			'end'	 		=> array('getDefaultDateFormat', ((isset($data['end']) && !empty($data['end']))?$data['end']:null)),
			'type' 		=> array('getDefaultTextFormat', (isset($data['type']))?$data['type']:null),
		) ;
		
		foreach ($default as $name => $rule) {
			if(!is_array($rule) || !count($rule)) {
				$data[$name] = $rule ;
				continue ;
			}
			
			$method = $rule[0];
			unset($rule[0]);
			
			if (method_exists($this, $method)) {
				$data[$name] = call_user_func_array(array(&$this, $method), $rule);
			} 
		}

		return true ;
	}
	
	
}
?>
