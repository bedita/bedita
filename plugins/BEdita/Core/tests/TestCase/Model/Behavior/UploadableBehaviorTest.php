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

/**
 * @coversDefaultClass \BEdita\Core\Model\Behavior\UploadableBehavior
 */
class UploadableBehaviorTest extends TestCase
{

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
     * List of files to keep in test filesystem, and their contents.
     *
     * @var \Cake\Collection\Collection
     */
    private $keep = [];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        FilesystemRegistry::setConfig(Configure::read('Filesystem'));
        $this->Streams = TableRegistry::get('Streams');

        $mountManager = FilesystemRegistry::getMountManager();
        $this->keep = collection($mountManager->listContents('default://'))
            ->map(function (array $object) use ($mountManager) {
                $path = sprintf('%s://%s', $object['filesystem'], $object['path']);
                $contents = fopen('php://memory', 'wb+');
                fwrite($contents, $mountManager->read($path));
                fseek($contents, 0);

                return compact('contents', 'path');
            })
            ->compile();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        // Cleanup test filesystem.
        $mountManager = FilesystemRegistry::getMountManager();
        $keep = $this->keep
            ->each(function (array $object) use ($mountManager) {
                $mountManager->putStream($object['path'], $object['contents']);
            })
            ->map(function (array $object) {
                return $object['path'];
            })
            ->toList();
        collection($mountManager->listContents('default://'))
            ->map(function (array $object) {
                return sprintf('%s://%s', $object['filesystem'], $object['path']);
            })
            ->reject(function ($uri) use ($keep) {
                return in_array($uri, $keep);
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
        $originalContents = "Sample uploaded file.\n";
        $newContents = 'Modified contents.';

        return [
            'nothing to do' => [
                [
                    "default://9e58fa47-db64-4479-a0ab-88a706180d59.txt" => $originalContents,
                ],
                [
                    'version' => 99, // Update some useless field so that the save is actually triggered.
                ],
            ],
            'updated contents' => [
                [
                    "default://9e58fa47-db64-4479-a0ab-88a706180d59.txt" => $newContents,
                ],
                [
                    'contents' => $newContents,
                ],
            ],
            'renamed file' => [
                [
                    "default://9e58fa47-db64-4479-a0ab-88a706180d59.txt" => false,
                    "default://9e58fa47-db64-4479-a0ab-88a706180d59-new-file.txt" => $originalContents,
                ],
                [
                    'file_name' => 'new-file.txt',
                ],
            ],
            'updated contents and renamed file' => [
                [
                    "default://9e58fa47-db64-4479-a0ab-88a706180d59.txt" => false,
                    "default://9e58fa47-db64-4479-a0ab-88a706180d59-new-file.txt" => $newContents,
                ],
                [
                    'file_name' => 'new-file.txt',
                    'contents' => $newContents,
                ],
            ],
        ];
    }

    /**
     * Test file management after the entity is saved.
     *
     * @param array $expected Expected files on filesystem and their contents.
     * @param array $data Data to patch entity with.
     * @return void
     *
     * @dataProvider afterSaveProvider()
     * @covers ::afterSave()
     * @covers ::processUpload()
     * @covers ::write()
     */
    public function testAfterSave(array $expected, array $data)
    {
        $manager = FilesystemRegistry::getMountManager();

        $stream = $this->Streams->get('9e58fa47-db64-4479-a0ab-88a706180d59');

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

        $stream = $this->Streams->get('9e58fa47-db64-4479-a0ab-88a706180d59');
        $path = $stream->uri;

        $this->Streams->deleteOrFail($stream);

        static::assertFalse($manager->has($path));
    }
}
