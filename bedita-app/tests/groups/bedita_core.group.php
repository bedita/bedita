<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * Bedita core group test
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeditaCoreGroupTest extends GroupTest {

	var $label = 'Bedita core tests';

	protected function  addTest($t) {
		TestManager::addTestFile($this, APP_TEST_CASES . DS . $t);
	}
	
	function BeditaCoreGroupTest() {
		$this->addTest('controllers' . DS . 'authentication_controller');
		$this->addTest('components' . DS . 'be_auth');
		$this->addTest('components' . DS . 'be_system');
		$this->addTest('components' . DS . 'permission_module');
		$this->addTest('datasources' . DS . 'schema');
		$this->addTest('models' . DS . 'category');
		$this->addTest('models' . DS . 'section');
		$this->addTest('models' . DS . 'document');
		$this->addTest('models' . DS . 'area');
	}
}
?>