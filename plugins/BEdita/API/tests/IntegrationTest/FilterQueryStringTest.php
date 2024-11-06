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
use Cake\Database\Driver\Postgres;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Test Query String `filter`.
 *
 * @coversNothing
 */
class FilterQueryStringTest extends IntegrationTestCase
{
    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.DateRanges',
    ];

    /**
     * Geometry support for current connection.
     *
     * @var bool
     */
    private static $geoSupport;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        if (!isset(static::$geoSupport)) {
            static::$geoSupport = TableRegistry::getTableLocator()
                ->get('Locations')
                ->checkGeoSupport();
        }
    }

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
               1,
            ],
            'simple 2' => [
                'filter[date_ranges][from_date]=2017-01-01T14:00:00',
                1,
             ],
             'simple 3' => [
                'filter[date_ranges][from_date]=2017-03-08T21:41:00',
                0,
             ],
             'none' => [
               'filter[date_ranges][end_date][le]=2017-01-01',
               0,
            ],
            'none 2' => [
                'filter[date_ranges][to_date]=2017-01-01',
                0,
             ],
             'combined' => [
               'filter[date_ranges][start_date][gt]=2017-01-01&filter[date_ranges][end_date][lt]=2017-04-01',
               1,
            ],
            'absurd' => [
               'filter[date_ranges][start_date][ge]=2018-01-01&filter[date_ranges][end_date][le]=2017-01-01',
               0,
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
               ],
            ],
            'array' => [
               'filter[geo][center]=44.4944183,11.3464055',
               [
                   0,
               ],
            ],
        ];
    }

    /**
     * Test 'geo` filter
     *
     * @param $query string URL with query filter string
     * @param $expected array Distance expected in response for every item
     * @param $endpoint string Endpoint to use
     * @dataProvider filterGeoProvider
     * @coversNothing
     */
    public function testFilterGeo($query, $expected, $endpoint = '/locations')
    {
        $this->configRequestHeaders();
        $this->get($endpoint . '?' . $query);
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertContentType('application/vnd.api+json');

        if (!static::$geoSupport) {
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
               '/documents',
            ],
            'bad' => [
               'filter[cool_filter]=top',
                '/events',
            ],
            'banana' => [
               'filter[geo][banana]=44.4944183,11.3464055',
               '/locations',
            ],
            'banana2' => [
               'filter[date_ranges][banana][gt]=2017-01-01',
               '/events',
            ],
        ];
    }

    /**
     * Test bad filters
     *
     * @param $query string URL with query filter string
     * @param $endpoint string Endpoint to use
     * @return void
     * @dataProvider badFilterProvider
     * @coversNothing
     */
    public function testBadFilter($query, $endpoint)
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
     * @coversNothing
     */
    public function testFindByRelation()
    {
        $this->configRequestHeaders();

        $this->get('/model/object_types?filter[by_relation][name]=test');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertCount(2, $result['data']);
        static::assertArrayNotHasKey('_matchingData', $result['data'][0]['attributes']);
    }

    /**
     * Test finder of object types by parent name.
     *
     * @return void
     * @coversNothing
     */
    public function testFindParent()
    {
        $this->configRequestHeaders();

        $this->get('/model/object_types?filter[parent]=media');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertCount(3, $result['data']);
    }

    /**
     * Data provider for `testSearchFilter` test case.
     *
     * @return array
     */
    public function searchFilterProvider()
    {
        return [
            'here' => [
                '/objects?filter[query]=here',
                [
                    '2',
                    '3',
                    '9',
                    '10',
                    '14',
                ],
            ],
            'locations' => [
                '/locations?filter[query]=bologna',
                [
                    '8',
                ],
                true,
            ],
            'users' => [
                '/users?filter[query]=second',
                [
                    '5',
                ],
            ],
            'filter role id' => [
                '/users?filter[roles]=1',
                [
                    '1',
                ],
            ],
            'filter roles ids' => [
                '/users?filter[roles][]=1&filter[roles][]=2',
                [
                    '1',
                    '5',
                ],
            ],
            'role name' => [
                '/users?filter[roles]=first role',
                [
                   '1',
                ],
            ],
            'role name (multiple)' => [
                '/users?filter[roles]=first role,second role',
                [
                   '1',
                   '5',
                ],
            ],
            'here2' => [
                '/objects?q=here',
                [
                    '2',
                    '3',
                    '9',
                    '10',
                    '14',
                ],
            ],
            'roles' => [
                '/roles?q=first',
                [
                    '1',
                ],
            ],
            'object_types' => [
                '/model/object_types?filter[query]=profile',
                [
                    '3',
                ],
            ],
            'relations' => [
                '/model/relations?q=another',
                [
                    '2',
                ],
            ],
            'properties' => [
                '/model/properties?filter[query]=another',
                [
                    '1',
                    '2',
                    '3',
                    '4',
                    '5',
                    '6',
                ],
            ],
            'translations' => [
                '/translations?filter[query]=ici',
                [
                    '2',
                ],
            ],
            'annotations' => [
                '/annotations?q=ipsum',
                [
                    '1',
                ],
            ],
            'categories' => [
                '/model/categories?filter[query]=second',
                [
                    '2',
                ],
            ],
            'tags' => [
                '/model/tags?q=first',
                [
                    '1',
                ],
            ],
        ];
    }

    /**
     * Test finder of objects by search string using `filter[query]` or `q`.
     *
     * @return void
     * @param string $url Url string.
     * @param array $expected Expected result.
     * @param bool $caseInsensitive Whether test case relies on case-insensitive comparison.
     * @dataProvider searchFilterProvider
     * @coversNothing
     */
    public function testSearchFilter($url, $expected, bool $caseInsensitive = false)
    {
        if ($caseInsensitive && ConnectionManager::get('default')->getDriver() instanceof Postgres) {
            static::markTestSkipped('Case-insensitive test cases are skipped on Postgres');
        }

        $this->configRequestHeaders();
        $this->get($url);
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals($expected, Hash::extract($result['data'], '{n}.id'), '');
        static::assertEqualsCanonicalizing($expected, Hash::extract($result['data'], '{n}.id'), '');
        static::assertEqualsWithDelta($expected, Hash::extract($result['data'], '{n}.id'), 0, '');
    }

    /**
     * Test search users by username.
     *
     * @coversNothing
     */
    public function testSearchUsername()
    {
        // add new user
        $data = [
            'type' => 'users',
            'attributes' => [
                'username' => 'gustavo',
            ],
        ];
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/users', json_encode(compact('data')));
        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $this->configRequestHeaders();
        $this->get('/users?filter[query]=gustavo');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals([(string)$this->lastObjectId()], Hash::extract($result['data'], '{n}.id'), '');
        static::assertEqualsCanonicalizing([(string)$this->lastObjectId()], Hash::extract($result['data'], '{n}.id'), '');
        static::assertEqualsWithDelta([(string)$this->lastObjectId()], Hash::extract($result['data'], '{n}.id'), 0, '');
    }

    /**
     * Data provider for `testTypeFilter` test case.
     *
     * @return array
     */
    public function typeFilterProvider()
    {
        return [
            'simple' => [
               'filter[type]=users',
               [
                   '1',
                   '5',
               ],
            ],
            'exclude' => [
               'filter[type][ne]=documents',
               [
                   '1',
                   '4',
                   '5',
                   '8',
                   '9',
                   '10',
                   '11',
                   '12',
                   '13',
                   '14',
                   '16',
                   '17',
                   '18',
                   '19',
               ],
            ],
            'multi' => [
               'filter[type][]=events&filter[type][]=locations',
               [
                   '8',
                   '9',
               ],
            ],
        ];
    }

    /**
     * Test `filter[type]` query string.
     *
     * @param string $query Query string.
     * @param array $expected Expected result.
     * @return void
     * @dataProvider typeFilterProvider
     * @coversNothing
     */
    public function testTypeFilter($query, $expected)
    {
        $this->configRequestHeaders();

        $this->get("/objects?$query");
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals($expected, Hash::extract($result['data'], '{n}.id'), '');
        static::assertEqualsCanonicalizing($expected, Hash::extract($result['data'], '{n}.id'), '');
        static::assertEqualsWithDelta($expected, Hash::extract($result['data'], '{n}.id'), 0, '');
    }

    /**
     * Data provider for `testFieldsFilter` test case.
     *
     * @return array
     */
    public function fieldsFilterProvider()
    {
        return [
            'simple' => [
                '/objects',
                'filter[uname]=title-one',
                [
                   '2',
                ],
            ],
            'boolean' => [
                '/model/object_types',
                'filter[is_abstract]=true',
                [
                   '1',
                   '8',
                ],
            ],
            'users' => [
                '/users',
                'filter[email]=second.user@example.com',
                [
                   '5',
                ],
            ],
            'status' => [
                '/documents',
                'filter[status]=off',
                [],
            ],
            'emptyRoles' => [
                '/roles',
                'filter[name]=gustavo',
                [
                ],
            ],
            'profileNameNull' => [
                '/profiles',
                'filter[name][null]=1',
                [
                ],
            ],
            'profileNameNotNull' => [
                '/profiles',
                'filter[name][null]=0',
                [
                    '4',
                 ],
            ],
            'config name' => [
                '/config',
                'filter[name]=appVal',
                [
                    '11',
                ],
            ],
        ];
    }

    /**
     * Test `filter[{field}]` query string.
     *
     * @param string $endpoint Endpoint.
     * @param string $query Query string.
     * @param array $expected Expected results ids.
     * @return void
     * @dataProvider fieldsFilterProvider
     * @coversNothing
     */
    public function testFieldsFilter($endpoint, $query, $expected)
    {
        $this->configRequestHeaders();

        $this->get("$endpoint?$query");
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals($expected, Hash::extract($result['data'], '{n}.id'), '');
        static::assertEqualsCanonicalizing($expected, Hash::extract($result['data'], '{n}.id'), '');
        static::assertEqualsWithDelta($expected, Hash::extract($result['data'], '{n}.id'), 0, '');
    }

    /**
     * Test `/folders?filter[mine]`.
     *
     * @coversNothing
     */
    public function testMineFilter()
    {
        $this->configRequestHeaders('GET', $this->getUserAuthHeader('second user', 'password2'));
        $this->get('/users?filter[mine]');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals([5], Hash::extract($result['data'], '{n}.id'));
    }

    /**
     * Test `/folders?filter[history_editor]`.
     *
     * @coversNothing
     */
    public function testHistoryEditorFilter()
    {
        $this->configRequestHeaders('GET', $this->getUserAuthHeader('second user', 'password2'));
        $this->get('/documents?filter[history_editor]');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals([2], Hash::extract($result['data'], '{n}.id'));

        $this->configRequestHeaders('GET', $this->getUserAuthHeader('second user', 'password2'));
        $this->get('/documents?filter[history_editor]=1');
        $result = json_decode((string)$this->_response->getBody(), true);

        static::assertArrayHasKey('data', $result);
        static::assertEquals([2], Hash::extract($result['data'], '{n}.id'));
    }

    /**
     * Data provider for `testTrashFilter` test case.
     *
     * @return array
     */
    public function trashFilterProvider()
    {
        return [
            'simple' => [
               'filter[type]=documents',
               [
                   '6',
                   '7',
               ],
            ],
            'exclude' => [
               'filter[type][ne]=documents',
               [
               ],
            ],
            'query1' => [
               'filter[query]=one',
               [
                   '6',
               ],
            ],
            'query2' => [
               'q=two',
               [
                   '7',
               ],
            ],
        ];
    }

    /**
     * Test filters on /trash endpoint.
     *
     * @param string $query Query string.
     * @param array $expected Expected result.
     * @return void
     * @dataProvider trashFilterProvider
     * @coversNothing
     */
    public function testTrashFilter($query, $expected)
    {
        $this->configRequestHeaders();

        $this->get("/trash?$query");
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals($expected, Hash::extract($result['data'], '{n}.id'), '');
        static::assertEqualsCanonicalizing($expected, Hash::extract($result['data'], '{n}.id'), '');
        static::assertEqualsWithDelta($expected, Hash::extract($result['data'], '{n}.id'), 0, '');
    }

    /**
     * Data provider for `testParentAncestorFilter` test case.
     *
     * @return array
     */
    public function parentAncestorFilterProvider()
    {
        return [
            'root parent' => [
                '/objects',
                'filter[parent]=root-folder',
                [
                    '12',
                    '2',
                ],
            ],
            'root ancestor' => [
                '/objects',
                'filter[ancestor]=root-folder',
                [
                    '2',
                    '4',
                    '12',
                ],
            ],
            'documents' => [
                '/documents',
                'filter[ancestor]=11',
                [
                    '2',
                ],
            ],
            'folders' => [
                '/folders',
                'filter[parent]=11',
                [
                    '12',
                ],
            ],
        ];
    }

    /**
     * Test filters on /trash endpoint.
     *
     * @param string $endpoint Endpoint.
     * @param string $query Query string.
     * @param array $expected Expected result.
     * @return void
     * @dataProvider parentAncestorFilterProvider
     * @coversNothing
     */
    public function testParentAncestorFilter($endpoint, $query, $expected)
    {
        $this->configRequestHeaders();
        $this->get("$endpoint?$query");
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals($expected, Hash::extract($result['data'], '{n}.id'));
    }

    /**
     * Test `/folders?filter[roots]`.
     *
     * @coversNothing
     */
    public function testRootsFilter()
    {
        $this->configRequestHeaders();
        $this->get('/folders?filter[roots]');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals([11, 13], Hash::extract($result['data'], '{n}.id'));
    }

    /**
     * Data provider for `testCategoriesTags`.
     *
     * @return array
     */
    public function categoriesTagsProvider()
    {
        return [
            'categories' => [
                '/documents',
                'filter[categories]=first-cat',
                [
                    '2',
                ],
            ],
            'two cats' => [
                '/documents',
                'filter[categories]=first-cat,second-cat',
                [
                    '2',
                ],
            ],
            'disabled' => [
                '/documents',
                'filter[categories]=disabled-cat',
                [
                ],
            ],
            'tags' => [
                '/profiles',
                'filter[tags]=first-tag',
                [
                    '4',
                ],
            ],
        ];
    }

    /**
     * Test filters on categories and tags.
     *
     * @param string $endpoint Endpoint.
     * @param string $query Query string.
     * @param array $expected Expected result.
     * @return void
     * @dataProvider categoriesTagsProvider
     * @coversNothing
     */
    public function testCategoriesTags($endpoint, $query, $expected)
    {
        $this->configRequestHeaders();
        $this->get("$endpoint?$query");
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertArrayHasKey('data', $result);
        static::assertEquals($expected, Hash::extract($result['data'], '{n}.id'));
    }

    /**
     * Test `/model/categories?filter[type]={type}`.
     *
     * @return void
     * @coversNothing
     */
    public function testCategoriesTypeFilter(): void
    {
        $this->configRequestHeaders();
        $this->get('/model/categories?filter[type]=documents');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('data', $result);
        static::assertEquals(4, count($result['data']));

        $this->configRequestHeaders();
        $this->get('/model/categories?filter[type]=locations');
        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals(0, count($result['data']));
    }

    /**
     * Data provider for `testRelatedFilter`.
     *
     * @return array
     */
    public function relatedFilterProvider()
    {
        return [
            'test' => [
                [2, 3],
                '/documents?filter[test]=4',
            ],
            'inverse_another_test' => [
                [8],
                '/locations?filter[inverse_another_test]=1',
            ],
        ];
    }

    /**
     * Test `filter[{relation}]={id}`.
     *
     * @param array $expected Expected result
     * @param string $url Request URL
     * @return void
     * @dataProvider relatedFilterProvider
     * @coversNothing
     */
    public function testRelatedFilter(array $expected, string $url): void
    {
        $this->configRequestHeaders();
        $this->get($url);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $result = json_decode((string)$this->_response->getBody(), true);
        $found = Hash::extract($result, 'data.{n}.id');
        static::assertEquals($expected, $found);
    }
}
