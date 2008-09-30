<?php 
/**
 * Areas, sections test cases...
 * 
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class SectionTestCase extends BeditaTestCase {

 	var $uses		= array('BEObject','Section') ;
    var $dataSource	= 'default' ;	

    /////////////////////////////////////////////////
    //      TEST METHODS
    /////////////////////////////////////////////////
 	
 	function testFeeds() {

 		$result = $this->Section->feedsAvailable();
		pr("Available feeds:");
 		pr($result);
 	} 

 	
    /////////////////////////////////////////////////
	//     END TEST METHODS
	/////////////////////////////////////////////////

	public   function __construct () {
		parent::__construct('Section', dirname(__FILE__)) ;
	}	
}

?> 