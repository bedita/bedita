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

class TreeTestCase extends BeditaTestCase {

 	var $uses = array('Tree') ;
 	private $savedIds = array();
 	private $countStatus = array("on" => 0, "off" => 0, "draft" => 0);
 	var $dataSource	= 'test' ;
 	
 	public function testBuildTree() {
 		$this->Tree->cacheQueries = false;
 		pr("Building tree:");
 		// clean tree
 		$this->Tree->deleteAll(array("path LIKE '/%'"));
 		$this->requiredData(array("buildTree"));
 		$this->saveObject($this->data['buildTree']);
		$tree = $this->Tree->getAll() ;
		echo $this->buildHtmlTree($tree);
		$arrToCompare = $this->prepareTreeToCompare($tree);
		if (!$this->assertEqual($arrToCompare, $this->data['buildTree'])) {
			pr("Array inserted: " . $this->data['buildTree']);
			pr("Result: " . $arrToCompare);
		}
		
		pr("<hr/>");
		$conf  = Configure::getInstance() ;
		$filter["object_type_id"] = array($conf->objectTypes['area']["id"], $conf->objectTypes['section']["id"]);
		$tree = $this->Tree->getAll(null, null, null, $filter) ;
		pr("<h4>Load only publication and sections tree</h4>") ;
		echo $this->buildHtmlTree($tree);
		
		pr("<hr/>");
		pr("<h4>Load Tree with 'on' objects</h4>") ;
		$tree = $this->Tree->getAll(null, null, 'on') ;
		echo $this->buildHtmlTree($tree);
 	}
 	
	public function testAppendChild() {
		$idParent = $this->savedIds["Section 1"];
		$idParent2 = $this->savedIds["Section 12"];
		$idDoc = $this->savedIds["Document 1"];
		$res = $this->Tree->appendChild($idDoc, $idParent);
		if ($this->assertTrue($res, "Error appending Document 1 to Section 1")) {
			pr("<span style='color: green'>Document 1 appended to Section 1</span>");
		} 
		$res = $this->Tree->appendChild($idDoc, $idParent2);
		if ($this->assertTrue($res, "Error appending Document 1 to Section 12")) {
			pr("<span style='color: green'>Document 1 appended to Section 12</span>");
		}
		
		pr("Tree:");
		pr($this->Tree->find("all", array("conditions" => array("id" => $this->savedIds["Document 1"]))));
	}
	
	public function testDeleteAppendedChild() {
		$idDoc = $this->savedIds["Document 1"];
		$res = ClassRegistry::init("Document")->del($idDoc);
		if ($this->assertTrue($res)) {
			pr("<span style='color: green'>Document 1 deleted</span>");
		}
		
		$res = $this->Tree->find("all", array("conditions" => array("id" => $this->savedIds["Document 1"])));
		if ($this->assertEqual(array(), $res)) {
			pr("<span style='color: green'>Document 1 deleted from tree</span>");
		} else {
			pr("Tree:");
			pr($res);
		}
	}
	
	public function testDeleteBranch() {
		$idSection = $this->savedIds["Section 3"];
		$descendants = $this->Tree->getDescendants($idSection);
		$section = ClassRegistry::init("Section");
			
		$section->del($idSection);

		$treeRes = $this->Tree->find("all", array("conditions" => array("path LIKE '%/".$idSection."/%'")));
		if ($this->assertEqual(array(), $treeRes)) {
			pr("<span style='color: green'>Tree cleaned</span>");
		} else {
			pr("<span style='color: red'>Tree not cleaned:</span>");
			pr($treeRes);
		}
		
		$res = $section->findById($idSection);
		if ($this->assertEqual($res, array())) {
			pr("<span style='color: green'>section Section 3 deleted</span>");
		} else {
			pr("<span style='color: red'>section Section 3 not deleted</span>");
		}
		
		foreach ($descendants["items"] as $item) {
			$modelName = Configure::read("objectTypes.".$item["object_type_id"].".model"); 
			$res = ClassRegistry::init($modelName)->findById($item["id"]);
			if ($modelName == "Section") {
				if ($this->assertEqual($res, array())) {
					pr("<span style='color: green'>subsection " . $item["title"] . " deleted</span>");
				} else {
					pr("<span style='color: red'>subsection " . $item["title"] . " not deleted</span>");
				}
			} else {
				if ($this->assertNotEqual($res, array())) {
					pr("<span style='color: green'>object " . $item["title"] . "  not deleted</span>");
				}
			}
		}
		
		// following operations don't work because queries are cached in protected attribute Datasource::_queryCache but no method to delete exists
		//$tree = $this->Tree->getAll();
		//echo $this->buildHtmlTree($tree);
	}
	
	 
	public function testAppendChild() {
		$idParent = $this->savedIds["Section 1"];
		$idParent2 = $this->savedIds["Section 12"];
		$idDoc = $this->savedIds["Document 1"];
		$res = $this->Tree->appendChild($idDoc, $idParent);
		if ($this->assertTrue($res, "Error appending Document 1 to Section 1")) {
			pr("<span style='color: green'>Document 1 appended to Section 1</span>");
		} 
		$res = $this->Tree->appendChild($idDoc, $idParent2);
		if ($this->assertTrue($res, "Error appending Document 1 to Section 12")) {
			pr("<span style='color: green'>Document 1 appended to Section 12</span>");
		}
		
		pr("Tree:");
		pr($this->Tree->find("all", array("conditions" => array("id" => $this->savedIds["Document 1"]))));
	}
	
	public function testDeleteAppendedChild() {
		$idDoc = $this->savedIds["Document 1"];
		$res = ClassRegistry::init("Document")->del($idDoc);
		if ($this->assertTrue($res)) {
			pr("<span style='color: green'>Document 1 deleted</span>");
		}
		
		$res = $this->Tree->find("all", array("conditions" => array("id" => $this->savedIds["Document 1"])));
		if ($this->assertEqual(array(), $res)) {
			pr("<span style='color: green'>Document 1 deleted from tree</span>");
		} else {
			pr("Tree:");
			pr($res);
		}
	}
	
	public function testDeleteBranch() {
		$idSection = $this->savedIds["Section 3"];
		$descendants = $this->Tree->getDescendants($idSection);
		$section = ClassRegistry::init("Section");
			
		$section->del($idSection);

		$treeRes = $this->Tree->find("all", array("conditions" => array("path LIKE '%/".$idSection."/%'")));
		if ($this->assertEqual(array(), $treeRes)) {
			pr("<span style='color: green'>Tree cleaned</span>");
		} else {
			pr("<span style='color: red'>Tree not cleaned:</span>");
			pr($treeRes);
		}
		
		$res = $section->findById($idSection);
		if ($this->assertEqual($res, array())) {
			pr("<span style='color: green'>section Section 3 deleted</span>");
		} else {
			pr("<span style='color: red'>section Section 3 not deleted</span>");
		}
		
		foreach ($descendants["items"] as $item) {
			$modelName = Configure::read("objectTypes.".$item["object_type_id"].".model"); 
			$res = ClassRegistry::init($modelName)->findById($item["id"]);
			if ($modelName == "Section") {
				if ($this->assertEqual($res, array())) {
					pr("<span style='color: green'>subsection " . $item["title"] . " deleted</span>");
				} else {
					pr("<span style='color: red'>subsection " . $item["title"] . " not deleted</span>");
				}
			} else {
				if ($this->assertNotEqual($res, array())) {
					pr("<span style='color: green'>object " . $item["title"] . "  not deleted</span>");
				}
			}
		}
		
		// following operations doesn't work because queries are cached in protected attribute Datasource::_queryCache but no method to delete it exist
		//$tree = $this->Tree->getAll();
		//echo $this->buildHtmlTree($tree);
	}

	public function __construct () {
		parent::__construct('Tree', dirname(__FILE__)) ;
	}
	
	private function buildHtmlTree($tree) {
 		$htmlTree = "";
 		foreach ($tree as $root) {
			$htmlTree .= "<h2>" . $root["title"] . " - id: " . $root["id"] . " - " .$root["status"] . "</h2>";
			if (!empty($root["children"])) {
				$htmlTree .= $this->buildHtmlBranch($root["children"]);
			}
		}
		return $htmlTree;
 	}
 	
 	private function buildHtmlBranch($branch) {
 		$htmlBranch = "<ul style='padding-left: 10px;'>";
 		foreach ($branch as $b) {
 			$htmlBranch .= "<li style='list-style: circle; padding:0; margin: 10px; font-size: 1.2em;'>" . $b["title"] . " - id: " . $b["id"] . " - "  .$b["status"] . "</li>";
 			if (!empty($b["children"])) {
 				$htmlBranch .= $this->buildHtmlBranch($b["children"]);
 			}
 		}
 		$htmlBranch .= "</ul>";
 		return $htmlBranch;
 	}
 	
 	private function prepareTreeToCompare($tree, $count=false) {
 		if (!$count) {
	 		$branch = array();
	 		foreach ($tree as $item) {
	 			$b = array("title" => $item["title"], "status" => $item["status"]);
	 			if (!empty($item["children"])) {
	 				$b["children"] = $this->prepareTreeToCompare($item["children"]);
	 			} elseif ($item["object_type_id"] == Configure::read('objectTypes.area.id') || $item["object_type_id"] == Configure::read('objectTypes.section.id')) {
	 				$b["children"] = array();
	 			}
	 			$modelName = Configure::read('objectTypes.' .$item["object_type_id"] . '.model'); 
	 			$branch[][$modelName] = $b;
	 		}
	 		return $branch;
 		} else {
 			$count = 0;
 			foreach ($tree as $item) {
 				$count++;
 				if (!empty($item["children"])) {
 					$count += $this->prepareTreeToCompare($item["children"], true);
 				}
 			}
 			return $count;
 		}	
 	}
 	
 	private function saveObject($arrData, $parent_id=null) {
 		foreach ($arrData as $data) {
	 		foreach ($data as $modelName => $modeldata) {
		 		$model = ClassRegistry::init($modelName);
		 		$model->create();
		 		$modeldata["parent_id"] = $parent_id;
				$result = $model->save($modeldata);
				$id = $model->getInsertID();
				$this->savedIds[$modeldata["title"]] = $id;
				$this->countStatus[$modeldata["status"]]++;
				if (!empty($parent_id) && $modelName != "Area" && $modelName != "Section") {
					$this->Tree->appendChild($id, $parent_id);
				}
				if (!empty($modeldata["children"])) {
					$this->saveObject($modeldata["children"],$id);
				}
			}
 		}
 	}
	
}
?> 