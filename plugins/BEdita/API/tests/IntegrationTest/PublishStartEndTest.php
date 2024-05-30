<?php
declare(strict_types=1);

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
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Integration test for `Publish.checkDate` configuration
 * using `publish_start` and `publish_date` values.
 */
class PublishStartEndTest extends IntegrationTestCase
{
    /**
     * Provider for testListObjects()
     *
     * @return array
     */
    public function listProvider(): array
    {
        return [
            'publishable docs' => [
                ['3'],
                true,
                '/documents',
            ],
            'all docs' => [
                ['2', '3'],
                false,
                '/documents',
            ],
        ];
    }

    /**
     * Test `Publish.checkDate` config on objects list
     *
     * @param array $expected Object ids in response
     * @param bool $config The `Publish.checkDate` config
     * @param string $url The test URL
     * @return void
     * @dataProvider listProvider
     * @coversNothing
     */
    public function testListObjects($expected, $config, $url): void
    {
        Configure::write('Publish.checkDate', $config);

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
    public function singleProvider(): array
    {
        return [
            'not started' => [
                404,
                true,
                [
                    'publish_start' => FrozenTime::now()->addDay(),
                ],
            ],
            'no conf' => [
                200,
                false,
                [
                    'publish_start' => FrozenTime::now()->addDay(),
                ],
            ],
            'ended' => [
                404,
                true,
                [
                    'publish_end' => FrozenTime::now()->subDay(),
                ],
            ],
            'started' => [
                200,
                true,
                [
                    'publish_start' => FrozenTime::now()->subDay(),
                    'publish_end' => FrozenTime::now()->addDay(),
                ],
            ],
        ];
    }

    /**
     * Test `Publish.checkDate` config on single objects
     *
     * @param int $expected The HTTP status code expected
     * @param bool $config The `Publish.checkDate` config
     * @param array $data The fields data
     * @return void
     * @dataProvider singleProvider
     * @coversNothing
     */
    public function testSingleObject($expected, $config, array $data): void
    {
        Configure::write('Publish.checkDate', $config);

        $table = TableRegistry::getTableLocator()->get('Documents');
        $document = $table->get(3);
        $document->set($data);
        $table->saveOrFail($document);

        $this->configRequestHeaders();
        $this->get('/documents/3');
        $this->assertResponseCode($expected);
    }
}
