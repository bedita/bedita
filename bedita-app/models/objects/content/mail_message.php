<?php
class MailMessage extends BeditaContentModel
{
	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject', 'Content'),
			'DeleteObject' 			=> 'objects',
	); 
	
	var $hasOne= array(
			'BEObject' => array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' => array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				)
		);
		
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"RelatedObject"
															)
									),
				"default" => array("BEObject" => array("ObjectType", "RelatedObject" )),

				"minimum" => array("BEObject" => array("ObjectType"))
	);
}
?>