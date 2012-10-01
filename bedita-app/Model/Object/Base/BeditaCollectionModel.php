<?php

App::uses('BEAppObjectModel', 'Model/Object/Base');

////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Base model for collection objects.
 */
class BeditaCollectionModel extends BEAppObjectModel {

	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave',
			'RevisionObject',
			'ForeignDependenceSave' => array('BEObject'),
			'DeleteDependentObject'	=> array('section'),
			'DeleteObject' 			=> 'objects',
			'Notify'
	);
	var $recursive 	= 2;

	var $hasOne = array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Tree' =>
				array(
					'foreignKey'	=> 'id',
				)
	);

}

?>