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
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class CardTestCase extends BeditaTestCase {

 	var $uses		= array('Card') ;
    var $dataSource	= 'test' ;	
 	var $components	= array('Transaction') ;

 	protected $inserted = array();
 	
    /////////////////////////////////////////////////
    //      TEST METHODS
    /////////////////////////////////////////////////
 	function testActsAs() {
 		$this->checkDuplicateBehavior($this->Card);
 	}
 	
 	function testInsert() {
		$this->requiredData(array("insert"));
		$result = $this->Card->save($this->data['insert']) ;
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Card->validationErrors);
			return ;
		}
		
		$result = $this->Card->findById($this->Card->id);
		pr("Card created:");
		pr($result);
		$this->inserted[] = $this->Card->id;
		
		// validation email test
		$this->requiredData(array("insertError"));
 		$result = $this->Card->save($this->data['insertError']) ;
		$this->assertEqual($result,false);		
		if(!$result) {
			pr("Validation test error:");
			pr($this->Card->validationErrors);
		}
	} 
	
 	function testDelete() {
        pr("Removing inserted cards:");
        foreach ($this->inserted as $ins) {
        	$result = $this->Card->delete($ins);
			$this->assertEqual($result, true);		
			pr("Card deleted");
        }        
 	}
 	
    /////////////////////////////////////////////////
	//     END TEST METHODS
	/////////////////////////////////////////////////

	
	public   function __construct () {
		parent::__construct('Card', dirname(__FILE__)) ;
	}	
}

?>