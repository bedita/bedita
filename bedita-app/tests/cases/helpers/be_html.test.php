<?php 
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

App::import('Helper', 'BeHtml');

class BeHtmlTestCase extends BeditaTestCase {
    var $dataSource = 'test';

	////////////////////////////////////////////////////////////////////

    function testIta() {
    	$this->requiredData(array("it"));
    	echo '<h3>Source</h3>';
    	$source = $this->data['it']['source'];
    	echo htmlentities($source);
    	echo '<h3>Expected</h3>';
    	$test = htmlentities($this->data['it']['test']);
    	$test = str_replace('&bull;', '&shy;', $test);
        $test = html_entity_decode($test);
    	echo $test;
    	echo '<h3>Result</h3>';
    	$result = $this->BeHtmlHelper->hyphen($source, 'it');
    	echo $result;
        $this->assertTrue($result == $test);
    }

	public   function __construct () {
		parent::__construct('BeHtml', dirname(__FILE__));
		$this->BeHtmlHelper = new BeHtmlHelper();
	}
}
