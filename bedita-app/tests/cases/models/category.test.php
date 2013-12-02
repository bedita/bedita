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
 * CategoryTestCase class
 */
require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class CategoryTestCase extends BeditaTestCase {

 	public $uses		= array('Document','Category') ;
	public $dataSource	= 'test' ;

 	/**
	 * clean categories table
	 */
	public function testCleanCategories() {
		$this->Category->Behaviors->disable('CompactResult');
		$res = $this->Category->deleteAll(array('object_type_id IS NOT NULL'));
		$this->assertEqual($res, true);
		$this->Category->Behaviors->enable('CompactResult');
	}

	public function testCleanTags() {
		$this->Category->Behaviors->disable('CompactResult');
		$res = $this->Category->deleteAll(array('object_type_id IS NULL'));
		$this->assertEqual($res, true);
		$this->Category->Behaviors->enable('CompactResult');
	}

 	public function testTags() {

 		// insert tags
 		$this->requiredData(array("category"));
 		$insertTag = $this->data['tag']['insert'];
 		$insertedTags = array();
 		foreach ($insertTag as $t) {
 			$this->Category->create();
			$res = $this->Category->save($t);
			$this->assertEqual($res, true);
			$insertedTags[] = $this->Category->id;
 		}

 		$insertOrphanTag = $this->data['tag']['insertOrphan'];
 		$orphanTags = array();
 		foreach ($insertOrphanTag as $t) {
 			$this->Category->create();
			$res = $this->Category->save($t);
			$this->assertEqual($res, true);
			$orphanTags[] = $this->Category->id;
 		}

 		$insertOffDraft = $this->data['tag']['insertOffDraft'];
 		$offDraftTags = array();
 		foreach ($insertOffDraft as $t) {
 			$this->Category->create();
			$res = $this->Category->save($t);
			$this->assertEqual($res, true);
			$offDraftTags[] = $this->Category->id;
 		}

 		// insert document with attached tags
 		$docData = $this->data['document']['insert'];
 		$docData['Category'] = array_merge($insertedTags, $offDraftTags);
 		$res = $this->Document->save($docData);
 		$this->assertEqual($res, true);
 		$docId = $this->Document->id;

		// show orphans
 		$result = $this->Category->getTags();
 		$this->assertEqual(count($result), count(array_merge($insertedTags, $offDraftTags, $orphanTags)));
		pr("Tags with orphans:");
 		pr($result);

 		$result = $this->Category->getTags(array("showOrphans" => false));
 		$this->assertEqual(count($result), count(array_merge($insertedTags, $offDraftTags)));
		pr("Tags without orphans:");
 		pr($result);

 	 	$result = $this->Category->getTags(array("status" => 'on'));
 	 	$this->assertEqual(count($result), count(array_merge($insertedTags, $orphanTags)));
		pr("Tags with status: on");
 		pr($result);

 	 	$result = $this->Category->getTags(array("status" => array('on', 'off', 'draft')));
 	 	$this->assertEqual(count($result), count(array_merge($insertedTags, $offDraftTags, $orphanTags)));
		pr("Tags with status: on/off/draft");
 		pr($result);

 	 	$result = $this->Category->getTags(array("cloud" => true));
 	 	foreach ($result as $t) {
 	 		if (in_array($t['id'], $orphanTags) && empty($t['class'])) {
 	 			$this->assertTrue(true);
 	 		} else {
 	 			$this->assertIsA($t['class'], 'string');
 	 		}
 	 	}
		pr("Tags with cloud: ");
		pr($result);
 	}

 	public function testCategoryName() {
 		$this->requiredData(array("category"));

 		$this->Category->Behaviors->disable('CompactResult');

 		// save category
		$result = $this->Category->save($this->data['category']['insert']);
	 	$this->assertEqual($result, true);
	 	$firstCategoryId = $this->Category->id;
	 	// check category name
	 	$categoryName = $this->Category->field('name', array('id' => $this->Category->id));
	 	$nameExpected = $this->data['category']['nameExpected'];
	 	$this->assertEqual($categoryName, $nameExpected);

	 	// insert category with same values, the name would be the old one concat '-1'
	 	$this->Category->create();
	 	$result = $this->Category->save($this->data['category']['insert']);
	 	$this->assertEqual($result, true);
	 	$categoryName = $this->Category->field('name', array('id' => $this->Category->id));
	 	$nameExpected2 = $nameExpected . '-1';
	 	$this->assertEqual($categoryName, $nameExpected2);

	 	// save first category changing lable (it would maintain the same name)
	 	$data = $this->data['category']['insert'];
	 	$data['id'] = $firstCategoryId;
	 	$data['label'] = 'changing category label';
	 	$this->Category->create();
	 	$result = $this->Category->save($data);
	 	$this->assertEqual($result, true);
	 	$categoryName = $this->Category->field('name', array('id' => $this->Category->id));
	 	$this->assertEqual($categoryName, $nameExpected);

	 	// changing name
		$data['name'] = 'my new name';
		$result = $this->Category->save($data);
	 	$this->assertEqual($result, true);
	 	$categoryName = $this->Category->field('name', array('id' => $this->Category->id));
	 	$this->assertEqual($categoryName, 'my-new-name');

	 	// pass name empty to rebuild name from label
	 	$data['name'] = '';
	 	$result = $this->Category->save($data);
	 	$this->assertEqual($result, true);
	 	$categoryName = $this->Category->field('name', array('id' => $this->Category->id));
	 	$this->assertEqual($categoryName, 'changing-category-label');

	 	// save category related to document with label image (it's name would be image-1)
	 	// insert category with same values, the name would be the old one concat '-1'
	 	$data = $this->data['category']['insert'];
	 	$data['label'] = 'image';
	 	$this->Category->create();
	 	$result = $this->Category->save($data);
	 	$this->assertEqual($result, true);
	 	$categoryName = $this->Category->field('name', array('id' => $this->Category->id));
	 	$this->assertEqual($categoryName, 'image-1');

	 	// test special category used for multimedia
	 	$this->requiredData(array("mediaCategory"));
	 	$mediaData = $this->data['mediaCategory']['insert'];
	 	$result = $this->Category->save($mediaData);
	 	$this->assertEqual($result, true);
	 	// check category name
	 	$categoryName = $this->Category->field('name', array('id' => $this->Category->id));
	 	$nameExpected = $this->data['mediaCategory']['nameExpected'];
	 	$this->assertEqual($categoryName, $nameExpected);

	 	// clean categories
	 	$this->testCleanCategories();
 	}
	
	public function __construct () {
		parent::__construct('Category', dirname(__FILE__));
	}
}

?>