<?php 
/**
 * 
 * @author ste@channelweb.it
 * 
 * BeAuthComponent tests
 * 
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class BeSystemTestCase extends BeditaTestCase {
	var $components = array('BeSystem');
	var $uses = array();
    var $dataSource = 'default' ;
	
	function testInfo() {

		$res = $this->BeSystem->systemInfo();
		pr($res);
	}

	public   function __construct () {
		parent::__construct('BeSystem', dirname(__FILE__)) ;
	}
}
?> 