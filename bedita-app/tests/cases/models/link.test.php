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

class LinkTestCase extends BeditaTestCase {
	
	var $uses = array('Link') ;
		
	function testLink() {
		$this->requiredData(array("link1","linksame","linkother"));

		$link = ClassRegistry::init("Link");
		$result = $link->save($this->data["link1"]);
		$this->assertEqual($result,true);
		return false;
		$id1 = $link->id;
		$result = $this->Link->findById($id1);
		pr("Link created:");
		pr($result);

// same id on same title-url [??] -- no exception
		$link->create();
		$result = $link->save($this->data["linksame"]);
		$this->assertEqual($result,true);
		$idsame = $link->id;
		$this->assertEqual($idsame,$id1);
// exception on same title-url

		try {
			$result = $link->save($this->data["linksame"]);
			$this->fail("BeditaExcepion expected");
		} catch(BeditaException $e) {
			$this->pass("BeditaExcepion raised");
		}
	
		$link->create();
		$result = $link->save($this->data["linkother"]);
		$this->assertEqual($result,true);		
		$id2 = $link->id;
		
		$result = $this->Link->delete($id1);
		$this->assertEqual($result,true);		
		$result = $this->Link->delete($id2);
		$this->assertEqual($result,true);		
		pr("Links removed");		
	}
	
 	public   function __construct () {
		parent::__construct('Link', dirname(__FILE__)) ;
	}	
}
?>