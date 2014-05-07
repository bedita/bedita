<?php

namespace BEdita\Test\TestCase\Model;

use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use BEdita\Model\Table\DocumentObjectsTable;

class DocumentObjectsTableTest extends TestCase {

    public $documentTable = null;

    public function setUp() {
        parent::setUp();
        $this->documentTable = TableRegistry::get('DocumentObjects');
    }

    public function testConf() {
        $objectTypeId = $this->documentTable->objectTypeId();
        $this->assertEquals(22, $objectTypeId);

        $objectChain = $this->documentTable->getObjectChain();
        $this->assertEquals(['Contents'], $objectChain);
    }
}