<?php
/* SVN FILE: $Id: session.test.php 6311 2008-01-02 06:33:52Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package			cake.tests
 * @subpackage		cake.tests.cases.libs.controller.components
 * @since			CakePHP(tm) v 1.2.0.5436
 * @version			$Revision: 6311 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-01-02 00:33:52 -0600 (Wed, 02 Jan 2008) $
 * @license			http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
uses('controller' . DS . 'controller', 'controller' . DS . 'components' . DS .'session');

class SessionTestController extends Controller {}
/**
 * Short description for class.
 *
 * @package    cake.tests
 * @subpackage cake.tests.cases.libs.controller.components
 */
class SessionComponentTest extends CakeTestCase {

	function setUp() {
		$this->Session = new SessionComponent();
	}

	function testSessionAutoStart() {
		$this->Session->startup(new SessionTestController());
		$this->assertTrue(isset($_SESSION));
	}

	function testSessionWriting() {
		$this->assertTrue($this->Session->write('Test.key.path', 'some value'));
		$this->assertEqual($this->Session->read('Test.key.path'), 'some value');
	}

	function tearDown() {
		unset($this->Session);
	}
}

?>
