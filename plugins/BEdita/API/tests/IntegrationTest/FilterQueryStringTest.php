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

use Cake\I18n\Time;

/**
 * Test Query String `filter`
 */
class FilterQueryStringTest extends ApiIntegrationTestCase
{

    /**
     * Data provider for `testFilterDate`
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
        $this->assertEquals($expected, count($result['data']));
    }
}
