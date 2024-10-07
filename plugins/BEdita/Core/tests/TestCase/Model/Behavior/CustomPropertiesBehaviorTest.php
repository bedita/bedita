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
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Collection\CollectionInterface;
use Cake\Database\Driver\Mysql;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\CustomPropertiesBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\CustomPropertiesBehavior
 */
class CustomPropertiesBehaviorTest extends TestCase
{
    use TestFilesystemTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
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
     * @covers ::initialize()
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
    public function getAvailableProvider()
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
     * @covers ::getAvailable()
     * @covers ::objectType()
     * @dataProvider getAvailableProvider
     */
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
     * @covers ::getAvailable()
     * @covers ::objectType()
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
     * @covers ::getAvailable()
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
     * @covers ::getAvailable()
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
     * @covers ::getDefaultValues()
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
    public function beforeFindProvider()
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
     * @dataProvider beforeFindProvider()
     * @covers ::beforeFind()
     * @covers ::promoteProperties()
     * @covers ::setupCustomProps()
     * @covers ::isFieldSet()
     */
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
     * @covers ::beforeFind()
     */
    public function testBeforeFindFormatterPrepended()
    {
        $expected = [
            'files_property' => ['media-one' => null, 'media-two' => null, 'media-svg' => null, 'media-modern-art' => null, 'media-contemporary-art' => null],
            'media_property' => ['media-one' => true, 'media-two' => false, 'media-svg' => false, 'media-modern-art' => false, 'media-contemporary-art' => false],
            'count' => 5,
        ];

        $result = $this->getTableLocator()->get('Files')->find()
            ->formatResults(function (CollectionInterface $results): array {
                return [
                    'files_property' => $results->combine('uname', 'files_property')->toArray(),
                    'media_property' => $results->combine('uname', 'media_property')->toArray(),
                    'count' => $results->count(),
                ];
            })
            ->order('Files.id')
            ->toArray();

        static::assertSame($expected, $result);
    }

    /**
     * Test that no errors are triggered if results aren't neither entities nor arrays.
     *
     * @return void
     * @covers ::beforeFind()
     * @covers ::promoteProperties()
     * @covers ::setupCustomProps()
     * @covers ::isFieldSet()
     */
    public function testBeforeFindOtherType()
    {
        $result = TableRegistry::getTableLocator()->get('Objects')
            ->find('list')
            ->find('type', ['documents'])
            ->toArray();

        static::assertNotEmpty($result);
    }

    /**
     * Data provider for `testBeforeSave` test case.
     *
     * @return array
     */
    public function beforeSaveProvider()
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
     * @dataProvider beforeSaveProvider()
     * @covers ::beforeSave()
     * @covers ::demoteProperties()
     * @covers ::formatValue()
     */
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
     * @covers ::beforeSave()
     * @covers ::demoteProperties()
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
     * @covers ::demoteProperties()
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
     * @covers ::beforeFind()
     * @covers ::promoteProperties()
     * @covers ::setupCustomProps()
     * @covers ::isFieldSet()
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
    public function findCustomPropProvider(): array
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
                [14, 16, 17, 18],
                'Files',
                ['media_property' => false],
            ],
            'filter bool 0 as false' => [
                [14, 16, 17, 18],
                'Files',
                ['media_property' => 0],
            ],
            'filter bool "0" as false' => [
                [14, 16, 17, 18],
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
     * @covers ::findCustomProp()
     * @dataProvider findCustomPropProvider
     */
    public function testFindCustomProp($expected, string $tableName, array $options): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection->getDriver() instanceof Mysql) {
            $this->expectException(BadFilterException::class);
            $this->expectExceptionMessage('customProp finder isn\'t supported for datasource');
        } elseif ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $result = $this->getTableLocator()
            ->get($tableName)
            ->find('customProp', $options)
            ->find('list')
            ->orderAsc('id')
            ->toArray();

        sort($expected);

        static::assertEquals($expected, array_keys($result));
    }

    /**
     * Test custom prop finder for integer property.
     *
     * @return void
     * @covers ::findCustomProp()
     */
    public function testFindCustomPropInteger(): void
    {
        $connection = ConnectionManager::get('default');
        if (!$connection->getDriver() instanceof Mysql) {
            $this->expectException(BadFilterException::class);
            $this->expectExceptionMessage('customProp finder isn\'t supported for datasource');
        }

        $Profiles = $this->getTableLocator()->get('Profiles');
        $profile = $Profiles->find()->first();
        $profile->set('number_of_friends', 10);
        $Profiles->saveOrFail($profile);

        $result = $Profiles->find('customProp', ['number_of_friends' => 10])
            ->find('list')
            ->orderAsc('id')
            ->toArray();

        static::assertEquals([$profile->id], array_keys($result));

        $result = $Profiles->find('customProp', ['number_of_friends' => '10'])
            ->find('list')
            ->orderAsc('id')
            ->toArray();

        static::assertEquals([$profile->id], array_keys($result));
    }
}
