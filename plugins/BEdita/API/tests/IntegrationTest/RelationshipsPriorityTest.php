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
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Test relationships priority.
 */
class RelationshipsPriorityTest extends IntegrationTestCase
{
    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        FilesystemRegistry::setConfig(Configure::read('Filesystem'));
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        FilesystemRegistry::dropAll();
        parent::tearDown();
    }

    /**
     * Data provider for `testPriority`
     *
     * @return array
     */
    public function priorityProvider(): array
    {
        return [
            'ok' => [
                [
                    [
                        'priority' => 1,
                        'inv_priority' => 1,
                    ],
                ],
                '/events/9/relationships/test_abstract',
                [
                    [
                        'id' => '10',
                        'type' => 'files',
                    ],
                ],
                '/events/9/test_abstract',
            ],
            'inverse' => [
                [
                    [
                        'priority' => 3,
                        'inv_priority' => 2,
                    ],
                ],
                '/documents/3/relationships/inverse_test',
                [
                    [
                        'id' => '2',
                        'type' => 'documents',
                        'meta' => [
                            'relation' => [
                                'priority' => 3,
                            ]
                        ]
                    ],
                ],
                '/documents/3/inverse_test',
            ],
        ];
    }

    /**
     * Test relationships priority set via API.
     *
     * @return void
     *
     * @dataProvider priorityProvider
     * @coversNothing
     */
    public function testPriority(array $expected, string $saveEndpoint, array $data, string $readEndpoint): void
    {
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post($saveEndpoint, json_encode(compact('data')));
        $this->assertResponseCode(200);

        $this->configRequestHeaders('GET');
        $this->get($readEndpoint);
        $this->assertResponseCode(200);

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertResponseNotEmpty();

        $relation = Hash::combine($body, 'data.{n}.id', 'data.{n}.meta.relation');
        $ids = Hash::extract($data, '{n}.id');
        $result = [];
        foreach ($ids as $id) {
            $v = $relation[$id];
            unset($v['params']);
            $result[] = $v;
        }

        static::assertEquals($expected, $result);
    }
}
