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
 * 				Generic date items (for calendars) 		
*/
class DateItem extends BEAppModel 
{
	var $recursive = 0 ;

	var $validate = array(
		'start' 		=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
		'end' 		=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true))
	) ;
	
	function beforeValidate() {
		if(isset($this->data[$this->name])) 
			$data = &$this->data[$this->name] ;
		else 
			$data = &$this->data ;
		
		$data['start'] = $this->getDefaultDateFormat($data['start']);
	 	$data['end'] = $this->getDefaultDateFormat($data['end']);
		
	 	if (!empty($data['start']) && !empty($data['timeStart'])) {
	 		$data['start'] .= " " . $data['timeStart'];
	 	}
		if (!empty($data['end']) && !empty($data['timeEnd'])) {
	 		$data['end'] .= " " . $data['timeEnd'];
	 	} else if(empty($data['end'])) {
	 		$data['end'] = $data['start']  ;
	 	}
 	 	
		return true;
	}
}
?>
