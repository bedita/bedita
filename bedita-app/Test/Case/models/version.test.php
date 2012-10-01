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

class VersionTestCase extends BeditaTestCase {

	var $uses = array('Version', 'Document') ;
	
	var $dataSource	= 'test' ;	
 	var $components	= array('Transaction') ;
 	
 	/////////////////////////////////////////////////
    //      TEST METHODS
    /////////////////////////////////////////////////
 	function testVersion() {
		
 		$this->requiredData(array("docs"));
 		
 		$this->Document->create();
 		$revisions = array();
 		foreach ($this->data["docs"] as $doc) {
 			if(!empty($this->Document->id)) {
 				$doc["id"] = $this->Document->id;
 			}
 			$this->Document->save($doc);
 			$revisions[] = $this->Document->findById($this->Document->id);
 		}
 		$id = $this->Document->id;
 		$nRev = $this->Version->numRevisions($id);
		$this->assertEqual($nRev, count($this->data["docs"])-1, "Wrong revision number");
		$currData = $revisions[count($revisions)-1];
		
		for ($r = $nRev; $r > 0; $r--) {
			$d = $this->Version->revisionData($id, $r, $this->Document);
			pr("Revision: $r");
			pr($d);
			$td = $this->data["docs"][$r-1];
			pr("Inserted:");
			pr($td);
			foreach ($td as $k => $v) {
				$this->assertNotNull($d[$k], "field '$k'' not found in revision $r");
				if(!empty($d[$k])) {
					$this->assertEqual($v, $d[$k], "Wrong field '$k'' - expected '$v', found '".$d[$k]."'");
				}
			}
		}

		$this->Document->delete($id);
		
 	}
 	
	public   function __construct () {
		parent::__construct('Version', dirname(__FILE__)) ;
	}
		
}
 
?>