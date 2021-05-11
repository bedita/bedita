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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Exception\LockedResourceException;
use BEdita\Core\Filesystem\FilesystemRegistry;
use BEdita\Core\Model\Action\AddRelatedObjectsAction;
use BEdita\Core\Model\Behavior\PlaceholdersBehavior;
use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Behavior\PlaceholdersBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\PlaceholdersBehavior
 */
class PlaceholdersBehaviorTest extends TestCase
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
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Streams',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.History',
    ];

    /** @inheritDoc */
    public function setUp()
    {
        parent::setUp();

        FilesystemRegistry::setConfig(Configure::read('Filesystem'));
    }

    /** @inheritDoc */
    public function tearDown()
    {
        FilesystemRegistry::dropAll();

        parent::tearDown();
    }

    /**
     * Data provider for {@see PlaceholdersBehaviorTest::testExtractPlaceholders()} test case.
     *
     * @return array[]
     */
    public function extractPlaceholdersProvider(): array
    {
        return [
            'no configured fields' => [
                [],
                [
                    'body' => '<p>This is a text</p><!-- BE-PLACEHOLDER.10.eyAiZm9vIjogImJhciIgfQ== -->',
                ],
                [],
            ],
            'missing field' => [
                [],
                [
                    'body' => '<p>This is a text</p><!-- BE-PLACEHOLDER.10.eyAiZm9vIjogImJhciIgfQ== -->',
                ],
                ['description'],
            ],
            'simple' => [
                [
                    [
                        'id' => 10,
                        'params' => [
                            'body' => [
                                [
                                    'offset' => 21,
                                    'length' => 26,
                                    'params' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'body' => '<p>This is a text</p><!-- BE-PLACEHOLDER.10 -->',
                ],
            ],
            'simple, extra spaces' => [
                [
                    [
                        'id' => 10,
                        'params' => [
                            'body' => [
                                [
                                    'offset' => 21,
                                    'length' => 30,
                                    'params' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'body' => '<p>This is a text</p><!--  BE-PLACEHOLDER.10    -->',
                ],
            ],
            'simple, no spaces' => [
                [
                    [
                        'id' => 10,
                        'params' => [
                            'body' => [
                                [
                                    'offset' => 21,
                                    'length' => 24,
                                    'params' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'body' => '<p>This is a text</p><!--BE-PLACEHOLDER.10-->',
                ],
            ],
            'simple, with params' => [
                [
                    [
                        'id' => 10,
                        'params' => [
                            'body' => [
                                [
                                    'offset' => 21,
                                    'length' => 51,
                                    'params' => '{ "foo": "bar" }',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'body' => '<p>This is a text</p><!-- BE-PLACEHOLDER.10.eyAiZm9vIjogImJhciIgfQ== -->',
                ],
            ],
            'multibyte string, multiple fields, with params' => [
                [
                    [
                        'id' => 10,
                        'params' => [
                            'description' => [
                                [
                                    'offset' => 0,
                                    'length' => 26,
                                    'params' => null,
                                ],
                                [
                                    'offset' => 55,
                                    'length' => 51,
                                    'params' => '{ "foo": "bar" }',
                                ],
                            ],
                            'body' => [
                                [
                                    'offset' => 25,
                                    'length' => 26,
                                    'params' => null,
                                ],
                            ],
                        ],
                    ],
                    [
                        'id' => 14,
                        'params' => [
                            'body' => [
                                [
                                    'offset' => 75,
                                    'length' => 43,
                                    'params' => 'not a json',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'title' => '<strong>This won\'t be extracted: <!-- BE-PLACEHOLDER.1 --></strong>',
                    'description' => '<!-- BE-PLACEHOLDER.10 --><h1>My sweet placeholder</h1><!-- BE-PLACEHOLDER.10.eyAiZm9vIjogImJhciIgfQ== -->',
                    'body' => '<p>Is this working? 😶</p><!-- BE-PLACEHOLDER.10 --><p>Apparently yes! 👍</p><!-- BE-PLACEHOLDER.14.bm90IGEganNvbg== -->',
                ],
            ],
        ];
    }

    /**
     * Test {@see PlaceholdersBehavior::extractPlaceholders()}.
     *
     * @param array[] $expected Expected result.
     * @param array $data Entity data.
     * @param string[] $fields Fields.
     * @return void
     *
     * @dataProvider extractPlaceholdersProvider()
     * @covers ::extractPlaceholders()
     */
    public function testExtractPlaceholders(array $expected, array $data, array $fields = ['description', 'body']): void
    {
        $entity = new Entity($data);
        $actual = PlaceholdersBehavior::extractPlaceholders($entity, $fields);

        static::assertSame($expected, $actual);
    }

    /**
     * Test {@see PlaceholdersBehavior::afterSave()}.
     *
     * @return void
     *
     * @covers ::afterSave()
     * @covers ::getAssociation()
     * @covers ::prepareEntities()
     */
    public function testSavePlaceholders(): void
    {
        $body = '<!-- BE-PLACEHOLDER.10 --><h1>My sweet placeholder</h1><!-- BE-PLACEHOLDER.10.eyAiZm9vIjogImJhciIgfQ== -->';
        $expected = [
            10 => [
                'body' => [
                    [
                        'offset' => 0,
                        'length' => 26,
                        'params' => null,
                    ],
                    [
                        'offset' => 55,
                        'length' => 51,
                        'params' => '{ "foo": "bar" }',
                    ],
                ],
            ],
        ];

        $table = $this->getTableLocator()->get('Documents');

        // Save with placeholder in body.
        $entity = $table->get(2, ['contain' => ['ObjectTypes']]);
        $entity->set('body', $body);
        $table->saveOrFail($entity);

        // Reload entity from database, with placeholders.
        $entity = $table->get(2, ['contain' => ['ObjectTypes', 'Placeholder']]);
        static::assertSame($body, $entity->get('body'), 'Entity body has been changed');
        static::assertTrue($entity->has('placeholder'));

        // Run assertions.
        $placeholders = $entity->get('placeholder');
        $ids = Hash::extract($placeholders, '{n}.id');
        static::assertSame(array_keys($expected), $ids);
        foreach ($placeholders as $placeholder) {
            $id = Hash::get($placeholder, 'id');
            $params = Hash::get($placeholder, '_joinData.params');

            static::assertSame($expected[$id], $params);
        }
    }

    /**
     * Test {@see PlaceholdersBehavior::afterSave()}.
     *
     * @return void
     *
     * @covers ::afterSave()
     * @covers ::getAssociation()
     * @covers ::prepareEntities()
     */
    public function testSavePlaceholdersReplace(): void
    {
        $body = '<!-- BE-PLACEHOLDER.10 --><h1>My sweet placeholder</h1><!-- BE-PLACEHOLDER.10.eyAiZm9vIjogImJhciIgfQ== -->';
        $expected = [
            10 => [
                'body' => [
                    [
                        'offset' => 0,
                        'length' => 26,
                        'params' => null,
                    ],
                    [
                        'offset' => 55,
                        'length' => 51,
                        'params' => '{ "foo": "bar" }',
                    ],
                ],
            ],
        ];

        $table = $this->getTableLocator()->get('Documents');

        // Save hypothetical previous data.
        $media = $this->getTableLocator()->get('Media');
        $action = new AddRelatedObjectsAction(['association' => $table->getAssociation('Placeholder')]);
        $action([
            'entity' => $table->get(2, ['contain' => ['ObjectTypes']]),
            'relatedEntities' => [
                $media->get(10, ['contain' => ['ObjectTypes']])->set(['_joinData' => ['params' => ['description' => []]]]),
                $media->get(14, ['contain' => ['ObjectTypes']]),
            ],
        ]);

        // Save with placeholder in body.
        $entity = $table->get(2, ['contain' => ['ObjectTypes', 'Placeholder']]);
        static::assertSame([10, 14], Hash::extract($entity->get('placeholder'), '{n}.id')); // Check that previous placeholders exist.
        $entity->set('body', $body);
        $table->saveOrFail($entity);

        // Reload entity from database, with placeholders.
        $entity = $table->get(2, ['contain' => ['ObjectTypes', 'Placeholder']]);
        static::assertSame($body, $entity->get('body'), 'Entity body has been changed');
        static::assertTrue($entity->has('placeholder'));

        // Run assertions.
        $placeholders = $entity->get('placeholder');
        $ids = Hash::extract($placeholders, '{n}.id');
        static::assertSame(array_keys($expected), $ids);
        foreach ($placeholders as $placeholder) {
            $id = Hash::get($placeholder, 'id');
            $params = Hash::get($placeholder, '_joinData.params');

            static::assertSame($expected[$id], $params);
        }
    }

    /**
     * Test {@see PlaceholdersBehavior::afterSave()}.
     *
     * @return void
     *
     * @covers ::afterSave()
     * @covers ::getAssociation()
     * @covers ::prepareEntities()
     */
    public function testSavePlaceholdersRemove(): void
    {
        $body = '<h1>My sweet placeholder</h1>';

        $table = $this->getTableLocator()->get('Documents');

        // Save hypothetical previous data.
        $media = $this->getTableLocator()->get('Media');
        $action = new AddRelatedObjectsAction(['association' => $table->getAssociation('Placeholder')]);
        $action([
            'entity' => $table->get(2, ['contain' => ['ObjectTypes']]),
            'relatedEntities' => [
                $media->get(10, ['contain' => ['ObjectTypes']])->set(['_joinData' => ['params' => ['description' => []]]]),
                $media->get(14, ['contain' => ['ObjectTypes']]),
            ],
        ]);

        // Save with placeholder in body.
        $entity = $table->get(2, ['contain' => ['ObjectTypes', 'Placeholder']]);
        static::assertSame([10, 14], Hash::extract($entity->get('placeholder'), '{n}.id')); // Check that previous placeholders exist.
        $entity->set('body', $body);
        $table->saveOrFail($entity);

        // Reload entity from database, with placeholders.
        $entity = $table->get(2, ['contain' => ['ObjectTypes', 'Placeholder']]);
        static::assertSame($body, $entity->get('body'), 'Entity body has been changed');
        static::assertTrue($entity->has('placeholder'));

        // Run assertions.
        $placeholders = $entity->get('placeholder');
        $ids = Hash::extract($placeholders, '{n}.id');
        static::assertSame([], $ids);
    }

    /**
     * Test {@see PlaceholdersBehavior::beforeSave()}.
     *
     * @return void
     *
     * @covers ::beforeSave()
     * @covers ::getAssociation()
     * @covers ::ensureNotPlaceholded()
     */
    public function testBeforeSaveLockedEntity(): void
    {
        $body = '<!-- BE-PLACEHOLDER.10 --><h1>My sweet placeholder</h1>';

        // Save with placeholder in body.
        $table = $this->getTableLocator()->get('Documents');
        $entity = $table->get(2, ['contain' => ['ObjectTypes']]);
        $entity->set('body', $body);
        $table->saveOrFail($entity);

        $entity = $table->get(2, ['contain' => ['ObjectTypes', 'Placeholder']]);
        static::assertSame([10], Hash::extract($entity->get('placeholder'), '{n}.id'));

        // Try to delete media.
        $this->expectException(LockedResourceException::class);
        $this->expectExceptionMessage('Cannot delete object 10 because it is still placeholded in one object');
        $table = $this->getTableLocator()->get('Media');
        $entity = $table->get(10, ['contain' => ['ObjectTypes']]);
        $entity->set('deleted', true);
        $table->saveOrFail($entity);
    }
}
