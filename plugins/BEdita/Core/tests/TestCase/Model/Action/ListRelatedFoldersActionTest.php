<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\ListRelatedFoldersAction;
use BEdita\Core\Model\Entity\Folder;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * @coversDefaultClass \BEdita\Core\Model\Action\ListRelatedFoldersAction
 */
class ListRelatedFoldersActionTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Trees',
    ];

    /**
     * Test execute for `Parents` association.
     *
     * @return void
     *
     * @covers ::execute()
     * @covers \BEdita\Core\Model\Action\ListRelatedObjectsAction::initialize()
     */
    public function testExecuteParents()
    {
        $association = TableRegistry::getTableLocator()->get('Folders')->getAssociation('Parents');
        $action = new ListRelatedFoldersAction(compact('association'));
        $result = $action(['primaryKey' => 12]);
        static::assertInstanceOf(Folder::class, $result);
        static::assertEquals(11, $result->get('id'));
    }

    /**
     * Test execute for `Children` association.
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::initialize()
     */
    public function testExecuteChildren()
    {
        $association = TableRegistry::getTableLocator()->get('Folders')->getAssociation('Children');
        $action = new ListRelatedFoldersAction(compact('association'));
        $result = $action(['primaryKey' => 11]);

        static::assertInstanceOf(Query::class, $result);

        $children = $result->toArray();

        $actual = Hash::extract($children, '{n}.id');
        sort($actual);

        $treesTable = TableRegistry::getTableLocator()->get('Trees');
        $node = $treesTable->find()->where(['object_id' => 11])->first();
        $expected = $treesTable
            ->find('children', ['for' => $node->id, 'direct' => true])
            ->toArray();
        $expected = Hash::extract($expected, '{n}.object_id');
        sort($expected);

        static::assertSame($expected, $actual);
    }
}
