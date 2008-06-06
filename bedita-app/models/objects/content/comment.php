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
 * 						
*/
class Comment extends BEAppObjectModel
{
	var $recursive 	= 2 ;

	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'CreateIndexFields'		=> array(),
			'ForeignDependenceSave' => array('BEObject', 'ContentBase'),
			'DeleteObject' 			=> 'objects',
	); 

	var $transactional 	= true ;
	
//	var $validate = array(
//			'author' => array(
//				'required' => true,
//	   		),
//	   		'email' => array(
//	   			'rule' => 'email',
//				'required' => true,
//	   			'message' => 'email not valid'
//	   		)
//	   );
	   
	var $hasOne = array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'ContentBase' =>
				array(
					'className'		=> 'ContentBase',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
		) ;

		
}
?>
