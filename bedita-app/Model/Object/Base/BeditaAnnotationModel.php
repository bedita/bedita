<?php

App::uses('BEAppObjectModel', 'Model/Object/Base');

/**
 * Bedita annotation model
**/

class BeditaAnnotationModel extends BEAppObjectModel {

	public $searchFields = array("title" => 10 , "description" => 6,
		"body" => 4, "author" => 3);

	var $belongsTo = array(
		"ReferenceObject" =>
			array(
				'className'		=> 'BEObject',
				'foreignKey'	=> 'object_id',
			),
	);

	var $actsAs 	= array(
			'CompactResult' 		=> array("ReferenceObject"),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject'),
			'DeleteObject' 			=> 'objects',
			'Notify'
	);

	protected $modelBindings = array(
		"detailed" =>  array("BEObject" => array(
									"ObjectType",
									"UserCreated",
									"Version" => array("User.realname", "User.userid")
								), "ReferenceObject"),
		"default" =>  array("BEObject" => array("ObjectType","UserCreated"), "ReferenceObject"),
		"minimum" => array("BEObject" => array("ObjectType"))
	);

}

?>