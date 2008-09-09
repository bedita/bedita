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
 * @author 		giangi giangi@qwerg.com	
*/
class Document extends BeditaContentModel 
{
	var $actsAs 	= array(
			'CompactResult' 		=> array('GeoTag'),
			'SearchTextSave'		=> array(),
			'ForeignDependenceSave' => array('BEObject', 'Content'),
			'DeleteObject' 			=> array(
										'objects' => array(
											"relatedObjects" => array("comment")
											)
										) 
	); 

	var $hasMany = array(
		'GeoTag' =>
			array(
				'foreignKey'	=> 'object_id',
				'dependent'		=> true
			)
	) ;

	function afterSave() {
		return $this->updateHasManyAssoc();
	}

	
}


?>
