<?php

App::uses('BEAppObjectModel', 'Model/Object/Base');

/**
 * Base class for products
 *
 */
class BeditaProductModel extends BEAppObjectModel {

	public $searchFields = array("title" => 10 , "description" => 6,
		"abstract" => 4, "body" => 4);

		protected $modelBindings = array(
				"detailed" => array("BEObject" => array("ObjectType",
														"Permission",
														"UserCreated",
														"UserModified",
														"RelatedObject",
														"ObjectProperty",
														"LangText",
														"Category",
														"Annotation",
														"Alias",
														"Version" => array("User.realname", "User.userid")
													),
									"Product"),
				"default" => array("BEObject" => array(	"ObjectProperty",
														"LangText",
														"ObjectType"),
									"Product"),
				"minimum" => array("BEObject" => array("ObjectType"),"Product")
	);


	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave'		=> array(),
			'ForeignDependenceSave' => array('BEObject', 'Product'),
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
			'Product' =>
				array(
					'className'		=> 'Product',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				)
	);
}

?>