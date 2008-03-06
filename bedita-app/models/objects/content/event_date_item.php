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
 * @author 		giangi giangi@qwerg.com	
 * 		
 * 				Esprime il calendario di un singolo evento 		
*/
class EventDateItem extends BEAppModel 
{
	var $useTable = 'event_date_items' ;
	var $recursive = 0 ;

	var $validate = array(
		'start' 		=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
		'end' 			=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true))
	) ;
	

}
?>
