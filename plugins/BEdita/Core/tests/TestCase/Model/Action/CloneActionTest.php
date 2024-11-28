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

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\CloneAction;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Action\CloneAction} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\CloneAction
 */
class CloneActionTest extends TestCase
{
    use TestFilesystemTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.EndpointPermissions',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Config',
        'plugin.BEdita/Core.AsyncJobs',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Translations',
        'plugin.BEdita/Core.UserTokens',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.Tags',
        'plugin.BEdita/Core.ObjectTags',
        'plugin.BEdita/Core.History',
        'plugin.BEdita/Core.Annotations',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * Test clone a document with relationships and translations.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::execute()
     * @covers ::cloneEntity()
     * @covers ::setEntityField()
     * @covers ::cloneRelationships()
     * @covers ::cloneTranslations()
     */
    public function testClone(): void
    {
        // document with ID 2 from fixtures has 5 relationships records and 4 translations records
        $id = 2;
        $title = 'new title';
        $status = 'draft';
        $_meta = ['include' => ['relationships', 'translations']];
        $data = compact('title', 'status', '_meta');
        $table = $this->fetchTable('Documents');
        $action = new CloneAction(compact('table'));
        $actual = $action(compact('id', 'data'));
        $original = $table->get($id, ['contain' => ['Test', 'TestSimple', 'TestDefaults', 'Translations']]);
        $clone = $table->get($actual->id, ['contain' => ['Test', 'TestSimple', 'TestDefaults', 'Translations']]);
        static::assertEquals($clone->title, $title);
        static::assertEquals($clone->status, $status);
        static::assertEquals($clone->title, $actual->title);
        static::assertEquals($clone->status, $actual->status);
        // relation test
        $relatedTest = $clone->get('test');
        $originalRelatedTest = $original->get('test');
        foreach ($relatedTest as $key => $item) {
            static::assertEquals($item->uname, $originalRelatedTest[$key]->uname);
        }
        // relation test_simple
        $relatedTestSimple = $clone->get('test_simple');
        $originalRelatedTestSimple = $original->get('test_simple');
        foreach ($relatedTestSimple as $key => $item) {
            static::assertEquals($item->uname, $originalRelatedTestSimple[$key]->uname);
        }
        // relation test_defaults
        $relatedTestDefaults = $clone->get('test_defaults');
        $originalRelatedTestDefaults = $original->get('test_defaults');
        foreach ($relatedTestDefaults as $key => $item) {
            static::assertEquals($item->uname, $originalRelatedTestDefaults[$key]->uname);
        }
        // translations
        $translations = $clone->get('translations');
        static::assertCount(4, $translations);
        foreach ($translations as $translation) {
            foreach ($original->get('translations') as $originalTranslation) {
                if ($originalTranslation->lang === $translation->lang) {
                    static::assertEquals($originalTranslation->translated_fields, $translation->translated_fields);
                }
            }
        }
    }

    /**
     * Test clone a media.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::execute()
     * @covers ::cloneEntity()
     * @covers ::setEntityField()
     * @covers ::cloneStreams()
     * @covers ::cloneStream()
     */
    public function testCloneMedia(): void
    {
        $this->filesystemSetup();

        // ID 14, stream bedita-logo-gray.gif
        $id = 14;
        $title = 'new title';
        $status = 'draft';
        $include = [];
        $data = compact('title', 'status');
        $table = $this->fetchTable('Images');
        $original = $table->get($id, ['contain' => ['Streams']]);
        $action = new CloneAction(compact('table'));
        $actual = $action(compact('id', 'data'));
        $clone = $table->get($actual->id, ['contain' => ['Streams']]);
        static::assertEquals($clone->title, $title);
        static::assertEquals($clone->status, $status);
        static::assertEquals($clone->title, $actual->title);
        static::assertEquals($clone->status, $actual->status);
        // stream test
        $streams = $clone->get('streams');
        static::assertCount(1, $streams);
        $originalStreams = $original->get('streams');
        $expected = $originalStreams[0];
        $actual = $streams[0];
        static::assertEquals($expected->file_name, $actual->file_name);
        static::assertEquals($expected->mime_type, $actual->mime_type);
        static::assertEquals($expected->file_size, $actual->file_size);
        static::assertEquals($expected->hash_md5, $actual->hash_md5);
        static::assertEquals($expected->hash_sha1, $actual->hash_sha1);
        static::assertEquals($expected->width, $actual->width);
        static::assertEquals($expected->height, $actual->height);
    }

    /**
     * Data provider for testSetEntityField.
     *
     * @return array
     */
    public function setEntityFieldProvider(): array
    {
        return [
            'reset field' => [
                [
                    'reset' => ['my_field'],
                ],
                new ObjectEntity(),
                new ObjectEntity(['my_field' => 'value']),
                'my_field',
                null,
            ],
            'overwrite field' => [
                [
                    'attributes' => ['my_field' => 'overwrite_value'],
                ],
                new ObjectEntity(),
                new ObjectEntity(['my_field' => 'value']),
                'my_field',
                'overwrite_value',
            ],
            'not unique field, not in attributes' => [
                [],
                new ObjectEntity(['my_field' => 'value']),
                new ObjectEntity(),
                'my_field',
                'value',
            ],
            'unique field' => [
                [
                    'unique' => ['my_field'],
                ],
                new ObjectEntity(['my_field' => 'value']),
                new ObjectEntity(),
                'my_field',
                'value-:::HASH:::',
            ],
            'not a string, unique and nullable, not in attributes' => [
                [
                    'unique' => ['my_field'],
                    'nullable' => ['my_field'],
                ],
                new ObjectEntity(['my_field' => 999]),
                new ObjectEntity(),
                'my_field',
                null,
            ],
            'not a string, unique and not nullable, not in attributes => exception' => [
                [
                    'unique' => ['my_field'],
                ],
                new ObjectEntity(['my_field' => 999]),
                new ObjectEntity(),
                'my_field',
                new \RuntimeException('Cannot set unique field "my_field"'),
            ],
        ];
    }

    /**
     * Test `setEntityField` method.
     *
     * @return void
     * @covers ::setEntityField()
     * @dataProvider setEntityFieldProvider()
     */
    public function testSetEntityField(array $schemaInfo, ObjectEntity $sourceEntity, ObjectEntity $entity, string $field, $expected): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $action = new class extends CloneAction {
            public function setEntityField(array $schemaInfo, ObjectEntity $sourceEntity, ObjectEntity &$entity, string $field)
            {
                return parent::setEntityField($schemaInfo, $sourceEntity, $entity, $field);
            }
        };
        $actual = $action->setEntityField($schemaInfo, $sourceEntity, $entity, $field);
        if ($expected === null) {
            static::assertNull($actual);
        } elseif (strpos($expected, ':::HASH:::') !== false) {
            // check only the prefix
            static::assertStringStartsWith(str_replace(':::HASH:::', '', $expected), $actual);
        } else {
            static::assertEquals($expected, $actual);
        }
    }
}
