<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
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

/**
 * Integration test for resource names validations.
 */
class ResourceNamesValidationTest extends IntegrationTestCase
{
    /**
     * Data provider for `testValidate` method
     *
     * @return array
     */
    public function validateProvider(): array
    {
        return [
            'bad ptype' => [
                400,
                '/model/property_types',
                [
                    'name' => '12345',
                ],
                'property_types',
            ],
            'good ptype' => [
                201,
                '/model/property_types',
                [
                    'name' => 'abc_de',
                    'params' => null,
                ],
                'property_types',
            ],
            'bad otype' => [
                400,
                '/model/object_types',
                [
                    'name' => 'Foos',
                    'singular' => 'Foo',
                ],
                'object_types',
            ],
            'good otype' => [
                201,
                '/model/object_types',
                [
                    'name' => 'foos',
                    'singular' => 'foo',
                ],
                'object_types',
            ],
            'bad app' => [
                400,
                '/admin/applications',
                [
                    'name' => '1_app',
                ],
                'applications',
            ],
            'bad role' => [
                400,
                '/roles',
                [
                    'name' => 'first-role',
                ],
                'roles',
            ],
        ];
    }

    /**
     * Test validation on resource creations
     *
     * @param int $expected Expected response code
     * @param string $endpoint The test URL
     * @param array $attributes Body data attributes
     * @param string $type The resource type
     * @return void
     * @dataProvider validateProvider
     * @coversNothing
     */
    public function testValidate(int $expected, string $endpoint, array $attributes, string $type): void
    {
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $data = compact('type', 'attributes');

        $this->post($endpoint, json_encode(compact('data')));
        $this->assertResponseCode($expected);
    }
}
