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
use BEdita\Core\Model\Behavior\CustomPropertiesBehavior;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Collection\CollectionInterface;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Model\Behavior\CustomPropertiesBehavior} Test Case
 */
#[CoversClass(CustomPropertiesBehavior::class)]
class CustomPropertiesBehaviorTest extends TestCase
{
    use TestFilesystemTrait;

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
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Streams',
        'plugin.BEdita/Core.History',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->filesystemSetup();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->filesystemRestore();
    }

    /**
     * Test initialization.
     *
     * @return void
     */
    public function testInitialize()
    {
        $table = TableRegistry::getTableLocator()->get('FakeObjects', [
            'className' => Table::class,
        ]);
        static::assertFalse($table->hasBehavior('BEdita/Core.ObjectType'));

        $table->addBehavior('BEdita/Core.CustomProperties');
        static::assertTrue($table->hasBehavior('ObjectType'));
    }

    /**
     * Data provider for testGetAvailable()
     *
     * @return array
     */
    public static function getAvailableProvider(): array
    {
        return [
            'locations' => [
                [],
                'Locations',
            ],
            'profiles' => [
                [
                    'another_surname',
                    'another_birthdate',
                    'number_of_friends',
                ],
                'Profiles',
            ],
            'users' => [
                [
                    'another_username',
                    'another_email',
                ],
                'Users',
            ],
            'media' => [
                [
                    'media_property',
                ],
                'Media',
            ],
            'files' => [
                [
                    'default_val_property',
                    'media_property',
                    'files_property',
                ],
                'Files',
            ],
        ];
    }

    /**
     * Test get available properties
     *
     * @param array $expected Expected result.
     * @param string $tableName Table name.
     * @return void
     */
    #[DataProvider('getAvailableProvider')]
    public function testGetAvailable(array $expected, $tableName)
    {
        $table = TableRegistry::getTableLocator()->get($tableName);
        $behavior = $table->behaviors()->get('CustomProperties');
        $result = $behavior->getAvailable();
        $result = array_keys($result);
        sort($result);
        sort($expected);
        static::assertEquals($expected, $result);

        // cover use of internal `available` array
        $result = $behavior->getAvailable();
        $result = array_keys($result);
        sort($result);
        static::assertEquals($expected, $result);
    }

    /**
     * Test get available properties for related object.
     *
     * @return void
     */
    public function testGetAvailableRelatedObject(): void
    {
        $table = TableRegistry::getTableLocator()->get('Profiles')
            ->getAssociation('InverseTest')->getTarget();

        static::assertEquals('InverseTest', $table->getAlias());

        $behavior = $table->behaviors()->get('CustomProperties');
        $result = $behavior->getAvailable();

        $expected = ['another_title', 'another_description']; // documents custom props
        $result = array_keys($result);
        sort($result);
        sort($expected);
        static::assertEquals($expected, $result);
    }

    /**
     * Test get available when no object type is found
     *
     * @return void
     */
    public function testGetAvailableTypeNotFound()
    {
        // test try/catch failure on `objectType` load
        $Relations = TableRegistry::getTableLocator()->get('Relations');
        $Relations->addBehavior('BEdita/Core.CustomProperties', ['field' => 'description']);
        $rel = $Relations->get(1);
        $result = $rel->toArray();
        static::assertNotEmpty($result);
    }

    /**
     * Test empty custom properties
     *
     * @return void
     */
    public function testEmpty()
    {
        $table = TableRegistry::getTableLocator()->get('Locations');
        $result = $table->behaviors()->get('CustomProperties')->getDefaultValues();
        static::assertEmpty($result);
    }

    /**
     * Test get available properties
     *
     * @return void
     */
    public function testDefaultValues()
    {
        $expected = [
            'media_property' => null,
            'files_property' => null,
            'default_val_property' => null,
        ];
        $user = TableRegistry::getTableLocator()->get('Files');
        $result = $user->behaviors()->get('CustomProperties')->getDefaultValues();
        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testBeforeFind` test case.
     *
     * @return array
     */
    public static function beforeFindProvider(): array
    {
        return [
            'simple' => [
                ['media_property', 'files_property'],
                10,
                'Files',
            ],
            'no hydration' => [
                ['media_property', 'files_property'],
                10,
                'Files',
                false,
            ],
            'empty' => [
                [],
                9,
                'Events',
            ],
        ];
    }

    /**
     * Test setting of priority before entity is saved.
     *
     * @param string[] $expectedProperties List of expected properties.
     * @param int $id Entity ID.
     * @param string $table Table.
     * @param bool $hydrate Should hydration be enabled?
     * @return void
     */
    #[DataProvider('beforeFindProvider')]
    public function testBeforeFind(array $expectedProperties, $id, $table, $hydrate = true)
    {
        $result = TableRegistry::getTableLocator()->get($table)->find()
            ->where(compact('id'))
            ->enableHydration($hydrate)
            ->first();
        if ($hydrate) {
            static::assertFalse($result->isDirty());
            $result = $result->toArray();
        }

        static::assertArrayNotHasKey('custom_props', $result);
        foreach ($expectedProperties as $property) {
            static::assertArrayHasKey($property, $result);
        }
    }

    /**
     * Test that formatter is prepended to other formatters that may be attached to the Query object.
     *
     * @return void
     */
    public function testBeforeFindFormatterPrepended()
    {
        $expected = [
            'files_property' => [
                'media-one' => null,
                'media-two' => null,
                'media-svg' => null,
                'media-modern-art' => null,
                'media-contemporary-art' => null,
                'media-funny-video-of-gustavo' => null,
            ],
            'media_property' => [
                'media-one' => true,
                'media-two' => false,
                'media-svg' => false,
                'media-modern-art' => false,
                'media-contemporary-art' => false,
                'media-funny-video-of-gustavo' => false,
            ],
            'count' => 6,
        ];

        $result = $this->getTableLocator()->get('Files')->find()
            ->formatResults(function (CollectionInterface $results): array {
                return [
                    'files_property' => $results->combine('uname', 'files_property')->toArray(),
                    'media_property' => $results->combine('uname', 'media_property')->toArray(),
                    'count' => $results->count(),
                ];
            })
            ->orderBy('Files.id')
            ->toArray();

        static::assertSame($expected, $result);
    }

    /**
     * Test that no errors are triggered if results aren't neither entities nor arrays.
     *
     * @return void
     */
    public function testBeforeFindOtherType()
    {
        $result = TableRegistry::getTableLocator()->get('Objects')
            ->find('list')
            ->find('type', value: ['documents'])
            ->toArray();

        static::assertNotEmpty($result);
    }

    /**
     * Data provider for `testBeforeSave` test case.
     *
     * @return array
     */
    public static function beforeSaveProvider(): array
    {
        return [
            'simple' => [
                [
                    'default_val_property' => null,
                    'media_property' => false,
                    'files_property' => null,
                ],
                [
                    'media_property' => false,
                ],
                10,
                'Files',
            ],
            'overwrite' => [
                [
                    'default_val_property' => null,
                    'media_property' => true,
                    'files_property' => ['gustavo' => 'supporto'],
                ],
                [
                    'files_property' => ['gustavo' => 'supporto'],
                ],
                10,
                'Files',
            ],
            'empty' => [
                [
                    'media_property' => ['Boolean expected, null received'],
                ],
                [
                    'media_property' => null,
                    'files_property' => '',
                ],
                10,
                'Files',
            ],
            'disabledProperty' => [
                [
                    'default_val_property' => null,
                    'media_property' => false,
                    'files_property' => null,
                ],
                [
                    'media_property' => 0,
                    'disabled_property' => 'do not write it!',
                ],
                10,
                'Files',
            ],
            'email' => [
                [
                    'another_email' => null,
                    'another_username' => 'another',
                ],
                [
                    'another_email' => '',
                    'another_username' => 'another',
                ],
                5,
                'Users',
            ],
        ];
    }

    /**
     * Test correct save of custom properties.
     *
     * @param array $expected Expected result.
     * @param array $data Data.
     * @param int $id Entity ID.
     * @param string $table Table.
     * @return void
     */
    #[DataProvider('beforeSaveProvider')]
    public function testBeforeSave(array $expected, array $data, $id, $table): void
    {
        $table = TableRegistry::getTableLocator()->get($table);
        $entity = $table->get($id);

        $table->patchEntity($entity, $data);
        $success = $table->save($entity);
        if ($success === false) {
            static::assertSame($expected, $entity->getErrors());

            return;
        }

        $result = $entity->get('custom_props');

        ksort($expected);
        ksort($result);

        static::assertSame($expected, $result);
    }

    /**
     * Test validation error on custom properties.
     *
     * @return void
     */
    public function testValidationFail(): void
    {
        $table = TableRegistry::getTableLocator()->get('Documents');
        $entity = $table->get(2);

        $table->patchEntity($entity, ['another_title' => true]);
        $result = $table->save($entity);

        static::assertFalse($result);
        static::assertNotEmpty($entity->getErrors());
    }

    /**
     * Test validation error on not nullable property.
     *
     * @return void
     */
    public function testValidationNewFail(): void
    {
        $table = TableRegistry::getTableLocator()->get('Files');
        $entity = $table->newEntity(['title' => 'New file']);
        $result = $table->save($entity);

        static::assertFalse($result);
        static::assertNotEmpty($entity->getErrors());
        $expected = [
            'media_property' => ['Boolean expected, null received'],
        ];
        static::assertEquals($expected, $entity->getErrors());
    }

    /**
     * Test that custom properties are not dirty getting object.
     *
     * @return void
     */
    public function testCustomPropertyNotDirty(): void
    {
        $user = TableRegistry::getTableLocator()->get('Users')->get(5);
        static::assertFalse($user->isDirty('another_username'));
        static::assertFalse($user->isDirty('another_email'));

        $user->set('another_username', 'blablabla');
        $user->set('another_email', 'xyz@example.com');
        static::assertTrue($user->isDirty('another_username'));
        static::assertTrue($user->isDirty('another_email'));
    }

    /**
     * Data provider for testFindCustomProp()
     *
     * @return array
     */
    public static function findCustomPropProvider(): array
    {
        return [
            'empty options' => [
                new BadFilterException('Invalid data'),
                'Documents',
                [],
            ],
            'invalid custom prop' => [
                new BadFilterException('Invalid data'),
                'Documents',
                ['yeppa' => 12],
            ],
            'filter string' => [
                [5],
                'Users',
                ['another_username' => 'synapse'],
            ],
            'filter operator string' => [
                [],
                'Users',
                ['another_username' => ['ne' => 'synapse']],
            ],
            'filter operator null' => [
                [1, 20],
                'Users',
                ['another_username' => null],
            ],
            'filter integer' => [
                [4],
                'Profiles',
                ['number_of_friends' => 42],
            ],
            'filter integer operator gt' => [
                [4],
                'Profiles',
                ['number_of_friends' => ['gt' => 17]],
            ],
            'filter integer operator lt' => [
                [],
                'Profiles',
                ['number_of_friends' => ['lt' => 10]],
            ],
            'filter integer operator in' => [
                [4],
                'Profiles',
                ['number_of_friends' => ['in' => [12, 24, 42]]],
            ],
            'filter integer with array' => [
                [4],
                'Profiles',
                ['number_of_friends' => [12, 24, 42]],
            ],
            'filter integer with array as string' => [
                [4],
                'Profiles',
                ['number_of_friends' => '12,24,42'],
            ],
            'filter bool true' => [
                [10],
                'Files',
                ['media_property' => true],
            ],
            'filter bool 1 as true' => [
                [10],
                'Files',
                ['media_property' => 1],
            ],
            'filter bool "1" as true' => [
                [10],
                'Files',
                ['media_property' => '1'],
            ],
            'filter bool false' => [
                [14, 16, 17, 18, 19],
                'Files',
                ['media_property' => false],
            ],
            'filter bool 0 as false' => [
                [14, 16, 17, 18, 19],
                'Files',
                ['media_property' => 0],
            ],
            'filter bool "0" as false' => [
                [14, 16, 17, 18, 19],
                'Files',
                ['media_property' => '0'],
            ],
        ];
    }

    /**
     * Test for custom prop finder.
     *
     * @param mixed $expected The expected result
     * @param string $tableName The table name
     * @param array $options Options for finder
     * @return void
     */
    #[DataProvider('findCustomPropProvider')]
    public function testFindCustomProp($expected, string $tableName, array $options): void
    {
        $connection = ConnectionManager::get('default');
        $driver = $connection->getDriver();
        if (!($driver instanceof Mysql) && !($driver instanceof Postgres)) {
            $this->expectException(BadFilterException::class);
            $this->expectExceptionMessage('customProp finder isn\'t supported for this datasource');
        } elseif ($expected instanceof Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $this->prepareCustomProps($tableName);

        $result = $this->getTableLocator()
            ->get($tableName)
            ->find('customProp', ...$options)
            ->find('list')
            ->orderByAsc('id')
            ->toArray();

        sort($expected);

        static::assertEquals($expected, array_keys($result));
    }

    /**
     * Prepare custom properties for testing.
     *
     * @param string $tableName Table name.
     * @return void
     */
    protected function prepareCustomProps(string $tableName): void
    {
        $table = $this->getTableLocator()->get($tableName);
        $entity = null;
        if ($tableName === 'Profiles') {
            $entity = $table->get(4);
            $entity->set('number_of_friends', 42);
        }
        if (empty($entity)) {
            return;
        }
        $table->saveOrFail($entity);
    }
}
