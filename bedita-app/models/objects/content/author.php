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
class Author extends BEAppObjectModel
{
	var $name 		= 'Author';
	var $recursive 	= 2 ;
	var $actsAs 	= array(
			'CompactResult' 		=> array('Image'),
			'CreateIndexFields'		=> array(),
			'ForeignDependenceSave' => array('Object', 'ContentBase'),
			'DeleteObject' 			=> 'objects',
	); 

	var $hasOne= array(
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
		'Image' =>
			array(
				'className'		=> 'ViewImage',
				'foreignKey'	=> 'image_id',
				'conditions'	=> ''
			),
	) ;

	function __construct() {
		parent::__construct() ;

	}


}
?>
