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
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test Query String `sort`
 */
#[CoversNothing]
class SortQueryStringTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.DateRanges',
    ];

    /**
     * Provider for testSortObjects()
     *
     * @return array
     */
    public static function sortProvider(): array
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
                'not_valid_field',
            ],
            'roles' => [
                200,
                '/roles',
                'name',
            ],
            'eventsSpecialSort' => [
                200,
                '/events',
                'date_ranges_max_end_date',
            ],
            'customPropSort' => [
                200,
                '/files',
                'media_property',
                true,
            ],
        ];
    }

    /**
     * Test sort on different endpoints
     *
     * @param int $expected The HTTP status code expected
     * @param string $endpoint The object type
     * @param string $sort The field on which sort
     * @param bool $customProps Whether there's a custom properties sort
     * @return void
     */
    #[DataProvider('sortProvider')]
    public function testSort($expected, $endpoint, $sort, $customProps = false): void
    {
        $sortedFields = [];
        if ($customProps) {
            $driver = ConnectionManager::get('default')->getDriver();
            $this->skipUnless(($driver instanceof Mysql) || ($driver instanceof Postgres));
        }
        // sort asc
        $this->configRequestHeaders();
        $url = sprintf('%s?sort=%s', $endpoint, $sort);
        $this->get($url);
        $fields = $sortedFields = [];
        $this->assertResponseCode($expected);
        if ($expected === 200) {
            $result = json_decode((string)$this->_response->getBody(), true);
            $fields = $sortedFields = Hash::extract($result, 'data.{n}.attributes.' . $sort);
            sort($sortedFields);
            $this->assertEquals($fields, $sortedFields);
        }

        // sort desc
        $this->configRequestHeaders();
        $url = sprintf('%s?sort=-%s', $endpoint, $sort);
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
