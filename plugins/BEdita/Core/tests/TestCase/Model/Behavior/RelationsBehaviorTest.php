<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Entity\Relation;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Behavior\RelationsBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\RelationsBehavior
 */
class RelationsBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * Test initial setup
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::objectType()
     * @covers ::setupRelations()
     * @covers ::relatedTo()
     */
    public function testInitialization()
    {
        TableRegistry::clear();

        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $Profiles = TableRegistry::getTableLocator()->get('Profiles');
        $Locations = TableRegistry::getTableLocator()->get('Locations');

        static::assertTrue($Documents->hasBehavior('ObjectType'));
        static::assertTrue($Profiles->hasBehavior('ObjectType'));
        static::assertTrue($Locations->hasBehavior('ObjectType'));

        static::assertSame(2, $Documents->objectType()->id);
        static::assertSame(3, $Profiles->objectType()->id);

        static::assertInstanceOf(BelongsToMany::class, $Documents->getAssociation('Test'));
        static::assertSame('BEdita/Core.Objects', $Documents->getAssociation('Test')->getClassName());
        static::assertInstanceOf(BelongsToMany::class, $Documents->getAssociation('InverseTest'));
        static::assertSame('BEdita/Core.Objects', $Documents->getAssociation('InverseTest')->getClassName());
        static::assertInstanceOf(BelongsToMany::class, $Profiles->getAssociation('InverseTest'));
        static::assertSame('BEdita/Core.Objects', $Profiles->getAssociation('InverseTest')->getClassName());
        static::assertInstanceOf(BelongsToMany::class, $Locations->getAssociation('InverseAnotherTest'));
        static::assertSame('BEdita/Core.Users', $Locations->getAssociation('InverseAnotherTest')->getClassName());

        $before = count($Profiles->associations()->keys());
        $Profiles->setupRelations('profiles');
        $after = count($Profiles->associations()->keys());

        static::assertSame($before, $after);
    }

    /**
     * Test that no error occurs on an unknown object type, and no associations are set up.
     *
     * @return void
     *
     * @covers ::setupRelations()
     */
    public function testUnknownObjectType()
    {
        $FakeArticles = TableRegistry::getTableLocator()->get('FakeArticles');

        $before = count($FakeArticles->associations()->keys());
        $FakeArticles->addBehavior('BEdita/Core.Relations');
        $after = count($FakeArticles->associations()->keys());

        static::assertSame($before, $after);
    }

    /**
     * Test getter of relations.
     *
     * @return void
     *
     * @covers ::getRelations()
     */
    public function testGetRelations()
    {
        $expected = [
            'test',
            'inverse_test',
        ];

        $Documents = TableRegistry::getTableLocator()->get('Documents');

        static::assertTrue($Documents->behaviors()->hasMethod('getRelations'));

        $relations = $Documents->behaviors()->call('getRelations');
        static::assertEquals($expected, array_keys($relations));
        foreach ($relations as $relation) {
            static::assertInstanceOf(Relation::class, $relation);
        }
    }

    /**
     * Data provider for `testFindRelated` test case.
     *
     * @return array
     */
    public function findRelatedProvider()
    {
        return [
            'test 3' => [
                'Documents',
                [
                    'test' => 3,
                ],
                [2],
            ],
            'inverse' => [
                'Locations',
                [
                    'inverse_another_test' => [1, 5],
                ],
                [8],
            ],
            'inverse 2' => [
                'Profiles',
                [
                    'inverse_test' => '1,2',
                ],
                [4],
            ],
        ];
    }

    /**
     * Test findRelated finder method.
     *
     * @param array $conditions Date conditions.
     * @param array|false $numExpected Number of expected results.
     * @return void
     *
     * @dataProvider findRelatedProvider
     * @covers ::findRelated()
     */
    public function testFindRelated(string $type, array $options, array $expected): void
    {
        $table = TableRegistry::getTableLocator()->get($type);
        $result = $table->find('related', $options)->order([$table->aliasField('id') => 'ASC'])->toArray();
        $found = Hash::extract($result, '{n}.id');
        static::assertEquals($expected, $found);
    }

    /**
     * Data provider for `testBadRelated` test case.
     *
     * @return array
     */
    public function badRelatedProvider()
    {
        return [
            'empty' => [
                'Profiles',
                [],
                new BadFilterException('Invalid options for finder "related"')
            ],
            'not exist' => [
                'Documents',
                [
                    'some_relation' => 999,
                ],
                new BadFilterException('Bad relation "some_relation"')
            ],
        ];
    }

    /**
     * Test finder error.
     *
     * @param string $type Object type.
     * @param array $options Filter options.
     * @return void
     *
     * @dataProvider badRelatedProvider
     * @covers ::findRelated()
     */
    public function testBadRelated(string $type, array $options, $expected)
    {
        $this->expectException(get_class($expected));
        $this->expectExceptionMessage($expected->getMessage());

        $table = TableRegistry::getTableLocator()->get($type);
        $table->find('related', $options)->toArray();
    }
}
