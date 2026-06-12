<?php
declare(strict_types=1);

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

use BEdita\API\Controller\Component\UploadComponent;
use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Test\Utility\TestArraySubsetTrait;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Utility\Hash;
use Cake\Validation\Validation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(UploadComponent::class)]
class UploadComponentTest extends IntegrationTestCase
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
        $this->filesystemSetup();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->filesystemRestore();
        parent::tearDown();
    }

    /**
     * Upload data provider for testUpload()
     *
     * @return array
     */
    public static function uploadProvider(): array
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
     */
    #[DataProvider('uploadProvider')]
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
     * Test uploadNewVersion: success, version increments correctly.
     *
     * @return void
     */
    public function testUploadNewVersion(): void
    {
        $objectId = 10; // media/files object with an existing version 1 stream in fixtures
        $fileName = 'v2.json';
        $contents = '{"version":2}';
        $contentType = 'application/json';

        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->post(sprintf('/streams/version/%d/%s', $objectId, $fileName), $contents);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);
        static::assertArrayHasKey('data', $response);
        static::assertSame('streams', $response['data']['type']);
        static::assertEquals(['file_name' => $fileName, 'mime_type' => $contentType], $response['data']['attributes']);
        static::assertArraySubset(
            ['version' => 2, 'hash_sha1' => sha1($contents)],
            $response['data']['meta'],
        );
    }

    /**
     * Test uploadNewVersion: 409 Conflict on hash collision with existing version.
     *
     * @return void
     */
    public function testUploadNewVersionConflict(): void
    {
        $objectId = 10;
        $contents = '{"conflict":"test"}';
        $contentType = 'application/json';

        // First upload creates version 2.
        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->post(sprintf('/streams/version/%d/conflict.json', $objectId), $contents);
        $this->assertResponseCode(201);

        // Second upload of same bytes triggers hash conflict.
        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->post(sprintf('/streams/version/%d/conflict.json', $objectId), $contents);
        $this->assertResponseCode(409);
    }

    /**
     * Test uploadNewVersion: content matching an older version (not latest) is accepted.
     */
    public function testUploadNewVersionOlderContentAllowed(): void
    {
        $objectId = 10;
        $contentsV2 = '{"version":2,"marker":"alpha"}';
        $contentsV3 = '{"version":3,"marker":"beta"}';
        $contentType = 'application/json';

        // Create version 2.
        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->post(sprintf('/streams/version/%d/v2.json', $objectId), $contentsV2);
        $this->assertResponseCode(201);

        // Create version 3 with different content.
        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->post(sprintf('/streams/version/%d/v3.json', $objectId), $contentsV3);
        $this->assertResponseCode(201);

        // Re-upload content of version 2 — matches an older version but NOT the latest (v3) → allowed as version 4.
        $this->configRequestHeaders('POST', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->post(sprintf('/streams/version/%d/v4.json', $objectId), $contentsV2);

        $this->assertResponseCode(201);
        $response = json_decode((string)$this->_response->getBody(), true);
        static::assertArraySubset(['version' => 4], $response['data']['meta']);
    }

    /**
     * Test uploadNewVersion with PATCH: latest version is replaced in place.
     *
     * @return void
     */
    public function testUploadReplaceLatestVersion(): void
    {
        $objectId = 10;
        $fileName = 'replaced.txt';
        $contents = 'replacement payload';
        $contentType = 'text/plain';

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->patch(sprintf('/streams/version/%d/%s', $objectId, $fileName), $contents);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);
        static::assertArrayHasKey('data', $response);
        static::assertSame('9e58fa47-db64-4479-a0ab-88a706180d59', $response['data']['id']);
        static::assertEquals(['file_name' => $fileName, 'mime_type' => $contentType], $response['data']['attributes']);
        static::assertArraySubset(
            ['version' => 1, 'hash_sha1' => sha1($contents)],
            $response['data']['meta'],
        );
    }

    /**
     * Test uploadNewVersion with PATCH: identical content is rejected.
     *
     * @return void
     */
    public function testUploadReplaceLatestVersionConflict(): void
    {
        $objectId = 10;
        $contentType = 'text/plain';
        $contents = "Sample uploaded file.\n";

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->patch(sprintf('/streams/version/%d/sample.txt', $objectId), $contents);

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test upload method with `private_url` query.
     *
     * @return void
     */
    public function testUploadPrivateUrl(): void
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

    /**
     * Test uploadNewVersion with PATCH: 404 when no streams exist for object.
     *
     * @return void
     */
    public function testUploadReplaceLatestVersionNotFound(): void
    {
        // Object 2 has no streams in the fixture.
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader() + ['Content-Type' => 'text/plain']);
        $this->patch('/streams/version/2/test.txt', 'some content');

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test uploadNewVersion with PATCH and `private_url` query param.
     *
     * @return void
     */
    public function testUploadReplaceLatestVersionPrivateUrl(): void
    {
        $objectId = 10;
        $fileName = 'replaced-private.txt';
        $contents = 'private content replacement';
        $contentType = 'text/plain';

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader() + ['Content-Type' => $contentType]);
        $this->patch(sprintf('/streams/version/%d/%s?private_url=true', $objectId, $fileName), $contents);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);
        static::assertArrayHasKey('data', $response);
        static::assertTrue($response['data']['meta']['private_url']);
    }
}
