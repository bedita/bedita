<?php
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
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Test `Status.level` configuration
 */
class StatusLevelTest extends IntegrationTestCase
{
    /**
     * Provider for testListObjects()
     *
     * @return array
     */
    public function listProvider()
    {
        return [
            'on docs' => [
                ['2'],
                'on',
                '/documents',
            ],
            'all docs' => [
                ['2', '3'],
                'draft',
                '/documents',
            ],
            'all docs 2' => [
                ['2', '3'],
                null,
                '/documents',
            ],
        ];
    }

    /**
     * Test `Status.level` config on objects list
     *
     * @param array $expected Object ids in response
     * @param string $config The `Status.level` config
     * @param string $url The test URL
     * @return void
     *
     * @dataProvider listProvider
     * @coversNothing
     */
    public function testListObjects($expected, $config, $url)
    {
        Configure::write('Status.level', $config);

        $this->configRequestHeaders();
        $this->get($url);
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);

        $ids = Hash::extract($result, 'data.{n}.id');
        sort($ids);
        static::assertEquals($expected, $ids);
    }

    /**
     * Provider for testSingleObject()
     *
     * @return array
     */
    public function singleProvider()
    {
        return [
            'notFound' => [
                404,
                'on',
                '/documents/3',
            ],
            'found' => [
                200,
                'draft',
                '/documents/3',
            ],
            'related with lang' => [
                200,
                'on',
                '/documents/3/test?lang=fr',
            ],
        ];
    }

    /**
     * Test `Status.level` config on single objects
     *
     * @param int $expected The HTTP status code expected
     * @param string $config The `Status.level` config
     * @param string $url The test URL
     * @return void
     *
     * @dataProvider singleProvider
     * @coversNothing
     */
    public function testSingleObject($expected, $config, $url)
    {
        Configure::write('Status.level', $config);

        $this->configRequestHeaders();
        $this->get($url);
        $this->assertResponseCode($expected);
    }
}
