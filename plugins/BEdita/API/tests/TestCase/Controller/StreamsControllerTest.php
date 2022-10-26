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
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Validation\Validation;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * @coversDefaultClass \BEdita\API\Controller\StreamsController
 */
class StreamsControllerTest extends IntegrationTestCase
{
    use ArraySubsetAsserts;
    use TestFilesystemTrait;

    /**
     * @inheritDoc
     */
    public $fixtures = [
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->filesystemSetup();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->filesystemRestore();
        unset($this->Streams);
    }

    /**
     * Test that `GET` requests work.
     *
     * @return void
     * @covers ::resource()
     * @covers ::getResourceId()
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
     * Test `upload` method.
     *
     * @return void
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
        $this->post(sprintf('/streams/upload/%s', $fileName), $contents);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);
        static::assertArrayHasKey('data', $response);

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

    /**
     * Test `download` method.
     *
     * @return void
     * @covers ::download()
     * @covers ::checkAcceptable()
     */
    public function testDownload(): void
    {
        $this->configRequestHeaders('GET', $this->getUserAuthHeader() + ['Content-Type' => 'text/plain']);
        $this->get('/streams/download/9e58fa47-db64-4479-a0ab-88a706180d59');

        $this->assertResponseCode(200);
        $this->assertContentType('text/plain');

        $response = (string)$this->_response->getBody();
        static::assertEquals(trim($response), 'Sample uploaded file.');
    }

    /**
     * Test that `checkAcceptable()` method.
     *
     * @return void
     * @covers ::checkAcceptable()
     */
    public function testCheckAcceptable()
    {
        $this->configRequestHeaders('GET', ['Accept' => 'text/plain']);
        $this->get('/streams');
        $this->assertResponseCode(406);
    }
}
