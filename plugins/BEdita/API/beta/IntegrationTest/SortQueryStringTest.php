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
use Cake\Network\Exception\BadRequestException;
use Cake\Utility\Hash;

/**
 * Test Query String `sort`
 */
class SortQueryStringTest extends IntegrationTestCase
{
    /**
     * Provider for testSortObjects()
     *
     * @return array
     */
    public function sortProvider()
    {
        return [
            'simpleObject' => [
                200,
                '/documents',
                'title',
            ],
            'usersSortByObjectsField' => [
                200,
                '/users',
                'title',
            ],
            'usersSortByProfilesField' => [
                200,
                '/users',
                'email',
            ],
            'notValidField' => [
                400,
                '/users',
                'not_valid_field'
            ],
            'roles' => [
                200,
                '/roles',
                'name'
            ]
        ];
    }

    /**
     * Test sort on different endpoints
     *
     * @param int $expected The HTTP status code expected
     * @param string $endpoint The object type
     * @param string $sort The field on which sort
     * @return void
     *
     * @dataProvider sortProvider
     */
    public function testSort($expected, $endpoint, $sort)
    {
        // sort asc
        $this->configRequestHeaders();
        $url = sprintf('%s?sort=%s', $endpoint, $sort);
        $this->get($url);
        $this->assertResponseCode($expected);
        if ($expected === 200) {
            $result = json_decode((string)$this->_response->getBody(), true);
            $fields = $sortedFields = Hash::extract($result, 'data.{n}.attributes.' . $sort);
            sort($sortedFields);
            $this->assertEquals($fields, $sortedFields);
        }

        // sort desc
        $this->configRequestHeaders();
        $url = sprintf('/%s?sort=-%s', $endpoint, $sort);
        $this->get($url);
        $this->assertResponseCode($expected);
        if ($expected === 200) {
            $result = json_decode((string)$this->_response->getBody(), true);
            $fields = Hash::extract($result, 'data.{n}.attributes.' . $sort);
            $sortedFields = array_reverse($sortedFields);
            $this->assertEquals($fields, $sortedFields);
        }
    }
}
