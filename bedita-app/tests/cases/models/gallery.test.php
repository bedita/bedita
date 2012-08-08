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

class GalleryTestCase extends BeditaTestCase {

	var $uses = array('Gallery') ;
	
	function testActsAs() {
 		$this->checkDuplicateBehavior($this->Gallery);
 	}
 	
	function testInsertGallery() {
		$this->requiredData(array("insert"));
		$result = $this->Gallery->save($this->data['insert']) ;
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Gallery->validationErrors);
			return ;
		}
		
		$result = $this->Gallery->findById($this->Gallery->id);
		pr("Gallery created:");
		pr($result);

		$result = $this->Gallery->delete($this->Gallery->id);
		$this->assertEqual($result,true);		
		pr("Gallery removed");
	}

	function testInsertImagesForGallery() {
		// TODO: add multimedia items...
		pr("To be done....");
	}

	public   function __construct () {
		parent::__construct('Gallery', dirname(__FILE__)) ;
	}	
}
?>