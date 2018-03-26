<?php
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

namespace BEdita\Core\Test\TestCase\Filesystem\Thumbnail;

use BEdita\Core\Filesystem\Exception\InvalidStreamException;
use BEdita\Core\Filesystem\FilesystemRegistry;
use BEdita\Core\Filesystem\Thumbnail\GlideGenerator;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\Core\Filesystem\Thumbnail\GlideGenerator
 */
class GlideGeneratorTest extends TestCase
{

    /**
     * Fixtures.
     *
     * @var string[]
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.media',
        'plugin.BEdita/Core.streams',
    ];

    /**
     * Generator instance.
     *
     * @var \BEdita\Core\Filesystem\Thumbnail\GlideGenerator
     */
    protected $generator;

    /**
     * Streams table.
     *
     * @var \BEdita\Core\Model\Table\StreamsTable
     */
    protected $Streams;

    /**
     * List of files to keep in test filesystem, and their contents.
     *
     * @var \Cake\Collection\Collection
     */
    protected $keep;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        FilesystemRegistry::setConfig(Configure::read('Filesystem'));
        $this->Streams = TableRegistry::get('Streams');

        $this->generator = new GlideGenerator();
        $this->generator->initialize([]);

        $mountManager = FilesystemRegistry::getMountManager();
        $this->keep = collection($mountManager->listContents('thumbnails://', true))
            ->reject(function (array $object) {
                return $object['type'] === 'dir';
            })
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
        collection($mountManager->listContents('thumbnails://', true))
            ->reject(function (array $object) {
                return $object['type'] === 'dir';
            })
            ->map(function (array $object) {
                return sprintf('%s://%s', $object['filesystem'], $object['path']);
            })
            ->reject(function ($uri) use ($keep) {
                return in_array($uri, $keep);
            })
            ->each([$mountManager, 'delete']);

        unset($this->generator, $this->Streams);
        FilesystemRegistry::dropAll();

        parent::tearDown();
    }

    /**
     * Data provider for `testGetUrl` test case.
     *
     * @return array
     */
    public function getUrlProvider()
    {
        return [
            'text file' => [
                'https://static.example.org/thumbs/9e58fa47-db64-4479-a0ab-88a706180d59-sample.txt/' . sha1(serialize([])) . '.txt',
                '9e58fa47-db64-4479-a0ab-88a706180d59',
            ],
            'png file' => [
                'https://static.example.org/thumbs/e5afe167-7341-458d-a1e6-042e8791b0fe-bedita-logo.png/' . sha1(serialize(['w' => 100])) . '.png',
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['w' => 100],
            ],
        ];
    }

    /**
     * Test URL getter.
     *
     * @param string $expected Expected URL.
     * @param string $uuid Stream UUID.
     * @param array $options Thumbnail options.
     * @return void
     *
     * @dataProvider getUrlProvider()
     * @covers ::getUrl()
     * @covers ::getFilename()
     */
    public function testGetUrl($expected, $uuid, array $options = [])
    {
        $stream = $this->Streams->get($uuid);

        $url = $this->generator->getUrl($stream, $options);

        static::assertSame($expected, $url);
    }

    /**
     * Data provider for `testGenerate` test case.
     *
     * @return array
     */
    public function generateProvider()
    {
        return [
            'text file' => [
                new InvalidStreamException('Unable to generate thumbnail for stream 9e58fa47-db64-4479-a0ab-88a706180d59'),
                '9e58fa47-db64-4479-a0ab-88a706180d59',
            ],
            'png file' => [
                true,
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['w' => 100, 'h' => 100],
            ],
            'png file in jpg' => [
                true,
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['w' => 100, 'h' => 100, 'fm' => 'jpg'],
            ],
        ];
    }

    /**
     * Test thumbnail generation.
     *
     * @param bool|\Exception $expected Expected result.
     * @param string $uuid Stream UUID.
     * @param array $options Thumbnail options.
     * @return void
     *
     * @dataProvider generateProvider()
     * @covers ::generate()
     * @covers ::getFilename()
     * @covers ::getGlideApi()
     * @covers ::makeThumbnail()
     */
    public function testGenerate($expected, $uuid, array $options = [])
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $stream = $this->Streams->get($uuid);

        $result = $this->generator->generate($stream, $options);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testExists` test case.
     *
     * @return array
     */
    public function existsProvider()
    {
        return [
            'not valid' => [
                false,
                '9e58fa47-db64-4479-a0ab-88a706180d59',
            ],
            'existing' => [
                true,
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['w' => 100, 'h' => 100],
            ],
            'missing' => [
                false,
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['w' => 100, 'h' => 100, 'fm' => 'jpg'],
            ],
        ];
    }

    /**
     * Test existence checks.
     *
     * @param bool $expected Expected result.
     * @param string $uuid Stream UUID.
     * @param array $options Thumbnail options.
     * @return void
     *
     * @dataProvider existsProvider()
     * @covers ::exists()
     * @covers ::getFilename()
     */
    public function testExists($expected, $uuid, array $options = [])
    {
        $stream = $this->Streams->get($uuid);

        $result = $this->generator->exists($stream, $options);

        static::assertSame($expected, $result);
    }

    /**
     * Test deletion of thumbnails.
     *
     * @return void
     *
     * @covers ::delete()
     */
    public function testDelete()
    {
        $uuid = 'e5afe167-7341-458d-a1e6-042e8791b0fe';
        $path = 'e5afe167-7341-458d-a1e6-042e8791b0fe-bedita-logo.png';

        $stream = $this->Streams->get($uuid);

        static::assertContains($path, Hash::extract(FilesystemRegistry::getMountManager()->listContents('thumbnails://'), '{*}.path'));

        $this->generator->delete($stream);

        static::assertNotContains($path, Hash::extract(FilesystemRegistry::getMountManager()->listContents('thumbnails://'), '{*}.path'));
    }
}
