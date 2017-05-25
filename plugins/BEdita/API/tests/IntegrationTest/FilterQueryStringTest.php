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
use BEdita\Core\Utility\Database;
use Cake\Utility\Hash;

/**
 * Test Query String `filter`.
 *
 * @coversNothing
 */
class FilterQueryStringTest extends IntegrationTestCase
{
    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.date_ranges',
        'plugin.BEdita/Core.locations',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
    ];

    /**
     * Data provider for `testFilterDate` test case.
     *
     * @return array
     */
    public function filterDateProvider()
    {
        return [
            'simple' => [
               'filter[date_ranges][start_date][gt]=2017-01-01',
               1
            ],
            'none' => [
               'filter[date_ranges][end_date][le]=2017-01-01',
               0
            ],
            'combined' => [
               'filter[date_ranges][start_date][gt]=2017-01-01&filter[date_ranges][end_date][lt]=2017-04-01',
               1
            ],
            'absurd' => [
               'filter[date_ranges][start_date][ge]=2018-01-01&filter[date_ranges][end_date][le]=2017-01-01',
               0
            ],
        ];
    }

    /**
     * Test 'date_ranges` filter
     *
     * @param $query string URL with query filter string
     * @param $expected int Number of objects id expected in response
     * @param $endpoint string Endpoint to use
     * @return void
     *
     * @dataProvider filterDateProvider
     * @coversNothing
     */
    public function testFilterDate($query, $expected, $endpoint = '/events')
    {
        $this->configRequestHeaders();
        $this->get($endpoint . '?' . $query);
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, count($result['data']));
    }

    /**
     * Data provider for `testFilterGeo` test case.
     *
     * @return array
     */
    public function filterGeoProvider()
    {
        return [
            'simple' => [
               'filter[geo][center][]=44.4944183&filter[geo][center][]=11.3464055',
               [
                   0,
               ]
            ],
            'array' => [
               'filter[geo][center]=44.4944183,11.3464055',
               [
                   0,
               ]
            ]
        ];
    }

    /**
     * Test 'geo` filter
     *
     * @param $query string URL with query filter string
     * @param $expected array Distance expected in response for every item
     * @param $endpoint string Endpoint to use
     *
     * @dataProvider filterGeoProvider
     * @coversNothing
     */
    public function testFilterGeo($query, $expected, $endpoint = '/locations')
    {
        $this->configRequestHeaders();
        $this->get($endpoint . '?' . $query);
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertContentType('application/vnd.api+json');
        if (!Database::supportedVersion(['vendor' => 'mysql', 'version' => '5.7'])) {
            $this->assertResponseCode(400);
        } else {
            $this->assertResponseCode(200);
            static::assertSame(count($expected), count($result['data']));
            $resultDistance = Hash::extract($result['data'], '{n}.meta.extra.distance');
            static::assertSame($expected, $resultDistance);
        }
    }

    /**
     * Data provider for `testBadFilter` test case.
     *
     * @return array
     */
    public function badFilterProvider()
    {
        return [
            'simple' => [
               'filter[geo][center]=44.4944183,11.3464055',
               '/documents'
            ],
            'bad' => [
               'filter[cool_filter]=top',
            ],
            'banana' => [
               'filter[geo][banana]=44.4944183,11.3464055',
               '/locations'
            ],
            'banana2' => [
               'filter[date_ranges][banana][gt]=2017-01-01',
               '/events'
            ],
        ];
    }

    /**
     * Test bad filters
     *
     * @param $query string URL with query filter string
     * @param $endpoint string Endpoint to use
     * @return void
     *
     * @dataProvider badFilterProvider
     * @coversNothing
     */
    public function testBadFilter($query, $endpoint = '/events')
    {
        $this->configRequestHeaders();
        $this->get($endpoint . '?' . $query);
        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test finder of object types by relation.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testFindByRelation()
    {
        $this->configRequestHeaders();

        $this->get('/object_types?filter[by_relation][name]=test');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertCount(2, $result['data']);
        static::assertArrayNotHasKey('_matchingData', $result['data'][0]['attributes']);
    }

    /**
     * Test finder of objects by query string.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testFindQuery()
    {
        $expected = [2, 3, 9];
        $this->configRequestHeaders();

        $this->get('/objects?filter[query]=here');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals($expected, Hash::extract($result['data'], '{n}.id'), '', 0, 10, true);
    }

    /**
     * Test finder of locations by query string.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testFindQueryLocations()
    {
        $expected = [8];
        $this->configRequestHeaders();

        $this->get('/locations?filter[query]=bologna');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals($expected, Hash::extract($result['data'], '{n}.id'), '', 0, 10, true);
    }

    /**
     * Test finder of users by query string.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testFindQueryUsers()
    {
        $expected = [5];
        $this->configRequestHeaders();

        $this->get('/users?filter[query]=second');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals($expected, Hash::extract($result['data'], '{n}.id'), '', 0, 10, true);
    }

    /**
     * Test finder of objects by query string using shorthand `?q=` query parameter.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testFindQueryAlias()
    {
        $expected = [2, 3, 9];
        $this->configRequestHeaders();

        $this->get('/objects?q=here');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals($expected, Hash::extract($result['data'], '{n}.id'), '', 0, 10, true);
    }
}
