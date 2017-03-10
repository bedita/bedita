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
namespace BEdita\API\Test\IntegrationTest;

use BEdita\Core\State\CurrentApplication;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * Test CRUD operations on objects with associated entities
 *
 */
class AssociatedEntitiesTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.date_ranges',
        'plugin.BEdita/Core.locations',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        CurrentApplication::setFromApiKey(API_KEY);
    }

    /**
     * Data provider for `testEventAssoc`
     */
    public function eventAssocProvider()
    {
        return [
            'moreDates' => [
                [
                    'title' => 'My Event',
                    'date_ranges' => [
                        [
                            'start_date' => '2017-03-01 12:12:12',
                            'end_date' => '2017-04-01 12:12:12',
                        ],
                        [
                            'start_date' => '2017-04-01 12:12:12',
                        ],
                    ],
                ],
                [],
            ],
            'noDates' => [
                [
                    'title' => 'My Event',
                    'date_ranges' => []
                ],
                [
                    'title' => 'Same Event',
                ],
            ],
            'otherDates' => [
                [
                    'title' => 'New years eve',
                    'date_ranges' => [
                        [
                            'start_date' => '2017-12-31T23:59:59',
                        ],
                    ]
                ],
                [
                    'title' => 'Happy new year!',
                    'date_ranges' => [
                        [
                            'start_date' => '2018-01-01',
                        ],
                    ]
                ]
            ],
        ];
    }

    /**
     * Test CRUD on Events with associated DateRanges entities
     *
     * @param $attributes array Event data to insert
     * @param $modified array Attributes to modify
     * @dataProvider eventAssocProvider
     * @coversNothing
     */
    public function testEventAssoc($attributes, $modified)
    {
        $type = 'events';
        $lastObject = TableRegistry::get('Objects')->find()->select('id')->order(['id' => 'DESC'])->first();
        $lastId = $lastObject->id;

        // ADD
        $data = [
            'type' => $type,
            'attributes' => $attributes,
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $endpoint = '/' . $type;
        $this->post($endpoint, json_encode(compact('data')));
        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        // VIEW
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $lastId++;
        $this->get("/$type/$lastId");
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $resultDates = $result['data']['attributes']['date_ranges'];
        $expectedDates = $attributes['date_ranges'];
        $this->assertEquals(count($resultDates), count($expectedDates));
        $count = count($resultDates);
        for ($i = 0; $i < $count; $i++) {
            foreach ($resultDates[$i] as $k => $d) {
                if (!empty($d)) {
                    $exp = Time::parse($expectedDates[$i][$k])->jsonSerialize();
                    $this->assertEquals($d, $exp);
                }
            }
        }

        // EDIT
        $data = [
            'id' => "$lastId",
            'type' => $type,
            'attributes' => $modified,
        ];
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch("/$type/$lastId", json_encode(compact('data')));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        // DELETE
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->delete("/$type/$lastId");
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');

        // EMPTY TRASH
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->delete("/trash/$lastId");
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
    }
}
