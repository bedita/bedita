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
use Cake\Utility\Hash;

/**
 * Test Query String `fields`
 */
class FieldsQueryStringTest extends IntegrationTestCase
{
    /**
     * Provider for `testFields`
     *
     * @return array
     */
    public function fieldsProvider()
    {
        return [
            'simple' => [
                '/documents?fields=title',
                ['title'],
                []
            ],
            'multi' => [
                '/roles?fields=name,created',
                ['name'],
                ['created']
            ],
            'none' => [
                '/users?fields=gustavo',
                [],
                []
            ],
            'single' => [
                '/users/1?fields=username',
                ['username'],
                []
            ],
            'meta' => [
                '/roles/1?fields=unchangeable',
                [],
                ['unchangeable'],
            ],
        ];
    }

    /**
     * Test `fields` query string
     *
     * @param string $url Endpoint url to test
     * @param array $attributes Expected response attributes
     * @param array $meta Expected response meta
     * @return void
     *
     * @dataProvider fieldsProvider
     * @coversNothing
     */
    public function testFields($url, $attributes, $meta)
    {
        $this->configRequestHeaders();
        $this->get($url);
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);

        // single result
        if (!empty($result['data']['id'])) {
            $resultAttr = [Hash::get($result, 'data.attributes', [])];
            $resultMeta = [Hash::get($result, 'data.meta', [])];
        } else {
            $resultAttr = Hash::extract($result, 'data.{n}.attributes');
            $resultMeta = Hash::extract($result, 'data.{n}.meta');
        }

        if (!empty($resultAttr)) {
            foreach ($resultAttr as $r) {
                static::assertEquals($attributes, array_keys($r));
            }
        } else {
            static::assertEquals($attributes, array_keys($resultAttr));
        }

        if (!empty($resultMeta)) {
            foreach ($resultMeta as $r) {
                static::assertEquals($meta, array_keys($r));
            }
        } else {
            static::assertEquals($meta, array_keys($resultMeta));
        }
    }
}
