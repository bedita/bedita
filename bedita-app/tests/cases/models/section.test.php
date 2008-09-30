<?php 
/**
 * Areas, sections test cases...
 * 
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class SectionTestCase extends BeditaTestCase {

 	var $uses		= array('BEObject','Section', 'Tree') ;
    var $dataSource	= 'default' ;	

    /////////////////////////////////////////////////
    //      TEST METHODS
    /////////////////////////////////////////////////
 	
 	function testFeeds() {

 		$conf = Configure::getInstance();
		$tree = $this->Tree->getAll(null, null, null, array($conf->objectTypes['area']['id'])) ;
 		
		foreach ($tree as $area) {
			pr("Publication: ". $area['id'] . " - ". $area['title']);
			$result = $this->Section->feedsAvailable($area['id']);
			pr("Available feeds:");
	 		pr($result);
		}
		
 	} 

 	
    /////////////////////////////////////////////////
	//     END TEST METHODS
	/////////////////////////////////////////////////

	public   function __construct () {
		parent::__construct('Section', dirname(__FILE__)) ;
	}	
}

?> 