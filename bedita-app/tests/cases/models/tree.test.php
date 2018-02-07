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

class TreeTestCase extends BeditaTestCase {

 	var $uses = array('Tree') ;
 	private $savedIds = array();
 	private $countStatus = array("on" => 0, "off" => 0, "draft" => 0);

 	public function testBuildTree() {
 		$this->Tree->cacheQueries = false;
 		pr("Building tree:");
 		// clean tree
 		$this->Tree->deleteAll(array("object_path LIKE '/%'"));
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

 	public function testCloneStructure() {
 		$this->requiredData(array("buildTree"));
 		$pubId = $this->savedIds['Publication 1'];
 		$idConversion = $this->Tree->cloneStructure($pubId, array('keepTitle' => true));
 		$clonedPubId = $idConversion[$pubId];
 		$clonedTree = $this->Tree->getAll($clonedPubId);
 		$arrToCompare = $this->prepareTreeToCompare($clonedTree);
 		if (!$this->assertEqual($arrToCompare[0], $this->data['buildTree'][0])) {
			pr("Original structure:");
			pr($this->data['buildTree'][0]);
			pr("Cloned strucuture:");
			pr($arrToCompare[0]);
		}
 	}

	function testSave() {
		$object_path = "/" . $this->savedIds["Section 7"] . "/" . $this->savedIds["Section 9"] . "/" . $this->savedIds["Section 7"] . "/" . $this->savedIds["Event 1"];
		$parent_path = "/" . $this->savedIds["Section 7"] . "/" . $this->savedIds["Section 9"] . "/" . $this->savedIds["Section 7"];
		$id = $this->savedIds["Event 1"];

		// Saving with empty object_path
		$data1["Tree"] = array(
			"id" => $id,
			"object_path" => ""
		);
		$res = $this->Tree->save($data1);
		pr("<h4>Saving with empty object_path</h4>");
		if ($this->assertFalse($res, "Error tree saved without object_path")) {
			pr("<span style='color: green'>Save tree with empty object_path failed</span>");
		}

		// Saving with empty parent_path
		$data2["Tree"] = array(
			"id" => $id,
			"parent_path" => ""
		);
		$res = $this->Tree->save($data2);
		pr("<h4>Saving with empty parent_path</h4>");
		if ($this->assertFalse($res, "Error tree saved without parent_path")) {
			pr("<span style='color: green'>Save tree with empty parent_path failed</span>");
		}

		// Saving with inconsistent object_path (recursion on path)
		$data3["Tree"] = array(
			"id" => $id,
			"object_path" => $object_path
		);
		$res = $this->Tree->save($data3);
		pr("<h4>Saving with recursion on object_path</h4>");
		if ($this->assertFalse($res, "Error tree saved recursion on  object_path")) {
			pr("<span style='color: green'>Save tree with recursion on object_path (".$object_path.")failed</span>");
		}

		// Saving with inconsistent parent_path (recursion on path)
		$data4["Tree"] = array(
			"id" => $id,
			"parent_path" => $parent_path
		);
		$res = $this->Tree->save($data4);
		pr("<h4>Saving with recursion on parent_path</h4>");
		if ($this->assertFalse($res, "Error tree saved recursion on  parent_path")) {
			pr("<span style='color: green'>Save tree with recursion on parent_path (".$parent_path.") failed</span>");
		}

		// Saving with id = parent_id (recursion on itself)
		$data5["Tree"] = array(
			"id" => $id,
			"parent_id" => $id
		);
		$res = $this->Tree->save($data5);
		pr("<h4>Saving with id = parent_id (recursion on itself)</h4>");
		if ($this->assertFalse($res, "Error tree saved recursion on itself id=parent_id")) {
			pr("<span style='color: green'>Save tree with recursion failed: id=parent_id=".$id."</span>");
		}

		// Saving with id recursion on parent_path
		$data6["Tree"] = array(
			"id" => $id,
			"parent_path" => "/" . $this->savedIds["Event 1"] . "/" . $this->savedIds["Section 9"] . "/" . $this->savedIds["Section 7"] . "/"
		);
		$res = $this->Tree->save($data6);
		pr("<h4>Saving with id recursion on parent_path</h4>");
		if ($this->assertFalse($res, "Error tree saved with id recursion on parent_path")) {
			pr("<span style='color: green'>Save tree with recursion failed: id=".$id." is also in parent_path=".$data6["Tree"]["parent_path"]."</span>");
		}
	}

	function testIsParent() {
		$idParent = $this->savedIds["Publication 1"];
		$idChild = $this->savedIds["Section 1"];
		$res = $this->Tree->isParent($idParent, $idChild);
		if ($this->assertTrue($res, "Error verifying parent Publication 1 (id=". $idParent .") for Section 1 (id=" .$idChild .")")) {
			pr("<span style='color: green'>Publication 1 (id=". $idParent .") is parent or ancestor of Section 1 (id=" .$idChild .")</span>");
		}

		$res = $this->Tree->isParent($idChild, $idParent);
		if ($this->assertFalse($res, "Error verifying parent Section 1 (id=". $idChild .") for Publication 1 (id=" .$idParent .")")) {
			pr("<span style='color: green'>Section 1 (id=". $idChild .") is not parent or ancestor of Publication 1 (id=" .$idParent .")</span>");
		}

		$idParent = $this->savedIds["Section 3"];
		$idChild = $this->savedIds["Document 2"];
		$res = $this->Tree->isParent($idParent, $idChild);
		if ($this->assertTrue($res, "Error verifying parent Section 3 (id=". $idParent .") for Document 2 (id=" .$idChild .")")) {
			pr("<span style='color: green'>Section 3 (id=". $idParent .") is parent or ancestor of Document 2 (id=" .$idChild .")</span>");
		}
	}

	public function testAppendToItself() {
		$idSection = $this->savedIds["Section 1"];
		$res = $this->Tree->appendChild($idSection, $idSection);
		pr("<h4>Try to append Section 1 to itself</h4>");
		if ($this->assertFalse($res, "Error appending Section 1 (id=" .$idSection .") to itself")) {
			pr("<span style='color: green'>Section 1 (id=" .$idSection .") can't be appended to itself</span>");
		}
	}

	public function testAppendChild() {
		$idParent = $this->savedIds["Section 1"];
		$idParent2 = $this->savedIds["Section 12"];
		$idDoc = $this->savedIds["Document 1"];
		$res = $this->Tree->appendChild($idDoc, $idParent);
		if ($this->assertTrue($res, "Error appending Document 1 (id=". $idDoc .") to Section 1 (id=" .$idParent .")")) {
			pr("<span style='color: green'>Document 1 (id=". $idDoc .") appended to Section 1 (id=" .$idParent .")</span>");
		}
		$res = $this->Tree->appendChild($idDoc, $idParent2);
		if ($this->assertTrue($res, "Error appending Document 1 (id=". $idDoc .") to Section 12 (id=" .$idParent2 .")")) {
			pr("<span style='color: green'>Document 1 (id=". $idDoc .") appended to Section 12 (id=" .$idParent2 .")</span>");
		}

		pr("Tree:");
		$tree = $this->Tree->find("all", array("conditions" => array("id" => $this->savedIds["Document 1"])));
		pr($tree);
	}

	public function testFailSaveSectionAlreadyOnTree() {
		$idParent = $this->savedIds["Section 1"];
		$idSection = $this->savedIds["Section 12"];
		$res = $this->Tree->appendChild($idSection, $idParent);
		$this->assertFalse($res);
	}

	public function testDeleteAppendedChild() {
		$idDoc = $this->savedIds["Document 1"];
		$res = ClassRegistry::init("Document")->delete($idDoc);
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

	public function testRecursiveMove() {
		$idToMove = $this->savedIds["Section 14"];
		$idNewParent = $this->savedIds["Section 14"];
		$idOldParent = $this->savedIds["Section 13"];
		$result = $this->Tree->move($idNewParent, $idOldParent, $idToMove);
		pr("<h4>Try to insert section 14 (id:".$idToMove.") inside itself</h4>");
		if ($this->assertEqual(false, $result)) {
			pr("<span style='color: green'>Section 14 (id:".$idToMove.") can't move inside itself</span>");
		}
	}

	public function testMove() {
		$idToMove = $this->savedIds["Section 14"];
		$idNewParent = $this->savedIds["Section 7"];
		$idOldParent = $this->savedIds["Section 13"];
		$children = array($this->savedIds["Section 18"], $this->savedIds["ShortNews 1"], $this->savedIds["Card 1"]);
		$result = $this->Tree->move($idNewParent, $idOldParent, $idToMove);
		if ($this->assertNotEqual(false, $result)) {
			pr("<span style='color: green'>Section 14 (id:".$idToMove.") and its content moved from Section 13 (id:".$idOldParent.") to Section 7 (id:".$idNewParent.")</span>");
		}

		$res = $this->Tree->find("first", array(
			"conditions" => array(
				"id" => $idToMove,
				"parent_id" => $idOldParent
			)
		));

		if ($this->assertEqual(false, $res)) {
			pr("<span style='color: green'>Section 13 (id:".$idOldParent.") is no longer parent of Section 14 (id:".$idToMove.")</span>");
		}

		$newPath = $this->Tree->field("object_path", array(
			"id" => $idToMove,
			"parent_id" => $idNewParent
		));

		$res = $this->Tree->find("all", array(
			"conditions" => array(
				"parent_path" => $newPath
			)
		));

		$childTest = true;
		foreach ($res as $r) {
			if (!in_array($r["Tree"]["id"], $children)) {
				$childTest = false;
				break;
			}
		}

		if ($this->assertIdentical(true, $childTest)) {
			pr("<span style='color: green'>All children moved</span>");
		}

	}

	public function testMovePriority() {
		$prioritySec3 = $this->Tree->field("priority", array("id" => $this->savedIds["Section 3"]));
		$prioritySec4 = $this->Tree->field("priority", array("id" => $this->savedIds["Section 4"]));
		pr("priority Section 3: ".$prioritySec3);
		pr("priority Section 4: ".$prioritySec4);

		// move priority down
		pr("<h3>moving priority down...</h3>");
		$res = $this->Tree->movePriorityDown($this->savedIds["Section 3"], $this->savedIds["Publication 1"]);
		if ($this->assertIdentical(true, $res)) {
			pr("<span style='color: green'>Section 3 moved down</span>");
		}
		$newPrioritySec3 = $this->Tree->field("priority", array("id" => $this->savedIds["Section 3"]));
		$newPrioritySec4 = $this->Tree->field("priority", array("id" => $this->savedIds["Section 4"]));
		$this->assertEqual($newPrioritySec3, $prioritySec4);
		$this->assertEqual($newPrioritySec4, $prioritySec3);
		pr("priority Section 3 after move down: ".$newPrioritySec3);
		pr("priority Section 4 after move down: ".$newPrioritySec4);

		// move priority up
		pr("<h3>moving priority up...</h3>");
		$res = $this->Tree->movePriorityUp($this->savedIds["Section 3"], $this->savedIds["Publication 1"]);
		if ($this->assertIdentical(true, $res)) {
			pr("<span style='color: green'>Section 3 moved up</span>");
		}
		$newPrioritySec3 = $this->Tree->field("priority", array("id" => $this->savedIds["Section 3"]));
		$newPrioritySec4 = $this->Tree->field("priority", array("id" => $this->savedIds["Section 4"]));
		$this->assertEqual($newPrioritySec3, $prioritySec3);
		$this->assertEqual($newPrioritySec4, $prioritySec4);
		pr("priority Section 3 after move up: ".$newPrioritySec3);
		pr("priority Section 4 after move up: ".$newPrioritySec4);

		// test fail move down (last priority item)
		$item = $this->Tree->find("first", array(
			"conditions" => array(
				"id" => $this->savedIds["Section 5"],
				"parent_id" => $this->savedIds["Publication 1"]
			)
		));
		$res = $this->Tree->movePriorityDown($this->savedIds["Section 5"], $this->savedIds["Publication 1"]);
		$this->assertIdentical(false, $res);
		$itemAfter = $this->Tree->find("first", array(
			"conditions" => array(
				"id" => $this->savedIds["Section 5"],
				"parent_id" => $this->savedIds["Publication 1"]
			)
		));
		$this->assertIdentical($item, $itemAfter);

		// test fail move up (first priority item)
		$item = $this->Tree->find("first", array(
			"conditions" => array(
				"id" => $this->savedIds["Section 1"],
				"parent_id" => $this->savedIds["Publication 1"]
			)
		));
		$res = $this->Tree->movePriorityUp($this->savedIds["Section 1"], $this->savedIds["Publication 1"]);
		$this->assertIdentical(false, $res);
		$itemAfter = $this->Tree->find("first", array(
			"conditions" => array(
				"id" => $this->savedIds["Section 1"],
				"parent_id" => $this->savedIds["Publication 1"]
			)
		));
		$this->assertIdentical($item, $itemAfter);
	}

	public function testSetPriority() {
		$res = $this->Tree->setPriority($this->savedIds["Section 1"], 10, $this->savedIds["Publication 1"]);
		$this->assertIdentical(true, $res);
		$priority = $this->Tree->field("priority", array(
			"id" => $this->savedIds["Section 1"],
			"parent_id" => $this->savedIds["Publication 1"]
		));
		$this->assertEqual(10, $priority);
	}

	public function testDeleteBranch() {
		$idSection = $this->savedIds["Section 3"];
		$descendants = $this->Tree->getDescendants($idSection);
		$section = ClassRegistry::init("Section");

		$section->delete($idSection);

		$treeRes = $this->Tree->find("all", array("conditions" => array("object_path LIKE '%/".$idSection."/%'")));
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

	public function testExcludedFromTree() {
		$excludedIds = Configure::read('excludeFromTreeIds');
		$idParent1 = $this->savedIds['Publication 1'];
		$idParent2 = $this->savedIds['Publication 2'];
		$children1 = 4;
		$children2 = 5;
		$testDesc = "Tree->getAll($idParent1)";
		Configure::write('excludeFromTreeIds', array($idParent1));
		// excludeFromTreeIds $idParent1 - exclude_branch-filter-false
		$filter = array('exclude_branch' => false);
		$tree = $this->Tree->getAll($idParent1, null, null, $filter);
		$res = ( isset($tree[0]['children']) && (count($tree[0]['children']) === $children1) );
		if ($this->assertTrue($res, "[$testDesc | exclude_branch-filter-false] Error verifying parent Publication 1 (id=". $idParent1 .")")) {
			pr("<span style='color: green'>[$testDesc | exclude_branch-filter-false] Publication 1 (id=". $idParent1 .") has $children1 children visible</span>");
		}
		// excludeFromTreeIds $idParent1 - unset exclude_branch-filter
		unset($filter['exclude_branch']);
		$tree = $this->Tree->getAll($idParent1, null, null, $filter);
		$res = ( isset($tree[0]['children']) && (count($tree[0]['children']) === 0) );
		if ($this->assertTrue($res, "[$testDesc | exclude_branch-filter unset] Error verifying parent Publication 1 (id=". $idParent1 .")")) {
			pr("<span style='color: green'>[$testDesc | exclude_branch-filter unset] Publication 1 (id=". $idParent1 .") has 0 children visible</span>");
		}
		// excludeFromTreeIds $idParent2 - verify other parent $idParent2 - exclude_branch-filter-false
		$filter = array('exclude_branch' => false);
		$tree = $this->Tree->getAll($idParent2, null, null, $filter);
		$res = ( !empty($tree[0]['children']) && (count($tree[0]['children']) === $children2) );
		if ($this->assertTrue($res, "[$testDesc | exclude_branch-filter-false] Error verifying parent Publication 2 (id=". $idParent2 .")")) {
			pr("<span style='color: green'>[$testDesc | exclude_branch-filter-false] Publication 2 (id=". $idParent2 .") has $children2 children visible</span>");
		}
		// excludeFromTreeIds $idParent - verify other parent $idParent2 - exclude_branch-filter unset
		unset($filter['exclude_branch']);
		$tree = $this->Tree->getAll($idParent2, null, null, $filter) ;
		$res = ( !empty($tree[0]['children']) && (count($tree[0]['children']) === 5) );
		if ($this->assertTrue($res, "[$testDesc | exclude_branch-filter unset] Error verifying parent Publication 2 (id=". $idParent2 .")")) {
			pr("<span style='color: green'>[$testDesc | exclude_branch-filter unset] Publication 2 (id=". $idParent2 .") has $children2 children visible</span>");
		}
		// end
		Configure::write('excludeFromTreeIds', $excludedIds);
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