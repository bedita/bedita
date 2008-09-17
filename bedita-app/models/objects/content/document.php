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
	var $useTable = 'contents';

	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"CustomProperties",
															"LangText",
															"RelatedObject",
															"Category"
															),
									"GeoTag"),
				"default" => array("BEObject" => array("CustomProperties", 
									"LangText", "ObjectType", 
									"Category", "RelatedObject" ), "GeoTag"),

				"minimum" => array("BEObject" => array("ObjectType"))		
	);
	
	var $actsAs 	= array(
			'CompactResult' 		=> array('GeoTag'),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject'),
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
