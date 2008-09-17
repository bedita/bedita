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
 * 						
*/
class ObjectUser extends BEAppObjectModel
{
	var $validate = array(
		'user_id'	=> array(array('rule' => VALID_NOT_EMPTY, 		'required' => true)),
	) ;

	var $belongsTo = array(
			'User' =>
				array(
					'className'		=> 'User',
					'conditions'   => '',
					'foreignKey'	=> 'user_id',
				),
		) ;			
}
?>
