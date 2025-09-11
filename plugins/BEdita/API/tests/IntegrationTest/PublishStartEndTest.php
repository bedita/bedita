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
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Integration test for `Publish.checkDate` configuration
 * using `publish_start` and `publish_date` values.
 */
#[CoversNothing]
class PublishStartEndTest extends IntegrationTestCase
{
    /**
     * Provider for testListObjects()
     *
     * @return array
     */
    public static function listProvider(): array
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
     */
    #[DataProvider('listProvider')]
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
    public static function singleProvider(): array
    {
        return [
            'not started' => [
                404,
                true,
                [
                    'publish_start' => DateTime::now()->addDays(1),
                ],
            ],
            'no conf' => [
                200,
                false,
                [
                    'publish_start' => DateTime::now()->addDays(1),
                ],
            ],
            'ended' => [
                404,
                true,
                [
                    'publish_end' => DateTime::now()->subDays(1),
                ],
            ],
            'started' => [
                200,
                true,
                [
                    'publish_start' => DateTime::now()->subDays(1),
                    'publish_end' => DateTime::now()->addDays(1),
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
     */
    #[DataProvider('singleProvider')]
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
