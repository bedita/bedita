<?php
/**
 * Generic link ('url', 'topic', 'google', 'coordinate')
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
 * @author 		giangi giangi@qwerg.com,	ste ste@channelweb.it
 * 		
*/
class Link extends BEAppModel {

	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'CreateIndexFields'		=> array(),
			'ForeignDependenceSave' => array('BEObject'),
			'DeleteObject' 			=> 'objects',
	); 
	
	var $hasOne= array(
			'BEObject' =>
			array(
				'className'		=> 'BEObject',
				'conditions'   => '',
				'foreignKey'	=> 'id',
				'dependent'		=> true
			)
		);
}
?>
