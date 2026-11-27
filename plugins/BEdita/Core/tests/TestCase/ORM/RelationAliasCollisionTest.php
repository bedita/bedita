<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\ORM;

use BEdita\Core\Model\Table\LocationsTable;
use BEdita\Core\Model\Table\MediaTable;
use BEdita\Core\Model\Table\ObjectsTable;
use BEdita\Core\ORM\Association\RelatedTo;
use BEdita\Core\ORM\Locator\TableLocator;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Relations whose name matches a Core table or object type.
 *
 * `RelationsBehavior` names a `RelatedTo` association after the relation and sets `className` to the target
 * object type's table, so Cake registers its target as `BEdita/Core.<RelationAlias>`. These tests guard that
 * `TableLocator` keeps that key apart from `<RelationAlias>`: folding `BEdita/Core.X` into `X` would make the
 * relation target share the registry key with the real `X` table.
 */
#[CoversClass(TableLocator::class)]
#[CoversClass(RelatedTo::class)]
class RelationAliasCollisionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * Create a relation between the given object types and reset the table registry.
     *
     * @param string $name Relation name.
     * @param array<string> $left Left object type names.
     * @param array<string> $right Right object type names.
     * @return void
     */
    protected function addRelation(string $name, array $left, array $right): void
    {
        $Relations = $this->fetchTable('Relations');
        $relation = $Relations->saveOrFail($Relations->newEntity([
            'name' => $name,
            'inverse_name' => 'inverse_' . $name,
        ]));
        $ObjectTypes = $this->fetchTable('ObjectTypes');
        $RelationTypes = $this->fetchTable('RelationTypes');
        foreach (['left' => $left, 'right' => $right] as $side => $types) {
            foreach ($types as $type) {
                $RelationTypes->saveOrFail($RelationTypes->newEntity([
                    'relation_id' => $relation->id,
                    'object_type_id' => $ObjectTypes->get($type)->id,
                    'side' => $side,
                ]));
            }
        }
        TableRegistry::getTableLocator()->clear();
    }

    /**
     * Relation `locations` (documents → media), with `LocationsTable` loaded before the relation target.
     *
     * The relation target must be a `MediaTable` while `Locations` holds a `LocationsTable`. If both shared one
     * registry key, Cake's `Association::getTarget()` would throw a `DatabaseException`
     * ("You can't have an association of the same name with a different target className").
     *
     * @return void
     */
    public function testDifferentClassCoreTableFirst(): void
    {
        $this->addRelation('locations', ['documents'], ['media']);
        $locations = $this->fetchTable('Locations');

        $target = $this->fetchTable('Documents')->getAssociation('Locations')->getTarget();

        static::assertInstanceOf(MediaTable::class, $target);
        static::assertInstanceOf(LocationsTable::class, $this->fetchTable('Locations'));
        static::assertSame($locations, $this->fetchTable('Locations'));
    }

    /**
     * Relation `locations` (documents → media), with the relation target built first.
     *
     * With a shared registry key nothing would throw: the `MediaTable` target would take key `Locations`, and every
     * later `get('Locations')` would return a `MediaTable` instead of the `LocationsTable`.
     *
     * @return void
     */
    public function testDifferentClassRelationFirst(): void
    {
        $this->addRelation('locations', ['documents'], ['media']);

        $target = $this->fetchTable('Documents')->getAssociation('Locations')->getTarget();

        static::assertInstanceOf(MediaTable::class, $target);
        static::assertInstanceOf(LocationsTable::class, $this->fetchTable('Locations'));
    }

    /**
     * Relation `documents` (events → media): a relation named after an object type.
     *
     * Guards the same collision as above, with the `ObjectsTable` behind the `documents` type instead of a Core
     * table class.
     *
     * @return void
     */
    public function testRelationNamedAfterObjectType(): void
    {
        $this->addRelation('documents', ['events'], ['media']);
        $this->fetchTable('Documents');

        $target = $this->fetchTable('Events')->getAssociation('Documents')->getTarget();

        static::assertInstanceOf(MediaTable::class, $target);
        static::assertNotInstanceOf(MediaTable::class, $this->fetchTable('Documents'));
        static::assertInstanceOf(ObjectsTable::class, $this->fetchTable('Documents'));
    }

    /**
     * Relation `media` (documents → files): same table class, different object type.
     *
     * The target class matches, so nothing would throw, but `RelatedTo::getTarget()` calls `setupRelations()` with
     * the `files` type on its target. With a shared instance, the `Media` table would gain every relation of
     * `files` (here `files_only`) and expose it on `media` too.
     *
     * @return void
     */
    public function testSameClassRelationsLeak(): void
    {
        $this->addRelation('files_only', ['files'], ['documents']);
        $this->addRelation('media', ['documents'], ['files']);
        $media = $this->fetchTable('Media');
        static::assertFalse($media->hasAssociation('FilesOnly'));

        $this->fetchTable('Documents')->getAssociation('Media')->getTarget();

        static::assertFalse($this->fetchTable('Media')->hasAssociation('FilesOnly'));
    }
}
