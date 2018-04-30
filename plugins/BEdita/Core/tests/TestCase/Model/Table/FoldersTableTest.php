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

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * BEdita\Core\Model\Table\FoldersTable Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\FoldersTable
 */
class FoldersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\FoldersTable
     */
    public $Folders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.object_relations',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.trees',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Folders = TableRegistry::get('Folders');
        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Folders);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     * @coversNothing
     */
    public function testInitialize()
    {
        $this->Folders->initialize([]);
        $this->assertEquals('objects', $this->Folders->getTable());
        $this->assertEquals('id', $this->Folders->getPrimaryKey());
        $this->assertEquals('title', $this->Folders->getDisplayField());

        $this->assertInstanceOf(BelongsToMany::class, $this->Folders->Children);
    }

    /**
     * Data provider for testHasAtMostOneParent()
     *
     * @return array
     */
    public function hasAtMostOneParentProvider()
    {
        return [
            'emptyParents' => [
                true,
                [],
            ],
            'valid' => [
                true,
                [
                    ['id' => 11],
                ],
            ],
            'tooMany' => [
                false,
                [
                    ['id' => 11],
                    ['id' => 13],
                ],
            ],
            'missingId' => [
                false,
                [
                    ['title' => 'pippo'],
                ],
            ],
        ];
    }

    /**
     * Test hasAtMostOneParent method
     *
     * @return void
     *
     * @dataProvider hasAtMostOneParentProvider()
     * @covers ::hasAtMostOneParent()
     */
    public function testHasAtMostOneParent($expected, $parents)
    {
        $entity = $this->Folders->newEntity(['parents' => $parents], [
            'accessibleFields' => ['parents' => true, 'id' => true],
        ]);

        $actual = $this->Folders->hasAtMostOneParent($entity);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testSave` test case
     *
     * @return array
     */
    public function saveProvider()
    {
        return [
            'parentNotSet' => [
                11,
                [
                    'id' => 12,
                    'title' => 'change title',
                ],
            ],
            'parentNotChanged' => [
                11,
                [
                    'id' => 12,
                    'title' => 'change title',
                    'parents' => [
                        ['id' => 11],
                    ],
                ],
            ],
            'createRootFolder' => [
                null,
                [
                    'title' => 'Folder as root',
                ],
            ],
            'createSubfolder' => [
                11,
                [
                    'title' => 'Subfolder',
                    'parents' => [
                        ['id' => 11],
                    ],
                ],
            ],
            'becomeRoot' => [
                null,
                [
                    'id' => 12,
                    'parents' => [],
                ],
            ],
            'changeParent' => [
                13,
                [
                    'id' => 12,
                    'parents' => [
                        ['id' => 13],
                    ],
                ],
            ],
            'tooManyParents' => [
                false,
                [
                    'id' => 12,
                    'parents' => [
                        ['id' => 13],
                        ['id' => 11],
                    ],
                ],
            ],
            'tooManyParents2' => [
                false,
                [
                    'id' => 12,
                    'parents' => [
                        '_ids' => [13, 11]
                    ],
                ],
            ],
            'ok' => [
                13,
                [
                    'id' => 12,
                    'parents' => [
                        '_ids' => [13],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test save folder
     *
     * @param mixed $expected The expected result
     * @param array $data The data to save
     * @return void
     *
     * @dataProvider saveProvider
     * @covers ::beforeSave()
     * @covers ::afterSave()
     */
    public function testSave($expected, $data)
    {
        $trees = TableRegistry::get('Trees');
        if (!empty($data['id'])) {
            $node = $trees->find()->where(['object_id' => $data['id']])->first();
            $descendants = $trees->childCount($node);
        }

        $entity = $this->Folders->newEntity($data, [
            'accessibleFields' => ['parents' => true, 'id' => true],
        ]);
        $entity->type = 'folders';

        $entity = $this->Folders->save($entity);
        if ($expected === false) {
            static::assertFalse((bool)$entity);

            return;
        }
        static::assertTrue((bool)$entity);

        $actual = $this->Folders->get($entity->id, ['contain' => 'Parents']);
        static::assertSame($expected, $actual->parent_id);

        if (!empty($data['id'])) {
            $node = $trees->find()->where(['object_id' => $data['id']])->first();
            $actual = $trees->childCount($node);
            static::assertEquals($descendants, $actual);
        }
    }

    /**
     * Test `findRoots()`
     *
     * @covers ::findRoots()
     */
    public function testFindRoots()
    {
        $folders = $this->Folders->find('roots')->toArray();
        static::assertNotEmpty($folders);
        $ids = Hash::extract($folders, '{n}.id');
        static::assertEquals([11, 13], $ids);
    }
}
