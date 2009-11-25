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

class SectionTestCase extends BeditaTestCase {

 	var $uses		= array('Section', 'Tree', 'Area') ;
    var $dataSource	= 'default' ;	

	function testActsAs() {
 		$this->checkDuplicateBehavior($this->Section);
 	}
 	
 	function testFeeds() {

 		$conf = Configure::getInstance();
		$tree = $this->Tree->getAll(null, null, null, array($conf->objectTypes['area']['id'])) ;
 		
		foreach ($tree as $area) {
			pr("Publication: ". $area['id'] . " - ". $area['title']);
			$result = $this->Section->feedsAvailable($area['id']);
			pr("Available feeds:");
	 		pr($result);
		}
		
 	}
 	
 	function testMinInsert() {
 		$this->setDefaultDataSource('test') ;
 		echo '<h2>Using database: <b>'. ConnectionManager::getDataSource('test')->config['database'] .'</b></h2>';

		$this->requiredData(array("tree"));
		$result = $this->Area->save($this->data['tree']['area']) ;
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Area->validationErrors);
			return ;
		}
		$area_id = $this->Area->id;
		$resultArea = $this->Area->findById($area_id);
		pr("<h4>Area created:</h4>");
		pr($resultArea);
		
		$this->data['tree']['section']['parent_id'] = $this->Area->id;
		$result = $this->Section->save($this->data['tree']['section']);
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Section->validationErrors);
			return ;
		}
		$section_id = $this->Section->id;
		$resultSection = $this->Section->findById($section_id);
		pr("<h4>Section created:</h4>");
		pr($resultSection);
		
		$this->data['tree']['subsection']['parent_id'] = $section_id;
		$this->Section->create();
		$result = $this->Section->save($this->data['tree']['subsection']);
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Section->validationErrors);
			return ;
		}
		$subsection_id = $this->Section->id;
		$resultSubsection = $this->Section->findById($subsection_id);
		pr("<h4>Subsection created:</h4>");
		pr($resultSubsection);
		
		echo "<hr/>";
		pr("<h4>Publishing tree path:</h4>");
		$result = $this->Tree->findById($area_id);
		$this->assertEqual($result["Tree"]["path"], '/'.$area_id);
		pr($result["Tree"]["path"]);
		
		pr("<h4>Section tree path:</h4>");
		$result = $this->Tree->findById($section_id);
		$this->assertEqual($result["Tree"]["path"], '/'.$area_id . '/' .$section_id);
		pr($result["Tree"]["path"]);
		
		pr("<h4>Subsection tree path:</h4>");
		$result = $this->Tree->findById($subsection_id);
		$this->assertEqual($result["Tree"]["path"], '/'.$area_id . '/' .$section_id . '/' . $subsection_id);
		pr($result["Tree"]["path"]);
		
		echo "<hr/>";
		
		// remove subsection
		$result = $this->Section->delete($subsection_id);
		$this->assertEqual($result,true);		
		pr("Subsection removed");
		
		//remove section
		$result = $this->Section->delete($section_id);
		$this->assertEqual($result,true);		
		pr("Section removed");
		
		// remove publication
		$result = $this->Area->delete($this->Area->{$this->Area->primaryKey});
		$this->assertEqual($result,true);		
		pr("Area removed");
	} 
 	
	public   function __construct () {
		parent::__construct('Section', dirname(__FILE__)) ;
	}	
}

?> 