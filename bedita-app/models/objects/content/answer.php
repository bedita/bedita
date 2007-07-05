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
class Answer extends BEAppObjectModel
{
	var $name 		= 'Answer';
	var $recursive 	= 2 ;
	var $actsAs 	= array(
			'CompactResult' 		=> array('Question'),
			'CreateIndexFields'		=> array(),
			'ForeignDependenceSave' => array('Object', 'ContentBase'),
			'DeleteObject' 			=> 'objects',
	); 

	var $validate = array(
		'question_id' 	=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
	) ;

	var $hasOne = array(
			'Object' =>
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

	var $belongsTo = array(
		'Question' =>
			array(
				'className'		=> 'Question',
				'foreignKey'	=> 'question_id',
				'conditions'	=> ''
			),
	) ;
		
	function __construct() {
		parent::__construct() ;

	}

}
?>
