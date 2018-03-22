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

namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Filesystem\FilesystemRegistry;
use BEdita\Core\Filesystem\Thumbnail;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\Api\Controller\MediaController
 */
class MediaControllerTest extends IntegrationTestCase
{

    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.streams',
    ];

    /**
     * Generator instance.
     *
     * @var \BEdita\Core\Filesystem\Thumbnail\GlideGenerator
     */
    protected $generator;

    /**
     * List of files to keep in test filesystem, and their contents.
     *
     * @var \Cake\Collection\Collection
     */
    protected $keep;

    /**
     * Original thumbnails registry.
     *
     * @var \BEdita\Core\Filesystem\ThumbnailRegistry
     */
    protected $originalRegistry;

    /**
     * Original thumbnails configuration.
     *
     * @var array
     */
    protected $originalConfig;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        FilesystemRegistry::setConfig(Configure::read('Filesystem'));

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

        $keys = Thumbnail::configured();
        $this->originalRegistry = Thumbnail::getRegistry();
        $this->originalConfig = array_combine(
            $keys,
            array_map([Thumbnail::class, 'getConfig'], $keys)
        );

        Thumbnail::setRegistry(null);
        foreach ($keys as $config) {
            Thumbnail::drop($config);
        }

        Thumbnail::setConfig(Configure::read('Thumbnails.generators'));
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

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

        FilesystemRegistry::dropAll();

        foreach (Thumbnail::configured() as $config) {
            Thumbnail::getGenerator($config); // Must be loaded in order to drop it… WHY???!!!
            Thumbnail::drop($config);
        }
        Thumbnail::setRegistry($this->originalRegistry);
        Thumbnail::setConfig($this->originalConfig);
        unset($this->originalConfig, $this->originalRegistry);
    }

    /**
     * Data provider for `testThumbs` test case.
     *
     * @return array
     */
    public function thumbsProvider()
    {
        return [
            'single, default' => [
                [
                    [
                        'id' => 14,
                        'uuid' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                        'ready' => false,
                        'url' => 'https://static.example.org/thumbs/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif/ef5b382f91ad45aff0e33b89e6677df31fcf6034.gif',
                    ],
                ],
                14,
            ],
            'array of IDs, custom preset' => [
                [
                    [
                        'id' => 14,
                        'uuid' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                        'ready' => true,
                        'url' => 'https://static.example.org/thumbs/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif/7712de3f48d7ecb9b473cc12feb34af1af79309e.gif',
                    ],
                    [
                        'id' => 10,
                        'uuid' => '9e58fa47-db64-4479-a0ab-88a706180d59',
                        'ready' => false,
                        'acceptable' => false,
                        'url' => 'https://static.example.org/thumbs/9e58fa47-db64-4479-a0ab-88a706180d59-sample.txt/7712de3f48d7ecb9b473cc12feb34af1af79309e.txt',
                    ],
                ],
                [10, 14],
                [
                    'preset' => 'favicon-sync',
                ],
            ],
            'comma delimited list of IDs, custom preset' => [
                [
                    [
                        'id' => 14,
                        'uuid' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                        'ready' => true,
                        'url' => 'https://static.example.org/thumbs/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif/7712de3f48d7ecb9b473cc12feb34af1af79309e.gif',
                    ],
                    [
                        'id' => 10,
                        'uuid' => '9e58fa47-db64-4479-a0ab-88a706180d59',
                        'ready' => false,
                        'acceptable' => false,
                        'url' => 'https://static.example.org/thumbs/9e58fa47-db64-4479-a0ab-88a706180d59-sample.txt/7712de3f48d7ecb9b473cc12feb34af1af79309e.txt',
                    ],
                ],
                '10,14',
                [
                    'preset' => 'favicon-sync',
                ],
            ],
        ];
    }

    /**
     * Test `thumbs` method.
     *
     * @param array $expected Expected thumbnails.
     * @param int|int[] $id List of IDs.
     * @param array $query Query options.
     * @return void
     *
     * @dataProvider thumbsProvider()
     * @covers ::thumbs()
     * @covers ::getIds()
     */
    public function testThumbs($expected, $id, array $query = [])
    {
        $this->configRequestHeaders('GET');

        $path = '/media/thumbs';
        if (!is_array($id) && strpos($id, ',') === false) {
            $path .= '/' . $id;
        } else {
            $query['ids'] = $id;
        }
        $path .= '?' . http_build_query($query);

        $this->configRequestHeaders('GET');
        $this->get($path);

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);

        $thumbnails = Hash::get((array)$body, 'meta.thumbnails');
        $expected = Hash::sort($expected, '{*}.uuid');
        $thumbnails = Hash::sort($thumbnails, '{*}.uuid');
        static::assertEquals($expected, $thumbnails);
    }

    /**
     * Test `thumbs` method when media IDs are passed both as query string and in path.
     *
     * @return void
     *
     * @covers ::thumbs()
     * @covers ::getIds()
     */
    public function testThumbsTooManyIds()
    {
        $this->configRequestHeaders('GET');
        $this->get('/media/thumbs/1?ids=2,3');

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(400);
        static::assertSame('Cannot specify IDs in both path and query string', Hash::get($body, 'error.title'));
    }

    /**
     * Test `thumbs` method when no media IDs are passed.
     *
     * @return void
     *
     * @covers ::thumbs()
     * @covers ::getIds()
     */
    public function testThumbsNoIds()
    {
        $this->configRequestHeaders('GET');
        $this->get('/media/thumbs');

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(400);
        static::assertSame('Missing IDs to generate thumbnails for', Hash::get($body, 'error.title'));
    }
}
