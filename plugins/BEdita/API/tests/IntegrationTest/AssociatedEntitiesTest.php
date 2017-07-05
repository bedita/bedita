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

use BEdita\API\TestSuite\IntegrationTestCase;

/**
 * Test CRUD operations on objects with associated entities
 *
 */
class AssociatedEntitiesTest extends IntegrationTestCase
{
    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.date_ranges',
        'plugin.BEdita/Core.locations',
    ];

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
                            'start_date' => '2017-04-01T00:00:00+00:00',
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
                            'start_date' => '2017-12-31T23:59:59Z',
                            'end_date' => '2018-01-01'
                        ],
                    ]
                ],
                [
                    'title' => 'Happy new year!',
                    'date_ranges' => [
                        [
                            'start_date' => '2017-03-08T00:00:00+00:00',
                            'end_date' => '2018-01-02 10:30'
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
        $lastId = $this->lastObjectId();

        // ADD
        $data = [
            'type' => $type,
            'attributes' => $attributes,
        ];

        $authHeader = $this->getUserAuthHeader();

        $this->configRequestHeaders('POST', $authHeader);
        $endpoint = '/' . $type;
        $this->post($endpoint, json_encode(compact('data')));
        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        // VIEW
        $this->configRequestHeaders();
        $lastId++;
        $this->get("/$type/$lastId");
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $resultDates = $result['data']['attributes']['date_ranges'];
        $expectedDates = $attributes['date_ranges'];
        static::assertEquals(count($resultDates), count($expectedDates));
        $count = count($expectedDates);
        for ($i = 0; $i < $count; $i++) {
            foreach ($expectedDates[$i] as $k => $d) {
                $found = $resultDates[$i][$k];
                $exp = new \DateTime($d);
                $exp = $exp->format('Y-m-d\TH:i:s+00:00');
                static::assertEquals($found, $exp);
            }
        }

        // EDIT
        $data = [
            'id' => "$lastId",
            'type' => $type,
            'attributes' => $modified,
        ];
        $this->configRequestHeaders('PATCH', $authHeader);
        $this->patch("/$type/$lastId", json_encode(compact('data')));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        // DELETE
        $this->configRequestHeaders('DELETE', $authHeader);
        $this->delete("/$type/$lastId");
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');

        // EMPTY TRASH
        $this->configRequestHeaders('DELETE', $authHeader);
        $this->delete("/trash/$lastId");
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
    }
}
