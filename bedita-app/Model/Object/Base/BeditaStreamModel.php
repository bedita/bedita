<?php

App::uses('BEAppObjectModel', 'Model/Object/Base');

/**
 * Base model for stream objects.
 */
class BeditaStreamModel extends BEAppObjectModel {

	public $searchFields = array("title" => 10 , "description" => 6,
		"subject" => 4, "abstract" => 4, "body" => 4, "name" => 6);

	protected $modelBindings = array(
				"detailed" => array("BEObject" => array("ObjectType",
														"Permission",
														"UserCreated",
														"UserModified",
														"RelatedObject",
														"Category",
														"ObjectProperty",
														"LangText",
														"Annotation",
														"Alias",
														"Version" => array("User.realname", "User.userid")
														),
									"Content", "Stream"),
				"default" => array("BEObject" => array(	"ObjectProperty",
														"LangText",
														"ObjectType",
														"Category",
														"Annotation"),
									"Content", "Stream"),
				"minimum" => array("BEObject" => array("ObjectType","Category"),"Content", "Stream"),

				"frontend" => array("BEObject" => array("LangText"), "Content", "Stream")
	);


	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave'		=> array(),
			'RevisionObject',
			'ForeignDependenceSave' => array('BEObject', 'Content', 'Stream'),
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
			'Stream' =>
				array(
					'className'		=> 'Stream',
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