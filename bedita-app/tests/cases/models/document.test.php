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

class DocumentTestCase extends BeditaTestCase {

 	var $uses		= array('BEObject','Document', 'SearchText', 'Tree') ;
    var $dataSource	= 'test' ;	
 	var $components	= array('Transaction') ;

 	protected $inserted = array();
 	
    /////////////////////////////////////////////////
    //      TEST METHODS
    /////////////////////////////////////////////////
 	function testActsAs() {
 		$this->checkDuplicateBehavior($this->Document);
 	}
 	
 	function testInsert() {
		$this->requiredData(array("insert"));
		$result = $this->Document->save($this->data['insert']) ;
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Document->validationErrors);
			return ;
		}
		
		$result = $this->Document->findById($this->Document->id);
		pr("Document created:");
		pr($result);
		$this->inserted[] = $this->Document->id;
	} 
	
 	function testSearch() {

 		$searches = $this->data['searches'];
 		foreach ($searches as $s) {
	 		pr("Search string:".$s);
			$res = $this->BEObject->findObjects(null, null, null, 
				array("object_type_id" => Configure::read("objectTypes.document.id"), "query" => $s));
	 		pr($res);
 		}

 	 	// tree search
 		foreach ($this->data['searchTree'] as $treeId) {
	 		foreach ($searches as $s) {
		 		pr("Tree id: $treeId - search string:".$s);
				$res = $this->BEObject->findObjects($treeId, null, null,
					array("object_type_id" => Configure::read("objectTypes.document.id"), "query" => $s));
		 		pr($res);
	 		}
 		}
 		
 	}	
	
 	function testDelete() {
        pr("Removing inserted documents:");
        foreach ($this->inserted as $ins) {
        	$result = $this->Document->delete($ins);
			$this->assertEqual($result, true);		
			pr("Document deleted");
        }        
 	}
 	
    /////////////////////////////////////////////////
	//     END TEST METHODS
	/////////////////////////////////////////////////
	
	public   function __construct () {
		parent::__construct('Document', dirname(__FILE__)) ;
	}	
}

?> 