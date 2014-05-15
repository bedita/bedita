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

    public function testSave() {
        $objectsData = [
            'id' => 28,
            'title' => 'Document title',
        ];
        $contentsData = [
            'abstract' => 'Document abstract',
            'body' => 'Document body',
            'start_date' => '2014-05-12 00:00:00',
            'end_date' => '2014-05-15 20:30:00',
        ];

        $data = array_merge($objectsData, $contentsData);

        $docEntity = $this->documentTable->newEntity($data);
        $res = $this->documentTable->save($docEntity);
        $this->assertNotEquals(false, $res);

        // check contents table
        $contents = TableRegistry::get('Contents');
        $contentRes = $contents->find()
            ->where(['Contents.id' => $docEntity->id])
            ->first();

        foreach ($contentsData as $key => $value) {
            if ($key == 'start_date' || $key == 'end_date') {
                $this->assertInstanceOf('Cake\Utility\Time', $contentRes->$key);
                $this->assertEquals($value, $contentRes->$key->format('Y-m-d H:i:s'));
            } else {
                $this->assertEquals($value, $contentRes->$key);
            }
        }
    }
}
