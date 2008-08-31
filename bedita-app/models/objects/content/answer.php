<?php
/**
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
 * 						
*/
class Answer extends BeditaContentModel
{
	var $actsAs 	= array(
			'CompactResult' 		=> array('Question'),
			'ForeignDependenceSave' => array('BEObject', 'Content'),
			'DeleteObject' 			=> 'objects',
	); 

	var $validate = array(
		'question_id' 	=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
	) ;

	var $belongsTo = array(
		'Question' =>
			array(
				'className'		=> 'Question',
				'foreignKey'	=> 'question_id',
				'conditions'	=> ''
			),
	) ;
}
?>
