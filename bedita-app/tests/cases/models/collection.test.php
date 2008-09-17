<?php 

class CollectionTest extends Collection {
    var $name = 'CollectionTest';
    var $useDbConfig = 'test_suite';
}

class CollectionTestCase extends CakeTestCase {
    var $fixtures = array( 'collection_test' );
 
	function testUno() {
        
        $this->CollectionTest =& new CollectionTest();
        $result = $this->CollectionTest->findAll();pr($result);
        $expected = array(
        				array (	'CollectionTest' => array(	'id' 				=> 1, 
															'object_type_id' 	=> 2, 
															'title' 			=> 'First Object') )
    				); 
        $this->assertEqual($result,$expected);
        
	} 
	
	function testDue() {
		$this->CollectionTest =& new CollectionTest();
        $this->assertNotNull(2);
	}  
}
?> 