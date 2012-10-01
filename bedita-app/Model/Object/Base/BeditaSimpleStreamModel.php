<?php

App::uses('BEAppObjectModel', 'Model/Object/Base');

/**
 * Base model for simple stream objects.
 */
class BeditaSimpleStreamModel extends BEAppObjectModel {

	public $searchFields = array("title" => 10 , "description" => 6,
		"subject" => 4, "abstract" => 4, "body" => 4, "name" => 6);

	protected $modelBindings = array(
				"detailed" => array("BEObject" => array("ObjectType",
														"Permission",
														"UserCreated",
														"UserModified",
														"RelatedObject",
														"Annotation",
														"Category",
														"LangText",
														"ObjectProperty",
														"Alias",
														"Version" => array("User.realname", "User.userid")
													),
									"Content"),
				"default" => array("BEObject" => array(	"ObjectProperty",
														"LangText",
														"ObjectType",
														"Annotation",
														"Category"),
									"Content"),
				"minimum" => array("BEObject" => array("ObjectType","Category"), "Content"),

				"frontend" => array("BEObject" => array("LangText"), "Content")
	);

	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave'		=> array(),
			'RevisionObject',
			'ForeignDependenceSave' => array('BEObject', 'Content'),
			'DeleteObject' 			=> 'objects',
			'Notify'
	);

	var $hasOne= array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' =>
				array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
	);

    function beforeValidate() {
        return $this->validateContent();
    }

}
?>