<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Controller\Component;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Utility\Hash;
use Cake\Validation\Validation;

/**
 * @coversDefaultClass \BEdita\API\Controller\Component\UploadComponent
 */
class UploadComponentTest extends IntegrationTestCase
{
    use TestFilesystemTrait;

    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->filesystemSetup();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $this->filesystemRestore();
        parent::tearDown();
    }

    /**
     * Upload data provider for testUpload()
     *
     * @return array
     */
    public function uploadProvider()
    {
        return [
            'javascript' => [
                [
                    'fileName' => 'synapse.js',
                    'contents' => 'exports.synapse = Promise.resolve();',
                    'contentType' => 'text/javascript',
                ],
            ],
            'xml' => [
                [
                    'fileName' => 'gustavo.xml',
                    'contents' => '<?xml version="1.0" encoding="utf-8"?><items><item>one</item><item>two</item></items>',
                    'contentType' => 'text/xml',
                ],
            ],
            'json' => [
                [
                    'fileName' => 'gustavo.json',
                    'contents' => '{"name":"Gustavo","surname":"Supporto"}',
                    'contentType' => 'application/json',
                ],
            ],
        ];
    }

    /**
     * Test upload method.
     *
     * @param array $data The file data.
     * @return void
     *
     * @dataProvider uploadProvider
     * @covers ::upload()
     * @covers ::beforeFilter()
     */
    public function testUpload($data)
    {
        $fileName = Hash::get($data, 'fileName');
        $contents = Hash::get($data, 'contents');
        $contentType = Hash::get($data, 'contentType');

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
        $meta['url'] = sprintf('https://static.example.org/files/%s-%s', $id, $fileName);
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
     * Test upload method with `private_url` query.
     *
     * @return void
     *
     * @covers ::upload()
     */
    public function testUploadPrivateUrl()
    {
        $fileName = 'private.txt';
        $contents = 'top secret URL';
        $contentType = 'text/plain';

        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType, 'Content-Transfer-Encoding' => 'base64']);
        $this->post(sprintf('/streams/upload/%s?private_url=true', $fileName), base64_encode($contents));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $meta = Hash::get($response, 'data.meta');
        static::assertNotEmpty($meta);
        static::assertTrue($meta['private_url']);
        static::assertNull($meta['url']);
    }
}
