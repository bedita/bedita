<?php
class MailMessage extends BeditaContentModel
{
	var $actsAs 	= array(
			'CompactResult' 		=> array("MailGroup"),
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
	
	var $hasAndBelongsToMany = array(
			'MailGroup' =>	array (
					'joinTable' => 'mail_group_messages'
				)
	);	
		
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"RelatedObject"
															),
									 "Content", "MailGroup"
									),
				"default" => array("BEObject" => array("ObjectType", "RelatedObject"), "Content"),
									
				"mailgroup" => array("MailGroup"),

				"minimum" => array("BEObject" => array("ObjectType"))
	);
}
?>