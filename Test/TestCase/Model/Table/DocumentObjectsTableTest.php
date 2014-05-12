<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
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
namespace BEdita\Test\TestCase\Model\Table;

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