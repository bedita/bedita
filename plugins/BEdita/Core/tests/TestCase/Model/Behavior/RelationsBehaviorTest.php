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

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Behavior\RelationsBehavior;
use BEdita\Core\Model\Entity\Relation;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Model\Behavior\RelationsBehavior} Test Case
 */
#[CoversClass(RelationsBehavior::class)]
class RelationsBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Locations',
    ];

    /**
     * Test initial setup
     *
     * @return void
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

        /** @var \BEdita\Core\Model\Behavior\ObjectTypeBehavior $docObjectTypeBehavior */
        $docObjectTypeBehavior = $Documents->getBehavior('ObjectType');
        /** @var \BEdita\Core\Model\Behavior\ObjectTypeBehavior $profileObjectTypeBehavior */
        $profileObjectTypeBehavior = $Profiles->getBehavior('ObjectType');
        static::assertSame(2, $docObjectTypeBehavior->objectType()?->id);
        static::assertSame(3, $profileObjectTypeBehavior->objectType()?->id);

        static::assertInstanceOf(BelongsToMany::class, $Documents->getAssociation('Test'));
        static::assertSame('BEdita/Core.Objects', $Documents->getAssociation('Test')->getClassName());
        static::assertInstanceOf(BelongsToMany::class, $Documents->getAssociation('InverseTest'));
        static::assertSame('BEdita/Core.Objects', $Documents->getAssociation('InverseTest')->getClassName());
        $testRelation = $Documents->getAssociation('Test')->getRelation();
        static::assertNotNull($testRelation);
        static::assertInstanceOf(Relation::class, $testRelation);
        static::assertSame('Test relation', $testRelation->label);
        static::assertNull($testRelation->params);

        static::assertInstanceOf(BelongsToMany::class, $Profiles->getAssociation('InverseTest'));
        static::assertSame('BEdita/Core.Objects', $Profiles->getAssociation('InverseTest')->getClassName());
        $inverseTestRelation = $Profiles->getAssociation('InverseTest')->getRelation();
        static::assertNotNull($inverseTestRelation);
        static::assertInstanceOf(Relation::class, $inverseTestRelation);
        static::assertSame('Test relation', $inverseTestRelation->label);
        static::assertNull($inverseTestRelation->params);

        static::assertInstanceOf(BelongsToMany::class, $Locations->getAssociation('InverseAnotherTest'));
        static::assertSame('BEdita/Core.Users', $Locations->getAssociation('InverseAnotherTest')->getClassName());
        $inverseAnotherTestRelation = $Locations->getAssociation('InverseAnotherTest')->getRelation();
        static::assertNotNull($inverseAnotherTestRelation);
        static::assertInstanceOf(Relation::class, $inverseAnotherTestRelation);
        static::assertSame('Another test relation', $inverseAnotherTestRelation->label);
        static::assertEquals((object)[
            'type' => 'object',
            'properties' => (object)[
                'name' => (object)[
                    'type' => 'string',
                ],
                'age' => (object)[
                    'type' => 'integer',
                    'minimum' => 0,
                ],
            ],
            'required' => ['name'],
        ], $inverseAnotherTestRelation->params);

        $before = count($Profiles->associations()->keys());
        /** @var \BEdita\Core\Model\Behavior\RelationsBehavior $relationsBehavior */
        $relationsBehavior = $Profiles->getBehavior('Relations');
        $relationsBehavior->setupRelations('profiles');
        $after = count($Profiles->associations()->keys());

        static::assertSame($before, $after);
    }

    /**
     * Test that no error occurs on an unknown object type, and no associations are set up.
     *
     * @return void
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
     * Data provider for `testHasRelation` method.
     *
     * @return array
     */
    public static function hasRelationProvider(): array
    {
        return [
            'hasRelation' => [
                [4],
                'Profiles',
                ['inverse_test_simple' => true],
            ],
            'hasRelationStringTrue' => [
                [4],
                'Profiles',
                ['inverse_test_simple' => 'true'],
            ],
            'hasRelationIntTrue' => [
                [4],
                'Profiles',
                ['inverse_test_simple' => 1],
            ],
            'hasRelationNumericStringTrue' => [
                [4],
                'Profiles',
                ['inverse_test_simple' => '1'],
            ],
            'hasRelation2' => [
                [2, 3],
                'Documents',
                ['test' => true],
            ],
            'hasRelationMulti' => [
                [2],
                'Documents',
                ['test' => true, 'test_simple' => true, 'test_defaults' => true],
            ],
            'notMatchRelation' => [
                [6, 7],
                'Documents',
                ['test' => false],
            ],
            'notMatchRelationStringFalse' => [
                [6, 7],
                'Documents',
                ['test' => 'false'],
            ],
            'notMatchRelationIntFalse' => [
                [6, 7],
                'Documents',
                ['test' => 0],
            ],
            'notMatchRelationNumericStringFalse' => [
                [6, 7],
                'Documents',
                ['test' => '0'],
            ],
            'matchAndNotMatchRelations' => [
                [7],
                'Documents',
                ['test' => false, 'inverse_test' => true],
            ],
            'invalidRelation' => [
                new BadFilterException('Invalid value for "relation" parameter using hasRelation finder'),
                'Documents',
                ['test' => 'gustavo'],
            ],
            'numericKey' => [
                new BadFilterException('Invalid value for "relation" parameter using hasRelation finder'),
                'Documents',
                [0 => true],
            ],
        ];
    }

    /**
     * Test `findHasRelation` method.
     *
     * @param array<int>|\BEdita\Core\Exception\BadFilterException $expected The expected list of IDs.
     * @param string $table The table to check for relations.
     * @param array<string, mixed> $relations The relations to check for.
     * @return void
     */
    #[DataProvider('hasRelationProvider')]
    public function testHasRelations(array|BadFilterException $expected, string $table, array $relations): void
    {
        if ($expected instanceof BadFilterException) {
            $this->expectExceptionMessage($expected->getMessage());
            $this->expectException(BadFilterException::class);
        }

        $Table = $this->fetchTable($table);
        if ($table === 'Documents') {
            // restore from trash documents 6, 7
            foreach ([6, 7] as $id) {
                $d = $Table->get($id);
                $d->set('deleted', false);
                $Table->saveOrFail($d);
            }
        }

        $result = $Table
            ->find('hasRelation', ...$relations)
            ->all()
            ->extract('id')
            ->toList();

        sort($result);
        sort($expected);

        static::assertSame($expected, $result);
    }

    /**
     * Test `findHasRelation` method when related entity is not available.
     *
     * @return void
     */
    public function testHasRelationWithRelatedNotAvailable(): void
    {
        $Table = $this->fetchTable('Documents');
        $result = $Table->find('hasRelation', test: true)
            ->all()
            ->extract('id')
            ->toList();

        sort($result);
        static::assertEquals([2, 3], $result);

        $ProfilesTable = $this->fetchTable('Profiles');
        $p = $ProfilesTable->get(4);
        $p->set('deleted', true);
        $ProfilesTable->saveOrFail($p);

        $result = $Table->find('hasRelation', test: true)
            ->all()
            ->extract('id')
            ->toList();

        sort($result);
        static::assertEquals([2], $result);
    }
}
