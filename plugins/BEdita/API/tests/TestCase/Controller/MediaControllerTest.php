<?php
declare(strict_types=1);

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

use BEdita\API\Datasource\JsonApiPaginator;
use BEdita\API\Test\TestConstants;
use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\Api\Controller\MediaController
 */
class MediaControllerTest extends IntegrationTestCase
{
    use TestFilesystemTrait;

    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * Generator instance.
     *
     * @var \BEdita\Core\Filesystem\Thumbnail\GlideGenerator
     */
    protected $generator;

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
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->filesystemSetup(true, true);

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
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->filesystemRestore();

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
                        'url' => 'https://static.example.org/thumbs/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif/ef5b382f91ad45aff0e33b89e6677df31fcf6034.jpg',
                    ],
                ],
                14,
            ],
            'svg' => [
                [
                    [
                        'id' => 16,
                        'uuid' => '9b06b2cf-fce7-47e8-b367-a3e5b464ca85',
                        'ready' => true,
                        'url' => 'https://static.example.org/files/9b06b2cf-fce7-47e8-b367-a3e5b464ca85-sample.svg',
                    ],
                ],
                16,
            ],
            'array of IDs, custom preset' => [
                [
                    [
                        'id' => 14,
                        'uuid' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                        'ready' => true,
                        'url' => 'https://static.example.org/thumbs/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif/7712de3f48d7ecb9b473cc12feb34af1af79309e.png',
                    ],
                    [
                        'id' => 10,
                        'uuid' => '9e58fa47-db64-4479-a0ab-88a706180d59',
                        'ready' => false,
                        'acceptable' => false,
                        'message' => 'Unable to generate thumbnail for stream 9e58fa47-db64-4479-a0ab-88a706180d59',
                        'url' => 'https://static.example.org/thumbs/9e58fa47-db64-4479-a0ab-88a706180d59-sample.txt/7712de3f48d7ecb9b473cc12feb34af1af79309e.png',
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
                        'url' => 'https://static.example.org/thumbs/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif/7712de3f48d7ecb9b473cc12feb34af1af79309e.png',
                    ],
                    [
                        'id' => 10,
                        'uuid' => '9e58fa47-db64-4479-a0ab-88a706180d59',
                        'ready' => false,
                        'acceptable' => false,
                        'message' => 'Unable to generate thumbnail for stream 9e58fa47-db64-4479-a0ab-88a706180d59',
                        'url' => 'https://static.example.org/thumbs/9e58fa47-db64-4479-a0ab-88a706180d59-sample.txt/7712de3f48d7ecb9b473cc12feb34af1af79309e.png',
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
     * @param int|int[]|string $id List of IDs.
     * @param array $query Query options.
     * @return void
     * @dataProvider thumbsProvider()
     * @covers ::thumbs()
     * @covers ::getIds()
     * @covers ::fetchProviderThumbs()
     */
    public function testThumbs($expected, $id, array $query = [])
    {
        $this->configRequestHeaders('GET');

        $path = '/media/thumbs';
        if (!is_array($id) && strpos((string)$id, ',') === false) {
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
     * @covers ::thumbs()
     * @covers ::getIds()
     */
    public function testThumbsBothIds()
    {
        $this->configRequestHeaders('GET');
        $this->get('/media/thumbs/1?ids=2,3');

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(400);
        static::assertSame('Cannot specify IDs in both path and query string', Hash::get($body, 'error.title'));
    }

    /**
     * Test thumbnails generation when number of IDs exceeds pagination limits.
     *
     * @return void
     * @covers ::thumbs()
     * @covers ::getIds()
     */
    public function testThumbsTooManyIds()
    {
        $this->configRequestHeaders('GET');
        $this->get('/media/thumbs?ids=' . implode(',', range(1, JsonApiPaginator::MAX_LIMIT + 1)));

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(400);
        static::assertMatchesRegularExpression('/^Cannot generate thumbnails for more than \d+ media at once$/', Hash::get($body, 'error.title'));
    }

    /**
     * Test `thumbs` method when no media IDs are passed.
     *
     * @return void
     * @covers ::thumbs()
     * @covers ::getIds()
     * @covers ::getAvailableIds()
     */
    public function testThumbsNoIds()
    {
        $this->configRequestHeaders('GET');
        $this->get('/media/thumbs');

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(400);
        static::assertSame('Missing IDs to generate thumbnails for', Hash::get($body, 'error.title'));
    }

    /**
     * Test available IDs.
     *
     * @return void
     * @covers ::getAvailableIds()
     */
    public function testAvailableIds()
    {
        $data = [
            'id' => '10',
            'type' => 'files',
            'attributes' => [
                'status' => 'off',
            ],
        ];
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/files/10', json_encode(compact('data')));
        $this->assertResponseCode(200);

        Configure::write('Status.level', 'on');

        $this->configRequestHeaders('GET');
        $this->get('/media/thumbs?ids=10,14');

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $thumbnails = Hash::get((array)$body, 'meta.thumbnails');
        $expected = [
            [
                'id' => 14,
                'uuid' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                'ready' => false,
                'url' => 'https://static.example.org/thumbs/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif/ef5b382f91ad45aff0e33b89e6677df31fcf6034.jpg',
            ],
        ];
        static::assertEquals($expected, $thumbnails);
    }

    /**
     * Test `thumbs` method with provider thumbnails.
     *
     * @return void
     * @covers ::fetchProviderThumbs()
     */
    public function testProviderThumbs()
    {
        // add remote media with provider thumb
        $data = [
            'type' => 'files',
            'attributes' => [
                'provider_thumbnail' => 'https://thumbs.example.org/item.jpg',
                'media_property' => true,
            ],
        ];
        $newId = $this->lastObjectId() + 1;
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/files', json_encode(compact('data')));

        $expected = [
            [
                'id' => 14,
                'uuid' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                'ready' => false,
                'url' => 'https://static.example.org/thumbs/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif/ef5b382f91ad45aff0e33b89e6677df31fcf6034.jpg',
            ],
            [
                'id' => $newId,
                'ready' => true,
                'url' => $data['attributes']['provider_thumbnail'],
            ],
        ];

        $this->configRequestHeaders('GET');
        $this->get(sprintf('/media/thumbs?ids=14,%d', $newId));

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $thumbnails = Hash::get((array)$body, 'meta.thumbnails');
        static::assertEquals($expected, $thumbnails);
    }

    /**
     * Test index method.
     *
     * @return void
     * @coversNothing
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/media',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/media',
                'last' => 'http://api.example.com/media',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'pagination' => [
                    'count' => 6,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 6,
                    'page_size' => 20,
                ],
                'schema' => [
                    'files' => [
                        '$id' => 'http://api.example.com/model/schema/files',
                        'revision' => TestConstants::SCHEMA_REVISIONS['files'],
                    ],
                    'images' => [
                        '$id' => 'http://api.example.com/model/schema/images',
                        'revision' => TestConstants::SCHEMA_REVISIONS['images'],
                    ],
                    'videos' => [
                        '$id' => 'http://api.example.com/model/schema/videos',
                        'revision' => TestConstants::SCHEMA_REVISIONS['videos'],
                    ],
                ],
            ],
            'data' => [
                [
                    'id' => '10',
                    'type' => 'files',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'media-one',
                        'title' => 'first media',
                        'description' => 'media description goes here',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                        'media_property' => true, // inherited custom property
                        'name' => 'My media name',
                        'provider' => null,
                        'provider_uid' => null,
                        'provider_url' => null,
                        'provider_thumbnail' => null,
                        'provider_extra' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2017-03-08T07:09:23+00:00',
                        'modified' => '2017-03-08T08:30:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'media_url' => 'https://static.example.org/files/9e58fa47-db64-4479-a0ab-88a706180d59.txt',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/files/10',
                    ],
                    'relationships' => [
                        'streams' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/10/streams',
                                'self' => 'http://api.example.com/files/10/relationships/streams',
                            ],
                            'data' => [
                               [
                                    'id' => '9e58fa47-db64-4479-a0ab-88a706180d59',
                                    'type' => 'streams',
                               ],
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/10/parents',
                                'self' => 'http://api.example.com/files/10/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/10/translations',
                                'self' => 'http://api.example.com/files/10/relationships/translations',
                            ],
                        ],
                        'inverse_test_abstract' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/10/inverse_test_abstract',
                                'self' => 'http://api.example.com/files/10/relationships/inverse_test_abstract',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '14',
                    'type' => 'files',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'media-two',
                        'title' => 'second media',
                        'description' => 'another media description goes here',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                        'media_property' => null,
                        'name' => 'My other media name',
                        'provider' => null,
                        'provider_uid' => null,
                        'provider_url' => null,
                        'provider_thumbnail' => null,
                        'provider_extra' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2018-03-22T16:42:31+00:00',
                        'modified' => '2018-03-22T16:42:31+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'media_url' => 'https://static.example.org/files/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/files/14',
                    ],
                    'relationships' => [
                        'streams' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/14/streams',
                                'self' => 'http://api.example.com/files/14/relationships/streams',
                            ],
                            'data' => [
                                [
                                    'id' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                                    'type' => 'streams',
                                ],
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/14/parents',
                                'self' => 'http://api.example.com/files/14/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/14/translations',
                                'self' => 'http://api.example.com/files/14/relationships/translations',
                            ],
                        ],
                        'inverse_test_abstract' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/14/inverse_test_abstract',
                                'self' => 'http://api.example.com/files/14/relationships/inverse_test_abstract',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '16',
                    'type' => 'files',
                    'attributes' => [
                        'name' => 'An svg media',
                        'provider' => null,
                        'provider_uid' => null,
                        'provider_url' => null,
                        'provider_thumbnail' => null,
                        'provider_extra' => null,
                        'status' => 'on',
                        'uname' => 'media-svg',
                        'title' => 'svg media',
                        'description' => 'an svg image',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                        'media_property' => false,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2024-03-25T16:11:18+00:00',
                        'modified' => '2024-03-25T16:11:18+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'media_url' => 'https://static.example.org/files/9b06b2cf-fce7-47e8-b367-a3e5b464ca85-sample.svg',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/files/16',
                    ],
                    'relationships' => [
                        'inverse_test_abstract' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/16/inverse_test_abstract',
                                'self' => 'http://api.example.com/files/16/relationships/inverse_test_abstract',
                            ],
                        ],
                        'streams' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/16/streams',
                                'self' => 'http://api.example.com/files/16/relationships/streams',
                            ],
                            'data' => [
                                0 => [
                                    'id' => '9b06b2cf-fce7-47e8-b367-a3e5b464ca85',
                                    'type' => 'streams',
                                ],
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/16/parents',
                                'self' => 'http://api.example.com/files/16/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/16/translations',
                                'self' => 'http://api.example.com/files/16/relationships/translations',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '17',
                    'type' => 'images',
                    'attributes' => [
                        'name' => 'Modern art',
                        'provider' => null,
                        'provider_uid' => null,
                        'provider_url' => null,
                        'provider_thumbnail' => null,
                        'provider_extra' => null,
                        'status' => 'on',
                        'uname' => 'media-modern-art',
                        'title' => 'Modern art',
                        'description' => 'an art piece',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                        'media_property' => false,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2024-06-25T10:11:18+00:00',
                        'modified' => '2024-06-25T10:11:18+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'media_url' => 'https://static.example.org/files/eadc9cd3-b0ae-4e43-9251-9f44bd026793-snow-on-white.jpg',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/images/17',
                    ],
                    'relationships' => [
                        'inverse_test_abstract' => [
                            'links' => [
                                'related' => 'http://api.example.com/images/17/inverse_test_abstract',
                                'self' => 'http://api.example.com/images/17/relationships/inverse_test_abstract',
                            ],
                        ],
                        'streams' => [
                            'links' => [
                                'related' => 'http://api.example.com/images/17/streams',
                                'self' => 'http://api.example.com/images/17/relationships/streams',
                            ],
                            'data' => [
                                0 => [
                                    'id' => 'eadc9cd3-b0ae-4e43-9251-9f44bd026793',
                                    'type' => 'streams',
                                ],
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/images/17/parents',
                                'self' => 'http://api.example.com/images/17/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/images/17/translations',
                                'self' => 'http://api.example.com/images/17/relationships/translations',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '18',
                    'type' => 'images',
                    'attributes' => [
                        'name' => 'Contemporary art',
                        'provider' => null,
                        'provider_uid' => null,
                        'provider_url' => null,
                        'provider_thumbnail' => null,
                        'provider_extra' => null,
                        'status' => 'on',
                        'uname' => 'media-contemporary-art',
                        'title' => 'Contemporary art',
                        'description' => 'an art piece',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                        'media_property' => false,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2024-06-25T10:11:18+00:00',
                        'modified' => '2024-06-25T10:11:18+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'media_url' => 'https://static.example.org/files/7ffcb45e-4cc1-492e-9775-74ee6999503f-snow-on-white.jpg',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/images/18',
                    ],
                    'relationships' => [
                        'inverse_test_abstract' => [
                            'links' => [
                                'related' => 'http://api.example.com/images/18/inverse_test_abstract',
                                'self' => 'http://api.example.com/images/18/relationships/inverse_test_abstract',
                            ],
                        ],
                        'streams' => [
                            'links' => [
                                'related' => 'http://api.example.com/images/18/streams',
                                'self' => 'http://api.example.com/images/18/relationships/streams',
                            ],
                            'data' => [
                                0 => [
                                    'id' => '7ffcb45e-4cc1-492e-9775-74ee6999503f',
                                    'type' => 'streams',
                                ],
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/images/18/parents',
                                'self' => 'http://api.example.com/images/18/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/images/18/translations',
                                'self' => 'http://api.example.com/images/18/relationships/translations',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '19',
                    'type' => 'videos',
                    'attributes' => [
                        'name' => null,
                        'provider' => null,
                        'provider_uid' => null,
                        'provider_url' => null,
                        'provider_thumbnail' => null,
                        'provider_extra' => null,
                        'status' => 'on',
                        'uname' => 'media-funny-video-of-gustavo',
                        'title' => 'Funny video of Gustavo',
                        'description' => 'Gustavo in action!',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                        'media_property' => false,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2024-10-29T12:01:18+00:00',
                        'modified' => '2024-10-29T12:01:18+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'media_url' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/videos/19',
                    ],
                    'relationships' => [
                        'inverse_test_abstract' => [
                            'links' => [
                                'related' => 'http://api.example.com/videos/19/inverse_test_abstract',
                                'self' => 'http://api.example.com/videos/19/relationships/inverse_test_abstract',
                            ],
                        ],
                        'streams' => [
                            'links' => [
                                'related' => 'http://api.example.com/videos/19/streams',
                                'self' => 'http://api.example.com/videos/19/relationships/streams',
                            ],
                            'data' => [],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/videos/19/parents',
                                'self' => 'http://api.example.com/videos/19/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/videos/19/translations',
                                'self' => 'http://api.example.com/videos/19/relationships/translations',
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => '9e58fa47-db64-4479-a0ab-88a706180d59',
                    'type' => 'streams',
                    'attributes' => [
                        'file_name' => 'sample.txt',
                        'mime_type' => 'text/plain',
                    ],
                    'meta' => [
                        'version' => 1,
                        'file_size' => 22,
                        'hash_md5' => '4803449f89ea5eeb42efa1b2889dd770',
                        'hash_sha1' => '283b1edb6f051ef1d1770cd9bb08e75066b437e6',
                        'width' => null,
                        'height' => null,
                        'duration' => null,
                        'created' => '2017-06-22T12:37:41+00:00',
                        'modified' => '2017-06-22T12:37:41+00:00',
                        'url' => 'https://static.example.org/files/9e58fa47-db64-4479-a0ab-88a706180d59.txt',
                        'file_metadata' => null,
                        'private_url' => false,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/streams/9e58fa47-db64-4479-a0ab-88a706180d59',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/streams/9e58fa47-db64-4479-a0ab-88a706180d59/object',
                                'self' => 'http://api.example.com/streams/9e58fa47-db64-4479-a0ab-88a706180d59/relationships/object',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                    'type' => 'streams',
                    'attributes' => [
                        'file_name' => 'bedita-logo-gray.gif',
                        'mime_type' => 'image/gif',
                    ],
                    'meta' => [
                        'version' => 1,
                        'file_size' => 927,
                        'hash_md5' => 'a714dbb31ca89d5b1257245dfa5c5153',
                        'hash_sha1' => '444b2b42b48b0b815d70f6648f8a7a23d5faf54b',
                        'width' => 118,
                        'height' => 52,
                        'duration' => null,
                        'created' => '2018-03-22T15:58:47+00:00',
                        'modified' => '2018-03-22T15:58:47+00:00',
                        'url' => 'https://static.example.org/files/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif',
                        'file_metadata' => null,
                        'private_url' => false,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/streams/6aceb0eb-bd30-4f60-ac74-273083b921b6',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/streams/6aceb0eb-bd30-4f60-ac74-273083b921b6/object',
                                'self' => 'http://api.example.com/streams/6aceb0eb-bd30-4f60-ac74-273083b921b6/relationships/object',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '9b06b2cf-fce7-47e8-b367-a3e5b464ca85',
                    'type' => 'streams',
                    'attributes' => [
                        'file_name' => 'sample.svg',
                        'mime_type' => 'image/svg+xml',
                    ],
                    'meta' => [
                        'version' => 1,
                        'file_size' => 461,
                        'hash_md5' => '',
                        'hash_sha1' => '',
                        'width' => null,
                        'height' => null,
                        'duration' => null,
                        'created' => '2024-03-25T16:11:18+00:00',
                        'modified' => '2024-03-25T16:11:18+00:00',
                        'url' => 'https://static.example.org/files/9b06b2cf-fce7-47e8-b367-a3e5b464ca85-sample.svg',
                        'file_metadata' => null,
                        'private_url' => false,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/streams/9b06b2cf-fce7-47e8-b367-a3e5b464ca85',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/streams/9b06b2cf-fce7-47e8-b367-a3e5b464ca85/object',
                                'self' => 'http://api.example.com/streams/9b06b2cf-fce7-47e8-b367-a3e5b464ca85/relationships/object',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 'eadc9cd3-b0ae-4e43-9251-9f44bd026793',
                    'type' => 'streams',
                    'attributes' => [
                        'file_name' => 'snow-on-white.jpg',
                        'mime_type' => 'image/jpeg',
                    ],
                    'meta' => [
                        'version' => 1,
                        'file_size' => 140910,
                        'hash_md5' => '04fd3cc862a142c114c6f7822996207a',
                        'hash_sha1' => 'e3d5556baf1c257d10a146c0ebf84a2ab99c7437',
                        'width' => 8000,
                        'height' => 4500,
                        'duration' => null,
                        'created' => '2024-06-25T10:11:18+00:00',
                        'modified' => '2024-06-25T10:11:18+00:00',
                        'url' => 'https://static.example.org/files/eadc9cd3-b0ae-4e43-9251-9f44bd026793-snow-on-white.jpg',
                        'file_metadata' => null,
                        'private_url' => false,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/streams/eadc9cd3-b0ae-4e43-9251-9f44bd026793',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/streams/eadc9cd3-b0ae-4e43-9251-9f44bd026793/object',
                                'self' => 'http://api.example.com/streams/eadc9cd3-b0ae-4e43-9251-9f44bd026793/relationships/object',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '7ffcb45e-4cc1-492e-9775-74ee6999503f',
                    'type' => 'streams',
                    'attributes' => [
                        'file_name' => 'snow-on-white.jpg',
                        'mime_type' => 'image/jpeg',
                    ],
                    'meta' => [
                        'version' => 1,
                        'file_size' => 140910,
                        'hash_md5' => '04fd3cc862a142c114c6f7822996207a',
                        'hash_sha1' => 'e3d5556baf1c257d10a146c0ebf84a2ab99c7437',
                        'width' => null,
                        'height' => null,
                        'duration' => null,
                        'created' => '2024-06-25T10:11:18+00:00',
                        'modified' => '2024-06-25T10:11:18+00:00',
                        'url' => 'https://static.example.org/files/7ffcb45e-4cc1-492e-9775-74ee6999503f-snow-on-white.jpg',
                        'file_metadata' => null,
                        'private_url' => false,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/streams/7ffcb45e-4cc1-492e-9775-74ee6999503f',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/streams/7ffcb45e-4cc1-492e-9775-74ee6999503f/object',
                                'self' => 'http://api.example.com/streams/7ffcb45e-4cc1-492e-9775-74ee6999503f/relationships/object',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/media');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test single view method.
     *
     * @return void
     * @coversNothing
     */
    public function testSingleView()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/files/14',
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'schema' => [
                    'files' => [
                        '$id' => 'http://api.example.com/model/schema/files',
                        'revision' => TestConstants::SCHEMA_REVISIONS['files'],
                    ],
                ],
            ],
            'data' => [
                'id' => '14',
                'type' => 'files',
                'attributes' => [
                    'status' => 'on',
                    'uname' => 'media-two',
                    'title' => 'second media',
                    'description' => 'another media description goes here',
                    'body' => null,
                    'extra' => null,
                    'files_property' => null,
                    'lang' => 'en',
                    'publish_start' => null,
                    'publish_end' => null,
                    'media_property' => null,
                    'name' => 'My other media name',
                    'provider' => null,
                    'provider_uid' => null,
                    'provider_url' => null,
                    'provider_thumbnail' => null,
                    'provider_extra' => null,
                    'default_val_property' => null,
                ],
                'meta' => [
                    'locked' => false,
                    'created' => '2018-03-22T16:42:31+00:00',
                    'modified' => '2018-03-22T16:42:31+00:00',
                    'published' => null,
                    'created_by' => 1,
                    'modified_by' => 1,
                    'media_url' => 'https://static.example.org/files/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif',
                ],
                'relationships' => [
                    'streams' => [
                        'links' => [
                            'related' => 'http://api.example.com/files/14/streams',
                            'self' => 'http://api.example.com/files/14/relationships/streams',
                        ],
                        'data' => [
                            [
                                'id' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                                'type' => 'streams',
                            ],
                        ],
                    ],
                    'parents' => [
                        'links' => [
                            'related' => 'http://api.example.com/files/14/parents',
                            'self' => 'http://api.example.com/files/14/relationships/parents',
                        ],
                    ],
                    'translations' => [
                        'links' => [
                            'related' => 'http://api.example.com/files/14/translations',
                            'self' => 'http://api.example.com/files/14/relationships/translations',
                        ],
                    ],
                    'inverse_test_abstract' => [
                        'links' => [
                            'related' => 'http://api.example.com/files/14/inverse_test_abstract',
                            'self' => 'http://api.example.com/files/14/relationships/inverse_test_abstract',
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                    'type' => 'streams',
                    'attributes' => [
                        'file_name' => 'bedita-logo-gray.gif',
                        'mime_type' => 'image/gif',
                    ],
                    'meta' => [
                        'version' => 1,
                        'file_size' => 927,
                        'hash_md5' => 'a714dbb31ca89d5b1257245dfa5c5153',
                        'hash_sha1' => '444b2b42b48b0b815d70f6648f8a7a23d5faf54b',
                        'width' => 118,
                        'height' => 52,
                        'duration' => null,
                        'created' => '2018-03-22T15:58:47+00:00',
                        'modified' => '2018-03-22T15:58:47+00:00',
                        'url' => 'https://static.example.org/files/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif',
                        'file_metadata' => null,
                        'private_url' => false,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/streams/6aceb0eb-bd30-4f60-ac74-273083b921b6',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/streams/6aceb0eb-bd30-4f60-ac74-273083b921b6/object',
                                'self' => 'http://api.example.com/streams/6aceb0eb-bd30-4f60-ac74-273083b921b6/relationships/object',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/files/14');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }
}
