<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
use Cake\Utility\Hash;

/**
 * Test Query String `count`.
 *
 */
class CountQueryStringTest extends IntegrationTestCase
{
    /**
     * Data provider for testCountSingle.
     *
     * @return array
     */
    public function countSingleProvider(): array
    {
        return [
            'empty' => [
                [],
                '',
            ],
            'one rel' => [
                [
                    'test' => 2,
                ],
                'test',
            ],
            'two rel' => [
                [
                    'test' => 2,
                    'inverse_test' => 0,
                ],
                'test,inverse_test',
            ],
            'exclude invalid rel' => [
                [
                    'test' => 2,
                    'inverse_test' => 0,
                ],
                'test,inverse_test,gustavo',
            ],
            'all rel' => [
                [
                    'test' => 2,
                    'inverse_test' => 0,
                ],
                'all',
            ],
        ];
    }

    /**
     * Test count query string getting one object.
     *
     * @param array $expected The expected count
     * @param string $count The count query string
     * @return void
     *
     * @dataProvider countSingleProvider()
     * @coversNothing
     */
    public function testCountSingle($expected, $count): void
    {
        $url = sprintf('/documents/2?count=%s', $count);
        $this->configRequestHeaders();
        $this->get($url);
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);

        if (empty($count)) {
            $metaCount = Hash::extract($result, 'data.relationships.{s}.meta.count');
            static::assertEquals($expected, $metaCount);

            return;
        }

        $relationships = (array)Hash::get($result, 'data.relationships');
        foreach ($relationships as &$relData) {
            $relData = Hash::get($relData, 'meta.count');
        }

        if ($count === 'all') {
            $count = ['test', 'inverse_test'];
        } else {
            $count = array_filter(explode(',', $count));
        }

        foreach ($count as $c) {
            if (array_key_exists($c, $expected)) {
                static::assertEquals($expected[$c], $relationships[$c]);
            } else {
                static::assertEmpty(Hash::get($relationships, $c));
            }
        }
    }

    /**
     * Test that meta count presence in included data.
     *
     * @return void
     */
    public function testCountInclude(): void
    {
        $this->configRequestHeaders();
        $this->get('/documents?include=test&count=all');
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);

        foreach ($result['data'] as $data) {
            static::assertArrayHasKey('count', (array)Hash::get($data, 'relationships.test.meta'));
            static::assertArrayHasKey('count', (array)Hash::get($data, 'relationships.inverse_test.meta'));
        }

        foreach ($result['included'] as $data) {
            if ($data['type'] === 'documents') {
                static::assertArrayHasKey('count', (array)Hash::get($data, 'relationships.test.meta'));
            }
            static::assertArrayHasKey('count', (array)Hash::get($data, 'relationships.inverse_test.meta'));
        }
    }
}
