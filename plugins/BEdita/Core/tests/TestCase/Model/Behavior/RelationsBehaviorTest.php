<?php
declare(strict_types=1);

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
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Locations',
    ];

    /**
     * Test initial setup
     *
     * @return void
     * @covers ::initialize()
     * @covers ::objectType()
     * @covers ::setupRelations()
     * @covers ::relatedTo()
     */
    public function testInitialization()
    {
        TableRegistry::getTableLocator()->clear();

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
}
