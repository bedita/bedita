<?php
declare(strict_types=1);

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

use BEdita\Core\Exception\LockedResourceException;
use BEdita\Core\Model\Table\TreesTable;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

/**
 * {@see \BEdita\Core\Model\Table\TreesTable} Test Case
 */
#[CoversClass(TreesTable::class)]
class TreesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\TreesTable
     */
    public $Trees;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Trees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('ChildrenParams', []);
        $this->Trees = TableRegistry::getTableLocator()->get('Trees');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Trees);
        Configure::delete('ChildrenParams');

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        static::assertInstanceOf(BelongsTo::class, $this->Trees->Objects);
        static::assertInstanceOf(BelongsTo::class, $this->Trees->ParentObjects);
        static::assertInstanceOf(BelongsTo::class, $this->Trees->RootObjects);
        static::assertInstanceOf(BelongsTo::class, $this->Trees->ParentNode);
        static::assertInstanceOf(HasMany::class, $this->Trees->ChildNodes);
        static::assertInstanceOf(TreeBehavior::class, $this->Trees->behaviors()->get('Tree'));
    }

    /**
     * Data provider for `testIsParentValid()`
     *
     * @return array
     */
    public static function isParentValidProvider(): array
    {
        return [
            'null, no object ID' => [
                false,
                null,
            ],
            'null, folder' => [
                true,
                null,
                12,
            ],
            'null, not a folder' => [
                false,
                null,
                4,
            ],
            'folder' => [
                true,
                12,
            ],
            'not a folder' => [
                false,
                4,
            ],
        ];
    }

    /**
     * Test for `isParentValid()`
     *
     * @param bool $expected The expected result
     * @param int|null $parentId The parent id
     * @param int|null $objectId The object id
     * @return void
     */
    #[DataProvider('isParentValidProvider')]
    public function testIsParentValid($expected, $parentId, $objectId = null)
    {
        $entity = $this->Trees->newEmptyEntity();
        if ($objectId !== null) {
            $entity->object_id = $objectId;
        }
        $entity->parent_id = $parentId;
        static::assertEquals($expected, $this->Trees->isParentValid($entity));
    }

    /**
     * Data provider for `testIsPositionUnique()`
     *
     * @return array
     */
    public static function isPositionUniqueProvider(): array
    {
        return [
            'folder, not unique' => [
                false,
                12,
                null,
            ],
            'folder, unique' => [
                true,
                13,
                null,
            ],
            'not a folder, appears twice inside parent' => [
                false,
                4,
                12,
            ],
            'not a folder, appears once inside parent' => [
                true,
                4,
                11,
            ],
        ];
    }

    /**
     * Test for `isFolderPositionUnique()`
     *
     * @param bool $expected Expected result.
     * @param int|null $objectId Object ID.
     * @param int|null $parentId Parent ID.
     * @return void
     */
    #[DataProvider('isPositionUniqueProvider')]
    public function testIsPositionUnique($expected, $objectId, $parentId)
    {
        $this->Trees->deleteAll(['object_id' => 13]);

        $entity = $this->Trees->newEmptyEntity();
        $entity->object_id = $objectId;
        $entity->parent_id = $parentId;
        static::assertEquals($expected, $this->Trees->isPositionUnique($entity));
    }

    /**
     * Data provider for `testSlugPopulation()`.
     *
     * @return array[]
     */
    public static function slugPopulationProvider(): array
    {
        return [
            'no slug' => [
                'title-two',
                3,
                '',
            ],
            'no slug or title' => [
                'title-two',
                3,
                '',
            ],
            'no slug, title special characters' => [
                'title-two',
                3,
                '',
            ],
            'slug correct' => [
                'slug-doc',
                2,
                'slug-doc',
            ],
            'update no slug' => [
                'title-one',
                2,
                '',
            ],
            'update no slug or title' => [
                'title-one',
                2,
                '',
            ],
            'update slug special characters' => [
                'asd-lol-rofl',
                2,
                'asd@lol.rofl',
            ],
            'update slug correct' => [
                'slug-doc',
                2,
                'slug-doc',
            ],
        ];
    }

    /**
     * Test for slug generation in `beforeRules()`.
     *
     * @param string $expected Expected slug.
     * @param int $objectId Object ID.
     * @param string|null $slug Slug set on tree node.
     * @return void
     */
    #[DataProvider('slugPopulationProvider')]
    public function testSlugPopulation(string $expected, int $objectId, ?string $slug = null): void
    {
        $node = $this->Trees->newEntity([
            'object_id' => $objectId,
            'slug' => $slug,
        ]);
        $this->Trees->beforeRules(new Event('Model.beforeRules'), $node);
        static::assertEquals($expected, $node->get('slug'));
    }

    /**
     * Data provider for `testChangeRoot()`
     *
     * @return array
     */
    public static function changeRootProvider(): array
    {
        return [
            'becomeRoot' => [
                12,
                null,
            ],
            'changeRoot' => [
                13,
                13,
            ],
        ];
    }

    /**
     * Test that moving a node under another `root_id`
     * all children will be migrated to the same `root_id`
     *
     * @param int $rootExpected Expected root ID.
     * @param int|null $parentId Parent ID.
     * @return void
     */
    #[DataProvider('changeRootProvider')]
    public function testChangeRoot($rootExpected, $parentId)
    {
        $node = $this->Trees->get(2);
        static::assertEquals(11, $node->root_id);
        $children = $this->Trees->find('children', for: 2)->all()->toList();

        $node->parent_id = $parentId;
        static::assertTrue((bool)$this->Trees->save($node));

        $node = $this->Trees->get(2);
        $actualChildren = $this->Trees->find('children', for: 2)->all()->toList();

        static::assertEquals($rootExpected, $node->root_id);
        static::assertCount(count($children), $actualChildren);
        foreach ($actualChildren as $child) {
            static::assertEquals($rootExpected, $child->root_id);
        }
    }

    /**
     * Test `afterSave` on new item
     *
     * @return void
     */
    public function testAfterSaveNew()
    {
        $entity = $this->Trees->newEntity(
            [
                'object_id' => 2,
                'parent_id' => 12,
                'root_id' => 11,
                'parent_node_id' => 2,
            ],
        );
        static::assertTrue((bool)$this->Trees->save($entity));
    }

    /**
     * Test that moving a parent as child fails.
     *
     * @return void
     */
    public function testMoveParentAsChild()
    {
        $this->expectException(RuntimeException::class);
        // create new Folder
        LoggedUser::setUserAdmin();
        $Folders = TableRegistry::getTableLocator()->get('Folders');
        $entity = $Folders->newEntity(['title' => 'subsub folder']);
        $entity->type = 'folders';
        $entity->parent = $Folders->get(12);

        $Folders->save($entity);

        $parentNode = $this->Trees
            ->find()
            ->where(['object_id' => $entity->parent->id])
            ->first();

        $parentNode->set('parent_id', $entity->id);

        $this->Trees->save($parentNode);
    }

    /**
     * Data provider for `testDeleteOrphaned` test case.
     *
     * @return array
     */
    public static function deleteOrphanedProvider(): array
    {
        return [
            'not a folder' => [
                true,
                2,
            ],
            'not primary' => [
                true,
                12,
                false,
            ],
            'primary' => [
                new LockedResourceException('This operation would leave an orphaned folder'),
                12,
                true,
            ],
        ];
    }

    /**
     * Test that no folder is ever left out of the tree.
     *
     * @param bool|\Exception $expected Expected result.
     * @param int $objectId Object ID.
     * @param bool $primary Is this a "primary" delete operation?
     * @return void
     */
    #[DataProvider('deleteOrphanedProvider')]
    public function testDeleteOrphaned($expected, $objectId, $primary = true)
    {
        if ($expected instanceof Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $node = $this->Trees->find()
            ->where(['object_id' => $objectId])
            ->firstOrFail();

        $result = (bool)$this->Trees->delete($node, ['_primary' => $primary]);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testSetPosition` test case.
     *
     * @return array
     */
    public static function setPositionProvider(): array
    {
        return [
            'first' => [
                1,
                2,
                'first',
            ],
            'last' => [
                2,
                12,
                'last',
            ],
            'moveRootAsLastRoot' => [
                2,
                11,
                'last',
            ],
            'invalid' => [
                new BadRequestException('Invalid position'),
                11,
                'gustavo',
            ],
        ];
    }

    /**
     * Test that a children's position is updated.
     *
     * @param int|\Exception $expected Expected final position.
     * @param int $objectId Object ID.
     * @param int|string $position Position.
     * @return void
     */
    #[DataProvider('setPositionProvider')]
    public function testSetPosition($expected, $objectId, $position)
    {
        if ($expected instanceof Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $node = $this->Trees->find()
            ->where(['object_id' => $objectId])
            ->firstOrFail();

        $node->set('position', $position);
        $this->Trees->save($node);

        /** @var \BEdita\Core\Model\Behavior\TreeBehavior $treeBehavior */
        $treeBehavior = $this->Trees->getBehavior('Tree');
        $currentPosition = $treeBehavior->getCurrentPosition($node);

        static::assertSame($expected, $currentPosition);
    }

    /**
     * Test set canonical `true`
     *
     * @return void
     */
    public function testSetCanonical()
    {
        $entity = $this->Trees->newEntity(
            [
                'object_id' => 2,
                'parent_id' => 12,
                'root_id' => 11,
                'parent_node_id' => 2,
            ],
        );
        $entity = $this->Trees->saveOrFail($entity);

        $entity = $this->Trees->get($entity->get('id'));
        static::assertFalse($entity->get('canonical'));

        // get other record for the same object
        $other = $this->Trees->get(3);
        static::assertTrue($other->get('canonical'));

        // change canonical in subfolder
        $entity->set('canonical', true);
        $entity = $this->Trees->save($entity);
        static::assertTrue($entity->get('canonical'));
        // other record must have canonical false now
        $other = $this->Trees->get(3);
        static::assertFalse($other->get('canonical'));
    }

    /**
     * Data provider for `testFindPathNodes` test case.
     *
     * @return array
     */
    public static function findPathNodesProvider(): array
    {
        return [
            'first' => [
                [11, 12, 4],
                4,
            ],
            'invalid' => [
                new RecordNotFoundException('Record not found in table `trees`'),
                3,
            ],
        ];
    }

    /**
     * Test `findPathNodes` method.
     *
     * @param array|\Exception $expected Expected array path or exception.
     * @param int $objectId The object id.
     * @return void
     */
    #[DataProvider('findPathNodesProvider')]
    public function testFindPathNodes($expected, int $objectId): void
    {
        if ($expected instanceof Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $path = $this->Trees->find('pathNodes', objectId: $objectId)
            ->find('list', keyField: 'id', valueField: 'object_id')
            ->toArray();

        static::assertSame($expected, array_values($path));
    }

    /**
     * Data provider for `testJsonSchema` test case.
     *
     * @return array
     */
    public static function jsonSchemaProvider(): array
    {
        $schema = [
            'type' => 'object',
            'required' => [
                'item',
                'class',
                'contained',
                'location',
            ],
            'properties' => [
                'item' => ['type' => 'string'],
                'class' => [
                    'type' => 'string',
                    'enum' => ['safe', 'euclid', 'keter'],
                ],
                'contained' => ['type' => 'boolean'],
                'location' => ['type' => 'string'],
                'description' => ['anyOf' => [['type' => 'null'], ['type' => 'string']]],
            ],
        ];

        return [
            'valid' => [
                true,
                [
                    'item' => 'SCP-5091',
                    'class' => 'safe',
                    'contained' => true,
                    'location' => 'Site-10',
                    'description' => 'SCP-5091 is a sapient human skeleton approximately 1.8 meters tall and weighing 2.5 kg when not encompassed by skin and flesh.',
                ],
                $schema,
            ],
            'empty schema' => [
                true,
                [
                    'item' => 'SCP-3404',
                    'class' => 'keter',
                ],
                null,
            ],
            'empty value' => [
                true,
                null,
                ['required' => []] + $schema, // remove required properties
            ],
            'missing required' => [
                'Required property missing: location',
                [
                    'item' => 'SCP-3759',
                    'class' => 'euclid',
                    'contained' => true,
                ],
                $schema,
            ],
            'invalid value' => [
                'Enum failed',
                [
                    'item' => 'SCP-7292',
                    'class' => 'thaumiel',
                    'contained' => false,
                    'location' => 'global',
                ],
                $schema,
            ],
            'nullable property' => [
                true,
                [
                    'item' => 'SCP-239',
                    'class' => 'keter',
                    'contained' => true,
                    'location' => 'Site-17',
                    'description' => null,
                ],
                $schema,
            ],
            'wrong type' => [
                'Boolean expected',
                [
                    'item' => 'SCP-6349',
                    'class' => 'euclid',
                    'contained' => 'not really',
                    'location' => 'Venus',
                ],
                $schema,
            ],
        ];
    }

    /**
     * Test JSON schema validator.
     *
     * @param true|string $expected Expected result.
     * @param array|null $value Value being validated.
     * @param array|null $schema JSON schema.
     * @return void
     */
    #[DataProvider('jsonSchemaProvider')]
    public function testJsonSchema($expected, ?array $value, ?array $schema): void
    {
        Configure::write('ChildrenParams', $schema);
        $result = TreesTable::jsonSchema($value);

        if ($expected === true) {
            static::assertTrue($result);
        } else {
            static::assertStringContainsString($expected, $result);
        }
    }

    /**
     * Test `getPathInfo()` with a valid multi-level path.
     *
     * @return void
     */
    public function testGetPathInfoValid(): void
    {
        $result = $this->Trees->getPathInfo('root-folder-11/sub-folder-12');

        static::assertIsArray($result, 'Result must be an array');
        static::assertNotEmpty($result, 'Result should not be empty');
        static::assertEquals([11, 12], $result['ids']);
        static::assertEquals(['root-folder-11', 'sub-folder-12'], $result['slugs']);
        static::assertArrayHasKey('types', $result, 'Array should have an `types` property');
    }

    /**
     * Test `getPathInfo()` with a deeper valid path.
     *
     * @return void
     */
    public function testGetPathInfoDeepValid(): void
    {
        $result = $this->Trees->getPathInfo('root-folder-11/sub-folder-12/gustavo-supporto-profile-4');

        static::assertIsArray($result, 'result must be an array');
        static::assertNotEmpty($result, 'result should not be empty');
        static::assertEquals([11, 12, 4], $result['ids']);
        static::assertEquals(
            ['root-folder-11', 'sub-folder-12', 'gustavo-supporto-profile-4'],
            $result['slugs'],
        );
        static::assertArrayHasKey('types', $result, 'array should have an `types` property');
    }

    /**
     * Test `getPathInfo()` with a valid alternative root.
     *
     * @return void
     */
    public function testGetPathInfoAlternativeRoot(): void
    {
        $result = $this->Trees->getPathInfo('another-root-folder-13');

        static::assertIsArray($result, 'result must be an array');
        static::assertNotEmpty($result, 'result should not be empty');
        static::assertEquals([13], $result['ids']);
        static::assertEquals(['another-root-folder-13'], $result['slugs']);
        static::assertArrayHasKey('types', $result, 'array should have an `types` property');
    }

    /**
     * Test `getPathInfo()` with an invalid path.
     *
     * @return void
     */
    public function testGetPathInfoInvalid(): void
    {
        $this->expectException(NotFoundException::class);
        $this->Trees->getPathInfo('this-is-not/a-valid-path');
    }

    /**
     * Test `getPathInfo()` with a partially valid but non-existent full path.
     *
     * @return void
     */
    public function testGetPathInfoPartialInvalid(): void
    {
        $this->expectException(NotFoundException::class);
        $this->Trees->getPathInfo('root-folder-11/waldo');
    }

    /**
     * Test `getPathInfo()` with an empty path.
     *
     * @return void
     */
    public function testGetPathInfoEmpty(): void
    {
        $this->expectException(NotFoundException::class);
        $this->Trees->getPathInfo('');
    }
}
