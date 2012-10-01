<?php

App::uses('BEAppObjectModel', 'Model/Object/Base');

////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Bedita simple object
**/

class BeditaSimpleObjectModel extends BEAppObjectModel {

	public $searchFields = array("title" => 10 , "description" => 6);
	public $useTable = 'objects';

	public $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave',
			'Notify'
	);

	public $hasOne= array();
}

?>