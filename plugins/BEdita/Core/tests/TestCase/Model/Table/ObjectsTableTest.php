<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
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
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Utility\Database;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\FrozenTime;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Table\ObjectsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\ObjectsTable
 */
class ObjectsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ObjectsTable
     */
    public $Objects;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.DateRanges',
        'plugin.BEdita/Core.Translations',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.Tags',
        'plugin.BEdita/Core.ObjectTags',
        'plugin.BEdita/Core.History',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Objects = TableRegistry::getTableLocator()->get('Objects');
        LoggedUser::setUserAdmin();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Objects);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     * @coversNothing
     */
    public function testInitialization()
    {
        $this->Objects->initialize([]);
        $this->assertEquals('objects', $this->Objects->getTable());
        $this->assertEquals('id', $this->Objects->getPrimaryKey());
        $this->assertEquals('title', $this->Objects->getDisplayField());

        $this->assertInstanceOf('\Cake\ORM\Association\BelongsTo', $this->Objects->ObjectTypes);
        $this->assertInstanceOf('\Cake\ORM\Behavior\TimestampBehavior', $this->Objects->behaviors()->get('Timestamp'));
    }

    /**
     * Data provider for `testSave` test case.
     *
     * @return array
     */
    public function saveProvider()
    {
        return [
            'valid' => [
                false,
                [
                    'title' => 'doc title',
                    'description' => 'doc description',
                ],
            ],
            'notUniqueUname' => [
                true,
                [
                    'title' => 'another doc title',
                    'description' => 'another doc description',
                    'uname' => 'title-one',
                ],
            ],
        ];
    }

    /**
     * Test entity save.
     *
     * @param bool $changed
     * @param array $data
     * @return void
     * @dataProvider saveProvider
     * @coversNothing
     */
    public function testSave(bool $changed, array $data)
    {
        $entity = $this->Objects->newEntity($data);
        $entity->type = 'documents';
        $success = (bool)$this->Objects->save($entity);

        $this->assertTrue($success);

        if ($changed) {
            $this->assertNotEquals($data['uname'], $entity->uname);
        } elseif (isset($data['uname'])) {
            $this->assertEquals($data['uname'], $entity->uname);
        }
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'title' => 'title three',
                    'description' => 'another description',
                    'uname' => 'title-three',
                ],
            ],
            'titleOnly' => [
                true,
                [
                    'title' => 'title only',
                ],
            ],
            'emptyForm' => [
                true,
                [
                    'title' => 'just another title',
                    'uname' => null,
                    'status' => null,
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $object = $this->Objects->newEntity($data);
        $object->type = 'documents';

        $error = (bool)$object->getErrors();
        $this->assertEquals($expected, !$error);
    }

    /**
     * Data provider for `testFindType` test case.
     *
     * @return array
     */
    public function findTypeProvider()
    {
        return [
            'documents' => [
                [
                    2 => 'title one',
                    3 => 'title two',
                    6 => 'title one deleted',
                    7 => 'title two deleted',
                    15 => null,
                ],
                [2],
            ],
            'multiple' => [
                [
                    2 => 'title one',
                    3 => 'title two',
                    4 => 'Gustavo Supporto profile',
                    6 => 'title one deleted',
                    7 => 'title two deleted',
                    15 => null,
                ],
                ['document', 'profiles'],
            ],
            'exclude' => [
                [
                    1 => 'Mr. First User',
                    4 => 'Gustavo Supporto profile',
                    5 => 'Miss Second User',
                    8 => 'The Two Towers',
                    9 => 'first event',
                    10 => 'first media',
                    11 => 'Root Folder',
                    12 => 'Sub Folder',
                    13 => 'Another Root Folder',
                    14 => 'second media',
                    16 => 'svg media',
                ],
                ['ne' => 'documents'],
            ],
            'missing' => [
                new RecordNotFoundException('Record not found in table "object_types"'),
                ['document', 'profiles', 0],
            ],
            'abstract' => [
                [
                    1 => 'Mr. First User',
                    2 => 'title one',
                    3 => 'title two',
                    4 => 'Gustavo Supporto profile',
                    5 => 'Miss Second User',
                    6 => 'title one deleted',
                    7 => 'title two deleted',
                    8 => 'The Two Towers',
                    9 => 'first event',
                    10 => 'first media',
                    11 => 'Root Folder',
                    12 => 'Sub Folder',
                    13 => 'Another Root Folder',
                    14 => 'second media',
                    15 => null,
                    16 => 'svg media',
                ],
                ['objects'],
            ],
            'polluted array' => [
                [
                    4 => 'Gustavo Supporto profile',
                ],
                [
                    'profiles',
                    'banana' => 33,
                ],
            ],
        ];
    }

    /**
     * Test object types finder.
     *
     * @param array|\Exception $expected Expected results.
     * @param array $types Array of object types to filter for.
     * @return void
     * @dataProvider findTypeProvider
     * @covers ::findType()
     */
    public function testFindType($expected, array $types)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $result = $this->Objects->find('list')->find('type', $types)->toArray();

        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for `testFindDateRanges` test case.
     *
     * @return array
     */
    public function findDateRangesProvider()
    {
        return [
            'simple' => [
                [9],
                [
                    'start_date' => ['gt' => '2017-01-01'],
                ],
            ],
            'sub1' => [
                [],
                [
                    'date_ranges_min_start_date' => true,
                    'from_date' => '2019-01-01',
                ],
            ],
            'sub2' => [
                [9],
                [
                    'date_ranges_max_start_date' => true,
                ],
            ],
        ];
    }

    /**
     * Test object date ranges finder.
     * {@see \BEdita\Core\Model\Table\DateRangesTable} for a more detailed test case
     *
     * @param array $expected Expected results.
     * @param array $options Finder options.
     * @return void
     * @dataProvider findDateRangesProvider
     * @covers ::findDateRanges()
     * @covers ::dateRangesSubQueryJoin()
     */
    public function testFindDateRanges(array $expected, array $options)
    {
        $result = $this->Objects->find('dateRanges', $options)->toArray();
        $this->assertEquals($expected, Hash::extract($result, '{n}.id'));
    }

    /**
     * Test save of date ranges using 'replace' save strategy ({@see https://github.com/bedita/bedita/issues/1152}).
     *
     * @return void
     * @coversNothing
     */
    public function testSaveDateRanges()
    {
        $object = $this->Objects->newEntity([]);
        $object->type = 'events';

        $data = [
            'date_ranges' => [
                [
                    'start_date' => '1992-08-17',
                    'params' => [
                        'k' => 'v',
                    ],
                ],
            ],
        ];
        $object = $this->Objects->patchEntity($object, $data);
        $object = $this->Objects->save($object);
        if (!$object) {
            static::fail('Unable to save object');
        }
        $object = $this->Objects->get($object->id, ['contain' => ['DateRanges']]);
        static::assertCount(1, $object->date_ranges);
        static::assertEquals(['k' => 'v'], $object->date_ranges[0]['params']);

        $data['date_ranges'][0]['start_date'] = date('Y-m-d');
        $object = $this->Objects->patchEntity($object, $data);
        $object = $this->Objects->save($object);
        if (!$object) {
            static::fail('Unable to save object');
        }

        $object = $this->Objects->get($object->id, ['contain' => ['DateRanges']]);

        static::assertCount(1, $object->date_ranges);
        static::assertSame(1, $this->Objects->DateRanges->find()->where(['object_id' => $object->id])->count());
        static::assertSame(0, $this->Objects->DateRanges->find()->where(['object_id IS' => null])->count());
    }

    /**
     * Test finder for my objects.
     *
     * @return void
     * @covers ::findMine()
     */
    public function testFindMine()
    {
        $expected = $this->Objects->find()
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->where(['created_by' => 1])
            ->toArray();

        $result = $this->Objects->find('mine')
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        static::assertEquals($expected, $result);
    }

    /**
     * Test save emojis in text fields.
     *
     * @return void
     * @coversNothing
     */
    public function testEmoji()
    {
        $object = $this->Objects->get(1);
        $expected = '🙈 😂 😱';
        $info = Database::basicInfo();
        if ($info['vendor'] == 'mysql' && (empty($info['encoding']) || $info['encoding'] != 'utf8mb4')) {
            $expected = '';
        }
        $object['description'] = $expected;
        $this->Objects->save($object);
        $object = $this->Objects->get(1);
        static::assertEquals($object['description'], $expected);
    }

    /**
     * Data provider for `testSaveAbstractDisabledTypes` test case.
     *
     * @return array
     */
    public function saveAbstractDisabledTypes()
    {
        return [
            'objects' => [
                true,
                true,
                'objects',
            ],
            'media' => [
                true,
                true,
                'media',
            ],
            'documents' => [
                false,
                true,
                'documents',
            ],
            'news' => [
                true,
                false,
                'news',
            ],
        ];
    }

    /**
     * Test that save of abstract or not enabled types fails as expected.
     *
     * @param bool $abstract Is the type abstract?
     * @param bool $enabled Is the type enabled?
     * @param string $type Type being saved.
     * @return void
     * @covers ::beforeSave()
     * @dataProvider saveAbstractDisabledTypes()
     */
    public function testSaveAbstractDisabledTypes($abstract, $enabled, $type)
    {
        if ($abstract || !$enabled) {
            $this->expectException(PersistenceFailedException::class);
        }

        $object = $this->Objects->newEntity([]);
        $object->type = $type;

        $result = $this->Objects->saveOrFail($object);

        static::assertInstanceOf(ObjectEntity::class, $result);
    }

    /**
     * Test `findAncestor()`
     *
     * @return void
     * @covers ::findAncestor()
     */
    public function testFindAncestor()
    {
        $objects = $this->Objects->find('ancestor', [11])
            ->order([$this->Objects->aliasField('id') => 'ASC'])
            ->toArray();
        static::assertNotEmpty($objects);
        $ids = Hash::extract($objects, '{n}.id');
        static::assertEquals([2, 4, 12], $ids);
    }

    /**
     * Test `findParent()`
     *
     * @return void
     * @covers ::findParent()
     */
    public function testFindParent()
    {
        $objects = $this->Objects->find('parent', [12])->toArray();
        static::assertNotEmpty($objects);
        $ids = Hash::extract($objects, '{n}.id');
        static::assertEquals([4], $ids);
    }

    /**
     * Data provider for `checkLangTag`.
     *
     * @return array
     */
    public function checkLangTagProvider()
    {
        return [
            'any lang' => [
                'en-US',
                [
                    'default' => null,
                ],
                [
                    'lang' => 'en-US',
                ],
            ],
            'use default' => [
                'en',
                [
                    'default' => 'en',
                ],
                [
                    'lang' => '',
                ],
            ],
        ];
    }

    /**
     * Test `checkLangTag()`.
     *
     * @param string $expected Expected result.
     * @param array $config I18n config.
     * @param array $data Save input data.
     * @return void
     * @dataProvider checkLangTagProvider()
     * @covers ::checkLangTag()
     */
    public function testCheckLangTag($expected, array $config, array $data)
    {
        Configure::write('I18n', $config);

        $object = $this->Objects->get(3);
        $object = $this->Objects->patchEntity($object, $data);
        $object = $this->Objects->save($object);

        static::assertSame($expected, $object->get('lang'));
    }

    /**
     * Data provider for `checkLocked`.
     *
     * @return array
     */
    public function checkLockedProvider(): array
    {
        return [
            'not locked' => [
                true,
                [
                    'id' => 3,
                    'status' => 'on',
                ],
            ],
            'forbidden' => [
                new LockedResourceException('Operation not allowed on "locked" objects'),
                [
                    'id' => 2,
                    'status' => 'off',
                ],
                'on',
            ],
            'allowed' => [
                true,
                [
                    'id' => 1,
                    'description' => 'new description',
                ],
            ],
            'locked now' => [
                true,
                [
                    'id' => 3,
                    'uname' => 'new-uname',
                    'locked' => true,
                ],
            ],
        ];
    }

    /**
     * Test `checkLocked()`.
     *
     * @param string|\Exception $expected result or Exception.
     * @param array $data Save input data.
     * @return void
     * @dataProvider checkLockedProvider()
     * @covers ::checkLocked()
     */
    public function testCheckLocked($expected, array $data): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $object = $this->Objects->get(Hash::get($data, 'id'));
        $object = $this->Objects->patchEntity($object, $data);
        $object = $this->Objects->saveOrFail($object);

        static::assertNotEmpty($object);
    }

    /**
     * Test `findTranslations()`.
     *
     * @return void
     * @covers ::findTranslations()
     */
    public function testFindTranslations()
    {
        $result = $this->Objects->find('translations', ['lang' => 'fr'])
            ->where(['Objects.id' => 2])
            ->toArray();

        static::assertNotEmpty($result);
        static::assertSame(1, count($result));
        static::assertSame(2, $result[0]['id']);
        static::assertSame(1, count($result[0]['translations']));
    }

    /**
     * Test `findTranslations() with status`.
     *
     * @return void
     * @covers ::findTranslations()
     */
    public function testFindTranslationsWithStatus()
    {
        Configure::write('Status.level', 'on');
        $result = $this->Objects->find('translations')
            ->where(['Objects.id' => 2])
            ->toArray();

        static::assertSame(2, count($result[0]['translations']));

        Configure::write('Status.level', 'draft');
        $result = $this->Objects->find('translations')
            ->where(['Objects.id' => 2])
            ->toArray();

        static::assertSame(3, count($result[0]['translations']));
    }

    /**
     * Data provider for `testFindAvailable`.
     *
     * @return array
     */
    public function findAvailableProvider(): array
    {
        return [
            'no status' => [
                13,
                ['id > 0'],
            ],
            'status on' => [
                8,
                ['id > 5'],
                'on',
            ],
        ];
    }

    /**
     * Test `findAvailable()`.
     *
     * @param int $expected Expected results.
     * @param array $condition Search condition.
     * @param string $statusLevel Configuration to write.
     * @return void
     * @dataProvider findAvailableProvider()
     * @covers ::findAvailable()
     */
    public function testFindAvailable(int $expected, array $condition, ?string $statusLevel = null): void
    {
        if (!empty($statusLevel)) {
            Configure::write('Status.level', $statusLevel);
        }

        $count = $this->Objects->find('available')->where($condition)->count();
        static::assertSame($expected, $count);
    }

    /**
     * Data provider for `testFindPublishable`.
     *
     * @return array
     */
    public function findPublishableProvider(): array
    {
        return [
            'on + publish' => [
                11,
                [
                    'Status.level' => 'on',
                    'Publish.checkDate' => true,
                ],
            ],
            'draft' => [
                16,
                [
                    'Status.level' => 'draft',
                ],
            ],
        ];
    }

    /**
     * Test `findPublishable()`.
     *
     * @param int $expected Expected results.
     * @param array $config Configuration to write.
     * @return void
     * @dataProvider findPublishableProvider()
     * @covers ::findPublishable()
     */
    public function testFindPublishable(int $expected, ?array $config = null): void
    {
        if (!empty($config)) {
            Configure::write($config);
        }

        $result = $this->Objects->find('publishable')->count();
        static::assertSame($expected, $result);
    }

    /**
     * Test `findPublishDateAllowed()`.
     *
     * @return void
     * @covers ::findPublishDateAllowed()
     */
    public function testFindPublishDateAllowed(): void
    {
        $result = $this->Objects->find('publishDateAllowed')->toArray();
        static::assertSame(13, count($result));
    }

    /**
     * Test `findPublishDateAllowed()` on a single object changing `publish_end`.
     *
     * @return void
     * @covers ::findPublishDateAllowed()
     */
    public function testFindPublishDateAllowedSingle(): void
    {
        $result = $this->Objects->find('publishDateAllowed')->where(['id' => 2])->first();
        static::assertNull($result);

        $object = $this->Objects->get(2);
        $object->publish_end = FrozenTime::parse(time() + DAY);
        $this->Objects->saveOrFail($object);

        $result = $this->Objects->find('publishDateAllowed')->where(['id' => 2])->first();
        static::assertNotNull($result);
    }

    /**
     * Test `findCategories` method.
     *
     * @return void
     * @covers ::findCategories()
     * @covers ::categoriesQuery()
     */
    public function testFindCategories()
    {
        $result = TableRegistry::getTableLocator()
            ->get('Documents')
            ->find('categories', ['first-cat,second-cat'])
            ->toArray();
        static::assertSame(1, count($result));
    }

    /**
     * Test `findTags` method.
     *
     * @return void
     * @covers ::findTags()
     * @covers ::categoriesQuery()
     */
    public function testFindTags()
    {
        $result = TableRegistry::getTableLocator()
            ->get('Profiles')
            ->find('tags', ['first-tag'])
            ->toArray();
        static::assertSame(1, count($result));
    }

    /**
     * Test `findUnameId` method.
     *
     * @return void
     * @covers ::findUnameId()
     */
    public function testFindUnameID()
    {
        $result = TableRegistry::getTableLocator()
            ->get('Profiles')
            ->find('unameId', ['gustavo-supporto'])
            ->firstOrFail();
        static::assertSame(4, $result->get('id'));

        $result = TableRegistry::getTableLocator()
            ->get('Profiles')
            ->find('unameId', [4])
            ->firstOrFail();
        static::assertSame('gustavo-supporto', $result->get('uname'));
    }

    /**
     * Test that only available children are returned.
     *
     * @return void
     * @coversNothing
     */
    public function testParentsAvailable(): void
    {
        $object = $this->Objects->get(2, ['contain' => ['Parents']]);
        static::assertNotEmpty($object->parents);

        $firstParent = $object->parents[0];
        $firstParent->status = 'off';
        $this->Objects->Parents->saveOrFail($firstParent);

        Configure::write('Status.level', 'off');
        $object = $this->Objects->get(2, ['contain' => ['Parents']]);
        $childrenIds = Hash::extract($object->parents, '{*}.id');
        static::assertContains($firstParent->id, $childrenIds);

        Configure::write('Status.level', 'draft');
        $object = $this->Objects->get(2, ['contain' => ['Parents']]);
        $childrenIds = Hash::extract($object->parents, '{*}.id');
        static::assertNotContains($firstParent->id, $childrenIds);
    }
}
