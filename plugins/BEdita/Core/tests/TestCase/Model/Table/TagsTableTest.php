<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Table\TagsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\TagsTable
 */
class TagsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\TagsTable
     */
    public $Tags;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Tags = TableRegistry::getTableLocator()->get('Tags');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Tags);
        parent::tearDown();
    }

    /**
     * Test `beforeFind` method
     *
     * @return void
     * @covers ::beforeFind()
     */
    public function testBeforeFindPrimary()
    {
        $tag = $this->Tags->get(4)->toArray();
        $expected = [
            'id' => 4,
            'name' => 'first-tag',
            'label' => 'First tag',
            'enabled' => true,
        ];
        unset($tag['created'], $tag['modified']);
        static::assertEquals($expected, $tag);
    }

    /**
     * Test `beforeFind` method in case of association
     *
     * @return void
     * @covers ::beforeFind()
     */
    public function testBeforeFindAssoc()
    {
        $profile = TableRegistry::getTableLocator()->get('Profiles')
            ->get(4, ['contain' => ['Tags']])
            ->toArray();
        $expected = [
            [
                'name' => 'first-tag',
                'label' => 'First tag',
                'params' => null,
            ],
        ];
        static::assertArrayHasKey('tags', $profile);
        static::assertEquals($expected, $profile['tags']);
    }

    /**
     * Test `findEnabled` method
     *
     * @return void
     * @coversNothing
     */
    public function testFindEnabled()
    {
        $tags = $this->Tags->find('enabled')->toArray();
        static::assertEquals([4], Hash::extract($tags, '{n}.id'));
    }

    /**
     * Test `findIds` method
     *
     * @return void
     * @covers ::findIds()
     */
    public function testFindIds()
    {
        $tags = $this->Tags->find('ids', ['names' => ['first-tag']])->toArray();
        static::assertEquals(1, count($tags));
        static::assertEquals(4, $tags[0]['id']);
    }
}
