<?php

App::uses('BEAppObjectModel', 'Model/Object/Base');

/**
 * Bedita content model relations
**/

class BeditaContentModel extends BEAppObjectModel {

	public $searchFields = array("title" => 10 , "creator" => 6, "description" => 6,
		"subject" => 4, "abstract" => 4, "body" => 4);

	function beforeValidate() {
    	return $this->validateContent();
    }

}

?>