<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
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
use Cake\Database\Driver\Mysql;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

/**
 * Test filter on custom properties
 */
class CustomPropertiesFilterTest extends IntegrationTestCase
{
    /**
     * @inheritDoc
     */
    public $fixtures = [
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->skipUnless(ConnectionManager::get('default')->getDriver() instanceof Mysql);
        FilesystemRegistry::setConfig(Configure::read('Filesystem'));
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        FilesystemRegistry::dropAll();
    }

    /**
     * Data provider for `testFilter()`
     *
     * @return array
     */
    public function filterProvider(): array
    {
        return [
            'bool true' => [
                ['10'],
                '/files?filter[media_property]=true',
            ],
            'bool 1' => [
                ['10'],
                '/files?filter[media_property]=1',
            ],
            'bool false' => [
                ['14'],
                '/files?filter[media_property]=false',
            ],
            'bool 0' => [
                ['14'],
                '/files?filter[media_property]=0',
            ],
            'string' => [
                ['5'],
                '/users?filter[another_username]=synapse',
            ],
            'string no results' => [
                [],
                '/users?filter[another_username]=batman',
            ],
        ];
    }

    /**
     * Test filter on custom props.
     *
     * @param array $expected The expected result
     * @param string $url Url
     * @return void
     * @dataProvider filterProvider
     * @coversNothing
     */
    public function testFilter(array $expected, $url): void
    {
        $this->configRequestHeaders();
        $this->get($url);

        $this->assertResponseCode(200);

        $result = json_decode((string)$this->_response->getBody(), true);
        $ids = Hash::extract($result, 'data.{n}.id');
        sort($expected);
        sort($ids);
        static::assertEquals($expected, $ids);
    }

    /**
     * Test that multi filter works.
     *
     * @return void
     */
    public function testMultiFilter(): void
    {
        $id = 4; // gustavo is here
        $Profiles = $this->getTableLocator()->get('Profiles');
        $profile = $Profiles->get($id);
        $profile->set([
            'number_of_friends' => 10,
            'another_surname' => 'Support',
        ]);
        $Profiles->saveOrFail($profile);

        $this->configRequestHeaders();
        $endpoint = '/profiles?filter[number_of_friends]=%s&filter[another_surname]=%s';
        $this->get(sprintf($endpoint, '10', 'Support'));
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);
        $ids = Hash::extract($result, 'data.{n}.id');
        sort($ids);
        static::assertEquals([$id], $ids);

        // retry but with number_of_friends wrong
        $this->configRequestHeaders();
        $this->get(sprintf($endpoint, '11', 'Support'));
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);
        $ids = Hash::extract($result, 'data.{n}.id');
        static::assertEquals([], $ids);
    }
}
