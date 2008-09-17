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
 * @author 		giangi giangi@qwerg.com, ste ste@channelweb.it
 * 		
*/
class Event extends BeditaContentModel
{
    var $useTable = 'contents';

	protected $modelBindings = array( 
				"detailed" =>  array(
								"BEObject" => array("ObjectType", 
													"UserCreated", 
													"UserModified", 
													"Permissions",
													"CustomProperties",
													"LangText",
													"RelatedObject",
													"Category"
													),
									"DateItem"),
				"default" 	=> array("BEObject" => array("CustomProperties", "LangText", 
								"ObjectType", "Category", "RelatedObject"),
								"DateItem"),
				"minimum" => array("BEObject" => array("ObjectType"))
	);
    
	var $actsAs 	= array(
			'CompactResult' 		=> array('DateItem'),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject'),
			'DeleteObject' 			=> 'objects',
	); 	 

	var $hasMany = array(
			'DateItem' =>
				array(
					'className'		=> 'DateItem',
					'foreignKey'	=> 'object_id',
					'dependent'		=> true
				)
		) ;

	function afterSave() {
		return $this->updateHasManyAssoc();
	}

}
?>
