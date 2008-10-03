<?php
class MailTemplate extends BeditaContentModel
{
	var $useTable = 'mail_messages';

	public $searchFields = array();
	
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
															"Permissions"),
									 "Content"
									),
				"default" => array("BEObject" => array("ObjectType"), "Content"),

				"minimum" => array("BEObject" => array("ObjectType"))
	);
	
}
?>