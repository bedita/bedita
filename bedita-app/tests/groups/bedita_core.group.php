<?php

/* SVN FILE: $Id:  $ */

/**
 * Bedita core group test.
 *
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @package			bedita.tests
 * @subpackage	bedita.tests.groups
 * @since			
 * @version			$Revision: $
 * @modifiedby		$LastChangedBy: $
 * @lastmodified	$Date: $
 * @license			
 * @author 			ste ste@channelweb.it
 */
/**
 *  BeditaCoreGroupTest
 */
class BeditaCoreGroupTest extends GroupTest {

	var $label = 'Bedita core tests';

	protected function  addTest($t) {
		TestManager::addTestFile($this, APP_TEST_CASES . DS . $t);
	}
	
	function BeditaCoreGroupTest() {
//		$this->addTest('components' . DS . 'permission_module');
		$this->addTest('components' . DS . 'be_auth');
		$this->addTest('datasources' . DS . 'schema');
	}
}
?>
?>