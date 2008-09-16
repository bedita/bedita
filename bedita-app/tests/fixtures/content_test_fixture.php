<?php 

class ContentTestFixture extends CakeTestFixture {
	
    var $name = 'ContentTest';
    
    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'object_type_id' => array('type' => 'integer'),
        'title' => array('type' => 'string', 'length' => 255, 'null' => false)
    ); 
    
    var $records = array(
        array (	'id' => 1, 
				'object_type_id' => 2, 
				'title' => 'First Object')
    ); 
    
}

?> 