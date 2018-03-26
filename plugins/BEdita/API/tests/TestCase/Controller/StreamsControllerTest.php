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

namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Core\Configure;
use Cake\Validation\Validation;

/**
 * @coversDefaultClass \BEdita\API\Controller\StreamsController
 */
class StreamsControllerTest extends IntegrationTestCase
{

    /**
     * {@inheritDoc}
     */
    public $fixtures = [
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
     * Test that `GET` requests work.
     *
     * @return void
     *
     * @covers ::resource()
     */
    public function testGet()
    {
        $id = 'e5afe167-7341-458d-a1e6-042e8791b0fe';

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get(sprintf('/streams/%s', $id));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test that `PATCH` requests are actually forbidden.
     *
     * @return void
     *
     * @covers ::resource()
     */
    public function testPatch()
    {
        $id = 'e5afe167-7341-458d-a1e6-042e8791b0fe';
        $data = [
            'id' => $id,
            'type' => 'streams',
            'attributes' => [
                'file_name' => 'gustavo.jpg',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch(sprintf('/streams/%s', $id), json_encode(compact('data')));

        $this->assertResponseCode(403);
        $this->assertContentType('application/vnd.api+json');

        $this->assertResponseContains(__d(
            'bedita',
            'You are not allowed to update existing streams, please delete and re-upload'
        ));
    }

    /**
     * Test upload method.
     *
     * @return void
     *
     * @covers ::upload()
     * @covers ::beforeFilter()
     */
    public function testUpload()
    {
        $fileName = 'synapse.js';
        $contents = 'exports.synapse = Promise.resolve();';
        $contentType = 'text/javascript';

        $attributes = [
            'file_name' => $fileName,
            'mime_type' => $contentType,
        ];
        $meta = [
            'version' => 1,
            'file_size' => strlen($contents),
            'hash_md5' => md5($contents),
            'hash_sha1' => sha1($contents),
            'url' => null,
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->post(sprintf('/streams/upload/%s', $fileName), $contents);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);

        static::assertArrayHasKey('data', $response);
        static::assertArrayHasKey('id', $response['data']);
        static::assertArrayHasKey('type', $response['data']);
        static::assertArrayHasKey('attributes', $response['data']);
        static::assertArrayHasKey('meta', $response['data']);
        static::assertArrayHasKey('links', $response);

        $id = $response['data']['id'];
        $url = sprintf('http://api.example.com/streams/%s', $id);
        $meta['url'] = sprintf('https://static.example.org/files/%s-synapse.js', $id);
        static::assertTrue(Validation::uuid($id));
        static::assertSame('streams', $response['data']['type']);
        static::assertEquals($attributes, $response['data']['attributes']);
        static::assertArraySubset($meta, $response['data']['meta']);

        $this->assertHeader('Location', $url);
    }

    /**
     * Test upload method.
     *
     * @return void
     *
     * @covers ::upload()
     * @covers ::beforeFilter()
     */
    public function testUploadBase64()
    {
        $fileName = 'synapse.js';
        $contents = 'exports.synapse = Promise.resolve();';
        $contentType = 'text/javascript';

        $attributes = [
            'file_name' => $fileName,
            'mime_type' => $contentType,
        ];
        $meta = [
            'version' => 1,
            'file_size' => strlen($contents),
            'hash_md5' => md5($contents),
            'hash_sha1' => sha1($contents),
            'url' => null,
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType, 'Content-Transfer-Encoding' => 'base64']);
        $this->post(sprintf('/streams/upload/%s', $fileName), base64_encode($contents));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);

        static::assertArrayHasKey('data', $response);
        static::assertArrayHasKey('id', $response['data']);
        static::assertArrayHasKey('type', $response['data']);
        static::assertArrayHasKey('attributes', $response['data']);
        static::assertArrayHasKey('meta', $response['data']);
        static::assertArrayHasKey('links', $response);

        $id = $response['data']['id'];
        $url = sprintf('http://api.example.com/streams/%s', $id);
        $meta['url'] = sprintf('https://static.example.org/files/%s-synapse.js', $id);
        static::assertTrue(Validation::uuid($id));
        static::assertSame('streams', $response['data']['type']);
        static::assertEquals($attributes, $response['data']['attributes']);
        static::assertArraySubset($meta, $response['data']['meta']);

        $this->assertHeader('Location', $url);
    }

    /**
     * Data provider for `testLinkStream` test case.
     *
     * @return array
     */
    public function linkStreamProvider()
    {
        return [
            'not a media' => [
                409,
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                'documents',
                2,
            ],
            'media subtype' => [
                200,
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                'files',
                10,
            ],
        ];
    }

    /**
     * Test linking a stream to a media.
     *
     * @param int $expected Expected response code.
     * @param string $uuid Stream UUID.
     * @param string $type Type of object to be linked.
     * @param int $id ID of object to be linked.
     * @return void
     *
     * @dataProvider linkStreamProvider()
     * @covers ::initialize()
     */
    public function testLinkStream($expected, $uuid, $type, $id)
    {
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $data = compact('id', 'type');
        $this->patch(sprintf('/streams/%s/relationships/object', $uuid), json_encode(compact('data')));

        $this->assertResponseCode($expected);
    }
}
