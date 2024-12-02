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

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Model\Action\CloneObjectAction;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Test\Utility\TestFilesystemTrait;

/**
 * {@see \BEdita\Core\Model\Action\CloneObjectAction} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\CloneObjectAction
 */
class CloneObjectActionTest extends IntegrationTestCase
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
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        // document with ID 2 from fixtures has 5 relationships records and 4 translations records
        $id = 2;
        $title = 'new title';
        $status = 'draft';
        $_meta = ['include' => ['relationships', 'translations']];
        $data = compact('title', 'status', '_meta');
        $table = $this->fetchTable('Documents');
        $action = new CloneObjectAction(compact('table'));
        $actual = $action(compact('id', 'data'));
        $original = $table->get($id, ['contain' => ['Test', 'TestSimple', 'TestDefaults', 'Translations']]);
        $clone = $table->get($actual->id, ['contain' => ['Test', 'TestSimple', 'TestDefaults', 'Translations']]);
        $this->assertEquals($clone->title, $title);
        $this->assertEquals($clone->status, $status);
        $this->assertEquals($clone->title, $actual->title);
        $this->assertEquals($clone->status, $actual->status);
        // relation test
        $relatedTest = $clone->get('test');
        $originalRelatedTest = $original->get('test');
        foreach ($relatedTest as $key => $item) {
            $this->assertEquals($item->uname, $originalRelatedTest[$key]->uname);
        }
        // relation test_simple
        $relatedTestSimple = $clone->get('test_simple');
        $originalRelatedTestSimple = $original->get('test_simple');
        foreach ($relatedTestSimple as $key => $item) {
            $this->assertEquals($item->uname, $originalRelatedTestSimple[$key]->uname);
        }
        // relation test_defaults
        $relatedTestDefaults = $clone->get('test_defaults');
        $originalRelatedTestDefaults = $original->get('test_defaults');
        foreach ($relatedTestDefaults as $key => $item) {
            $this->assertEquals($item->uname, $originalRelatedTestDefaults[$key]->uname);
        }
        // translations
        $translations = $clone->get('translations');
        $this->assertCount(4, $translations);
        foreach ($translations as $translation) {
            foreach ($original->get('translations') as $originalTranslation) {
                if ($originalTranslation->lang === $translation->lang) {
                    $this->assertEquals($originalTranslation->translated_fields, $translation->translated_fields);
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
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());

        // ID 14, stream bedita-logo-gray.gif
        $id = 14;
        $title = 'new title';
        $status = 'draft';
        $include = [];
        $data = compact('title', 'status');
        $table = $this->fetchTable('Images');
        $original = $table->get($id, ['contain' => ['Streams']]);
        $action = new CloneObjectAction(compact('table'));
        $actual = $action(compact('id', 'data'));
        $clone = $table->get($actual->id, ['contain' => ['Streams']]);
        $this->assertEquals($clone->title, $title);
        $this->assertEquals($clone->status, $status);
        $this->assertEquals($clone->title, $actual->title);
        $this->assertEquals($clone->status, $actual->status);
        // stream test
        $streams = $clone->get('streams');
        $this->assertCount(1, $streams);
        $originalStreams = $original->get('streams');
        $expected = $originalStreams[0];
        $actual = $streams[0];
        $this->assertEquals($expected->file_name, $actual->file_name);
        $this->assertEquals($expected->mime_type, $actual->mime_type);
        $this->assertEquals($expected->file_size, $actual->file_size);
        $this->assertEquals($expected->hash_md5, $actual->hash_md5);
        $this->assertEquals($expected->hash_sha1, $actual->hash_sha1);
        $this->assertEquals($expected->width, $actual->width);
        $this->assertEquals($expected->height, $actual->height);
    }

    /**
     * Data provider for testCloneRelationships.
     *
     * @return array
     */
    public function cloneRelationshipsProvider(): array
    {
        return [
            'no relationships' => [
                19,
                0,
                0,
            ],
            'relationship with 5 objects' => [
                2,
                5,
                0,
            ],
        ];
    }

    /**
     * Test clone relationships
     *
     * @param int $sourceId Source object ID
     * @param int $expectedCountLeft Expected count of left relationships
     * @param int $expectedCountRight Expected count of right relationships
     * @return void
     * @covers ::cloneRelationships()
     * @dataProvider cloneRelationshipsProvider()
     */
    public function testCloneRelationships(int $sourceId, int $expectedCountLeft, int $expectedCountRight): void
    {
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $source = $this->fetchTable('Objects')->get($sourceId);
        $table = $this->fetchTable($source->get('type'));
        $action = new class (['table' => $table]) extends CloneObjectAction {
            public function cloneRelationships(int $sourceId, int $destinationId, string $key): array
            {
                return parent::cloneRelationships($sourceId, $destinationId, $key);
            }
        };
        $clone = $action->cloneEntity($source, []);
        $actual = $action->cloneRelationships($sourceId, $clone->id, 'left_id');
        $this->assertEquals($expectedCountLeft, count($actual));
        $actual = $action->cloneRelationships($sourceId, $clone->id, 'right_id');
        $this->assertEquals($expectedCountRight, count($actual));
    }

    /**
     * Test clone unauthorized exception.
     *
     * @return void
     * @covers ::initialize()
     */
    public function testUnauthorizedException(): void
    {
        $this->expectException(\Cake\Http\Exception\UnauthorizedException::class);
        $this->expectExceptionMessage('Cannot clone object without a logged user');
        $table = $this->fetchTable('Documents');
        new CloneObjectAction(compact('table'));
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
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $action = new class extends CloneObjectAction {
            public function setEntityField(array $schemaInfo, ObjectEntity $sourceEntity, ObjectEntity $entity, string $field)
            {
                return parent::setEntityField($schemaInfo, $sourceEntity, $entity, $field);
            }
        };
        $actual = $action->setEntityField($schemaInfo, $sourceEntity, $entity, $field);
        if ($expected === null) {
            $this->assertNull($actual);
        } elseif (strpos($expected, ':::HASH:::') !== false) {
            // check only the prefix
            $this->assertStringStartsWith(str_replace(':::HASH:::', '', $expected), $actual);
        } else {
            $this->assertEquals($expected, $actual);
        }
    }
}
