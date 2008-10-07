<?php
/* SVN FILE: $Id: basket_fixture.php 7690 2008-10-02 04:56:53Z nate $ */
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
 * @subpackage		cake.tests.fixtures
 * @since			CakePHP(tm) v 1.2.0.4667
 * @version			$Revision: 7690 $
 * @modifiedby		$LastChangedBy: nate $
 * @lastmodified	$Date: 2008-10-02 00:56:53 -0400 (Thu, 02 Oct 2008) $
 * @license			http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
/**
 * Short description for class.
 *
 * @package		cake.tests
 * @subpackage	cake.tests.fixtures
 */
class BasketFixture extends CakeTestFixture {
/**
 * name property
 * 
 * @var string 'Basket'
 * @access public
 */
	var $name = 'Basket';
/**
 * fields property
 * 
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'type' => array('type' => 'string', 'length' => 255),
		'name' => array('type' => 'string', 'length' => 255),
		'object_id' => array('type' => 'integer'),
		'user_id' => array('type' => 'integer'),
	);
/**
 * records property
 * 
 * @var array
 * @access public
 */
	var $records = array(
		array('id' => 1, 'type' => 'nonfile', 'name' => 'basket1', 'object_id' => 1, 'user_id' => 1),
		array('id' => 2, 'type' => 'file', 'name' => 'basket2', 'object_id' => 2, 'user_id' => 1),
	);
}

?>