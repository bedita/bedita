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

use BEdita\Core\Model\Entity\Relation;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
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

        $Documents = TableRegistry::get('Documents');
        $Profiles = TableRegistry::get('Profiles');
        $News = TableRegistry::get('News');

        static::assertSame(1, $Documents->objectType()->id);
        static::assertSame(2, $Profiles->objectType()->id);

        static::assertInstanceOf(BelongsToMany::class, $Documents->association('Test'));
        static::assertSame('BEdita/Core.Objects', $Documents->association('Test')->className());
        static::assertInstanceOf(BelongsToMany::class, $Documents->association('InverseTest'));
        static::assertSame('BEdita/Core.Objects', $Documents->association('InverseTest')->className());
        static::assertInstanceOf(BelongsToMany::class, $Profiles->association('InverseTest'));
        static::assertSame('BEdita/Core.Objects', $Profiles->association('InverseTest')->className());
        static::assertInstanceOf(BelongsToMany::class, $News->association('AnotherTest'));
        static::assertSame('BEdita/Core.Locations', $News->association('AnotherTest')->className());

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
        $FakeArticles = TableRegistry::get('FakeArticles');

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

        $Documents = TableRegistry::get('Documents');

        static::assertTrue($Documents->behaviors()->hasMethod('getRelations'));

        $relations = $Documents->behaviors()->call('getRelations');
        static::assertEquals($expected, array_keys($relations));
        foreach ($relations as $relation) {
            static::assertInstanceOf(Relation::class, $relation);
        }
    }
}
