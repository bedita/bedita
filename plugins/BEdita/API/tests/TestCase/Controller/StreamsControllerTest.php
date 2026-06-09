<?php
declare(strict_types=1);

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

use BEdita\API\Controller\StreamsController;
use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Test\Utility\TestArraySubsetTrait;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Validation\Validation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\API\Controller\StreamsController} Test Case
 */
#[CoversClass(StreamsController::class)]
class StreamsControllerTest extends IntegrationTestCase
{
    use TestArraySubsetTrait;
    use TestFilesystemTrait;

    /**
     * @inheritDoc
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->filesystemSetup(true, true);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->filesystemRestore();
        unset($this->Streams);

        parent::tearDown();
    }

    /**
     * Test that `GET` requests work.
     *
     * @return void
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
            'You are not allowed to update existing streams, please delete and re-upload',
        ));
    }

    /**
     * Test `upload` method.
     *
     * @return void
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
    public static function linkStreamProvider(): array
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
     */
    #[DataProvider('linkStreamProvider')]
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
     * Test content negotiation.
     *
     * @return void
     */
    public function testContentNegotiation()
    {
        $this->configRequestHeaders('GET', ['Accept' => 'text/plain']);
        $this->get('/streams');
        $this->assertResponseCode(406);

        $this->configRequestHeaders('GET', ['Accept' => 'application/json']);
        $this->get('/streams');
        $this->assertResponseCode(200);
    }

    /**
     * Test `uploadNewVersion` action: success case, version increments to 2.
     *
     * @return void
     */
    public function testUploadNewVersion(): void
    {
        $objectId = 10; // media/files object that already has version 1 stream in fixtures
        $fileName = 'new-version.json';
        $contents = '{"name":"NewVersion","v":2}';
        $contentType = 'application/json';

        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->post(sprintf('/streams/version/%d/%s', $objectId, $fileName), $contents);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);
        static::assertArrayHasKey('data', $response);

        $id = $response['data']['id'];
        $locationUrl = sprintf('http://api.example.com/streams/%s', $id);
        static::assertTrue(Validation::uuid($id));
        static::assertSame('streams', $response['data']['type']);
        static::assertEquals(['file_name' => $fileName, 'mime_type' => $contentType], $response['data']['attributes']);
        static::assertArraySubset(
            ['version' => 2, 'file_size' => strlen($contents), 'hash_sha1' => sha1($contents)],
            $response['data']['meta'],
        );
        $this->assertHeader('Location', $locationUrl);
    }

    /**
     * Test `uploadNewVersion` action: 409 Conflict when uploading an identical file.
     *
     * @return void
     */
    public function testUploadNewVersionConflict(): void
    {
        $objectId = 10;
        $fileName = 'duplicate.json';
        $contents = '{"duplicate":"yes"}';
        $contentType = 'application/json';

        // First upload must succeed (becomes version 2).
        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->post(sprintf('/streams/version/%d/%s', $objectId, $fileName), $contents);
        $this->assertResponseCode(201);

        // Uploading the exact same bytes again must return 409 Conflict.
        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->post(sprintf('/streams/version/%d/%s', $objectId, $fileName), $contents);
        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test `uploadNewVersion` action with PATCH: latest version is replaced in place.
     *
     * @return void
     */
    public function testReplaceLatestVersion(): void
    {
        $objectId = 10;
        $fileName = 'replacement.txt';
        $contents = 'replacement payload';
        $contentType = 'text/plain';

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->patch(sprintf('/streams/version/%d/%s', $objectId, $fileName), $contents);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);
        static::assertArrayHasKey('data', $response);
        static::assertSame('9e58fa47-db64-4479-a0ab-88a706180d59', $response['data']['id']);
        static::assertArraySubset(
            ['version' => 1, 'hash_sha1' => sha1($contents)],
            $response['data']['meta'],
        );
        static::assertEquals(['file_name' => $fileName, 'mime_type' => $contentType], $response['data']['attributes']);
    }

    /**
     * Test that DELETE on the latest versioned stream is allowed.
     */
    public function testDeleteLatestVersionAllowed(): void
    {
        // object 10 has only version 1 — it is the latest
        $uuid = '9e58fa47-db64-4479-a0ab-88a706180d59';

        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete(sprintf('/streams/%s', $uuid));

        $this->assertResponseCode(204);
    }

    /**
     * Test that DELETE on a non-latest versioned stream returns 409.
     */
    public function testDeleteNonLatestVersionConflict(): void
    {
        $objectId = 10;
        $version1Uuid = '9e58fa47-db64-4479-a0ab-88a706180d59';

        // Create version 2 so that version 1 is no longer the latest.
        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => 'application/json']);
        $this->post(sprintf('/streams/version/%d/v2.json', $objectId), '{"v":2}');
        $this->assertResponseCode(201);

        // Attempting to delete version 1 (not the latest) must be rejected.
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete(sprintf('/streams/%s', $version1Uuid));

        $this->assertResponseCode(409);
    }

    /**
     * Test that DELETE on a stream with no object_id (standalone) is always allowed.
     */
    public function testDeleteStandaloneStreamAllowed(): void
    {
        // This stream has version set but no object_id.
        $uuid = 'e5afe167-7341-458d-a1e6-042e8791b0fe';

        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete(sprintf('/streams/%s', $uuid));

        $this->assertResponseCode(204);
    }

    /**
     * Test {@see \BEdita\API\Controller\StreamsController::clone()} action.
     *
     * @return void
     */
    public function testClone(): void
    {
        $attributes = [
            'file_name' => 'bedita-logo-gray.gif',
            'mime_type' => 'image/gif',
        ];
        $meta = [
            'version' => 1,
            'file_size' => 927,
            'hash_md5' => 'a714dbb31ca89d5b1257245dfa5c5153',
            'hash_sha1' => '444b2b42b48b0b815d70f6648f8a7a23d5faf54b',
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/streams/clone/6aceb0eb-bd30-4f60-ac74-273083b921b6');

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);
        static::assertArrayHasKey('data', $response);

        $id = $response['data']['id'];
        $url = sprintf('http://api.example.com/streams/%s', $id);
        static::assertTrue(Validation::uuid($id));
        static::assertSame('streams', $response['data']['type']);
        static::assertEquals($attributes, $response['data']['attributes']);
        static::assertArraySubset($meta, $response['data']['meta']);
        static::assertSame(sprintf('https://static.example.org/files/%s-%s', $id, $attributes['file_name']), $response['data']['meta']['url']);

        $this->assertHeader('Location', $url);
    }
}
