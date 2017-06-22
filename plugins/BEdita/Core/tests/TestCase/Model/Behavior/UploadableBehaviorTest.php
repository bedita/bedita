<?php
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

use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;

/**
 * @coversDefaultClass \BEdita\Core\Model\Behavior\UploadableBehavior
 */
class UploadableBehaviorTest extends TestCase
{

    /**
     * Synapse.JS
     *
     * @see https://github.com/Chialab/synapse
     *
     * @var string
     */
    const SYNAPSE_JS = 'export const synapse = Promise.resolve();';

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\StreamsTable
     */
    public $Streams;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.streams',
    ];

    /**
     * List of files to keep in test filesystem.
     *
     * @var array
     */
    private $keep = [];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        FilesystemRegistry::setConfig(Configure::consume('Filesystem'));

        $this->Streams = TableRegistry::get('Streams');
        $this->keep = collection(FilesystemRegistry::getMountManager()->listContents('default://'))
            ->map(function (array $object) {
                return sprintf('%s://%s', $object['filesystem'], $object['path']);
            })
            ->toList();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        // Cleanup test filesystem.
        $mountManager = FilesystemRegistry::getMountManager();
        collection($mountManager->listContents('default://'))
            ->map(function (array $object) {
                return sprintf('%s://%s', $object['filesystem'], $object['path']);
            })
            ->reject(function ($uri) {
                return in_array($uri, $this->keep);
            })
            ->each([$mountManager, 'delete']);

        unset($this->Streams);
        FilesystemRegistry::dropAll();

        parent::tearDown();
    }

    /**
     * Data provider for `testAfterSave` test case.
     *
     * @return array
     */
    public function afterSaveProvider()
    {
        $uuid = Text::uuid();
        $emberJs = 'export const synapse = Promise.reject("Synapse is deprecated, please use Ember.JS instead");';

        return [
            'nothing to do' => [
                [
                    "default://{$uuid}-synapse.js" => static::SYNAPSE_JS,
                ],
                [
                    'version' => 99, // Update some useless field so that the save is actually triggered.
                ],
                $uuid,
            ],
            'updated contents' => [
                [
                    "default://{$uuid}-synapse.js" => $emberJs,
                ],
                [
                    'contents' => $emberJs,
                ],
                $uuid,
            ],
            'renamed file' => [
                [
                    "default://{$uuid}-synapse.js" => false,
                    "default://{$uuid}-dna.js" => static::SYNAPSE_JS,
                ],
                [
                    'file_name' => 'dna.js',
                ],
                $uuid,
            ],
            'updated contents and renamed file' => [
                [
                    "default://{$uuid}-synapse.js" => false,
                    "default://{$uuid}-dna.js" => $emberJs,
                ],
                [
                    'file_name' => 'dna.js',
                    'contents' => $emberJs,
                ],
                $uuid,
            ],
        ];
    }

    /**
     * Test file management after the entity is saved.
     *
     * @param array $expected Expected files on filesystem and their contents.
     * @param array $data Data to patch entity with.
     * @param string $uuid UUID.
     * @return void
     *
     * @dataProvider afterSaveProvider()
     * @covers ::afterSave()
     * @covers ::processUpload()
     * @covers ::write()
     */
    public function testAfterSave(array $expected, array $data, $uuid)
    {
        $manager = FilesystemRegistry::getMountManager();

        // Prepare environment.
        $stream = $this->Streams->newEntity([
            'contents' => static::SYNAPSE_JS,
            'file_name' => 'synapse.js',
        ]);
        $stream->uuid = $uuid;
        $this->Streams->saveOrFail($stream);

        static::assertTrue($manager->has($stream->uri));
        static::assertSame(static::SYNAPSE_JS, $manager->read($stream->uri));

        // Re-save entity.
        $this->Streams->patchEntity($stream, $data, ['accessibleFields' => ['*' => true]]);
        if ($stream->isDirty('file_name')) {
            $stream->uri = $stream->filesystemPath(); // Force update of URI.
        }
        $this->Streams->saveOrFail($stream);

        foreach ($expected as $path => $contents) {
            if ($contents === false) {
                static::assertFalse($manager->has($path));
            } else {
                static::assertTrue($manager->has($path));
                static::assertSame($contents, $manager->read($path));
            }
        }
    }

    /**
     * Test file management after the entity is delete.
     *
     * @return void
     *
     * @covers ::afterDelete()
     * @covers ::processDelete()
     */
    public function testAfterDelete()
    {
        $manager = FilesystemRegistry::getMountManager();

        // Prepare environment.
        $stream = $this->Streams->newEntity([
            'contents' => static::SYNAPSE_JS,
            'file_name' => 'synapse.js',
        ]);
        $this->Streams->saveOrFail($stream);
        $path = $stream->uri;

        static::assertTrue($manager->has($stream->uri));
        static::assertSame(static::SYNAPSE_JS, $manager->read($stream->uri));

        // Delete entity.
        $this->Streams->deleteOrFail($stream);

        static::assertFalse($manager->has($path));
    }
}
