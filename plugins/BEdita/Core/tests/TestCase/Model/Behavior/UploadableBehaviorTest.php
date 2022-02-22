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
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Behavior\UploadableBehavior
 */
class UploadableBehaviorTest extends TestCase
{
    use TestFilesystemTrait;

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
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->filesystemSetup();
        $this->Streams = TableRegistry::getTableLocator()->get('Streams');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        $this->filesystemRestore();
        unset($this->Streams);
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
            'private file' => [
                [
                    "default://9e58fa47-db64-4479-a0ab-88a706180d59-new-file.txt" => $newContents,
                ],
                [
                    'file_name' => 'new-file.txt',
                    'contents' => $newContents,
                    'private_url' => true,
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
     * @covers ::setVisibility()
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
        $visibility = (bool)$stream->get('private_url') ? 'private' : 'public';

        foreach ($expected as $path => $contents) {
            if ($contents === false) {
                static::assertFalse($manager->has($path));
            } else {
                static::assertTrue($manager->has($path));
                static::assertSame($contents, $manager->read($path));
                static::assertEquals($visibility, $manager->getVisibility($path));
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
