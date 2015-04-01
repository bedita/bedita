<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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

/**
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class SoapTestCase extends BeditaTestCase {

	var $uses = array('SoapClientModel') ;
	
	var $dataSource	= 'test' ;	
 	
 	/////////////////////////////////////////////////
    //      TEST METHODS
    /////////////////////////////////////////////////
 	function testSoapCall() {
		
 		$this->requiredData(array("services"));

 		foreach ($this->data["services"] as $service) {
			pr("Test Service: " . $service);
			
			// load local wsld
			$this->data[$service]['wsdl'] = MODEL_TESTS . $this->data[$service]['wsdl'];
			
			Configure::write("soap.$service", $this->data[$service]);

			$this->SoapClientModel->setup($service);
			
 			foreach ($this->data[$service.".request"] as $method => $args) {
				pr("Test Method: " . $method);
 				$res = $this->SoapClientModel->call($method, $args);
				pr("Response:");
				pr($res);
				pr($this->SoapClientModel->debugMsg());
 			}
 		}
		
 	}
 	
	public   function __construct () {
		parent::__construct('Soap', dirname(__FILE__)) ;
	}
		
}
 
?>