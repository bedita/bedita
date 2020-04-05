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

namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Test\Utility\TestFilesystemTrait;

/**
 * @coversDefaultClass \BEdita\API\Controller\UploadController
 */
class UploadControllerTest extends IntegrationTestCase
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
        $this->filesystemCleanup();
        parent::tearDown();
    }

    /**
     * Test upload method.
     *
     * @param array $data The file data.
     * @return void
     *
     * @covers ::upload()
     * @covers ::initialize()
     */
    public function testUpload()
    {
        $fileName = 'gustavo.json';
        $contents = '{"name":"Gustavo","surname":"Supporto"}';
        $contentType = 'application/json';

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
        $this->post(sprintf('/files/upload/%s', $fileName), $contents);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);
        static::assertArrayHasKey('data', $response);

        $id = $response['data']['id'];
        $url = sprintf('http://api.example.com/files/%s', $id);
        static::assertSame('files', $response['data']['type']);
        static::assertEquals($fileName, $response['data']['attributes']['title']);

        $this->assertHeader('Location', $url);
    }
}
