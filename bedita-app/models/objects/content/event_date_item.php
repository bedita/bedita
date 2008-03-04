<?php
/**
 *
 * PHP versions 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
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
 * @author 		giangi giangi@qwerg.com	
 * 		
 * 				Esprime il calendario di un singolo evento 		
*/
class EventDateItem extends BEAppModel 
{
	var $useTable = 'event_date_items' ;
	var $recursive = 0 ;
	
	public function beforeValidate() {
		if(isset($this->data[$this->name])) 
			$data = $this->data[$this->name] ;
		else 
			$data = $this->data ;
		$data['start'] = isset($data['start']) ? $this->getDefaultDateFormat($data['start']) : null;
		$data['end'] = isset($data['end']) ? $this->getDefaultDateFormat($data['end']) : null;
	}
}
?>
