<?php 
/**
 * Areas, sections test cases...
 * 
 * @author giangi@qwerg.com ste@channelweb.it
 * 
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class ObjectCategoryTestCase extends BeditaTestCase {

 	var $uses		= array('BEObject','ObjectCategory') ;
    var $dataSource	= 'default' ;	

    /////////////////////////////////////////////////
    //      TEST METHODS
    /////////////////////////////////////////////////
 	
 	function testTags() {

		// show orphans
 		$result = $this->ObjectCategory->getTags(true) ;
		pr("Tags with orphans:");
 		pr($result);
 		
 		$result = $this->ObjectCategory->getTags(false) ;
		pr("Tags without orphans:");
 		pr($result);

 	 	$result = $this->ObjectCategory->getTags(true, 'on') ;
		pr("Tags with status: on");
 		pr($result);
 		
 	 	$result = $this->ObjectCategory->getTags(true, array('on', 'off', 'draft')) ;
		pr("Tags with status: on/off/draft");
 		pr($result);
 	
 	 	$result = $this->ObjectCategory->getTags(true, null, true) ;
		pr("Tags with cloud: ");
 		pr($result);
 	
 	} 

 	
    /////////////////////////////////////////////////
	//     END TEST METHODS
	/////////////////////////////////////////////////

	public   function __construct () {
		parent::__construct('ObjectCategory', dirname(__FILE__)) ;
	}	
}

?> 