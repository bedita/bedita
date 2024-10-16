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
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Test on `uname` field
 */
class UniqueNameTest extends IntegrationTestCase
{
    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Locations',
    ];

    /**
     * Data provider for testDoubleInsert
     *
     * @return array
     */
    public function doubleInsertProvider()
    {
        return [
            'sameTitle' => [
                ['title' => 'test double test double'],
            ],
            'emptyTitle' => [
                ['title' => ''],
            ],
            'empty' => [
                [],
            ],
        ];
    }

    /**
     * Test inserting the same data two times for different object types.
     *
     * @param array $attributes Object attributes.
     * @return void
     * @dataProvider doubleInsertProvider
     * @coversNothing
     */
    public function testDoubleInsert(array $attributes)
    {
        $authHeader = $this->getUserAuthHeader();

        $sendRequest = function ($type) use ($attributes, $authHeader) {
            $data = [
                'type' => $type,
                'attributes' => $attributes,
            ];
            $endpoint = '/' . $type;
            $requestBody = json_encode(compact('data'));
            $this->configRequestHeaders('POST', $authHeader);

            $this->post($endpoint, $requestBody);

            $this->assertResponseCode(201);
            $this->assertContentType('application/vnd.api+json');
            $this->assertHeader('Location', sprintf('http://api.example.com/%s/%s', $type, $this->lastObjectId()));
            $this->assertResponseNotEmpty();
            $body = json_decode((string)$this->_response->getBody(), true);
            static::assertArrayHasKey('data', $body);
            static::assertArrayHasKey('attributes', $body['data']);
            static::assertArrayHasKey('uname', $body['data']['attributes']);

            return $body;
        };

        foreach (['documents', 'locations'] as $type) {
            $bodyFirst = $sendRequest($type);
            $bodySecond = $sendRequest($type);
            static::assertNotEquals($bodyFirst['data']['attributes']['uname'], $bodySecond['data']['attributes']['uname']);
        }
    }

    /**
     * Test unique name customization.
     *
     * @return void
     * @coversNothing
     */
    public function testCustomBehavior()
    {
        $authHeader = $this->getUserAuthHeader();
        $this->configRequestHeaders('POST', $authHeader);

        $data = [
            'type' => 'profiles',
            'attributes' => [
                'title' => 'my profile',
            ],
        ];
        $this->post('/profiles', json_encode(compact('data')));

        $id = $this->lastObjectId();

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', sprintf('http://api.example.com/profiles/%s', $id));
        $this->assertResponseNotEmpty();
        $body = json_decode((string)$this->_response->getBody(), true);
        static::assertArrayHasKey('data', $body);
        static::assertArrayHasKey('attributes', $body['data']);
        static::assertArrayHasKey('uname', $body['data']['attributes']);
        $designatedUname = 'profile-my-profile';
        static::assertSame($designatedUname, $body['data']['attributes']['uname']);

        static::assertSame($designatedUname, TableRegistry::getTableLocator()->get('Profiles')->get($id)->get('uname'));
    }

    /**
     * Test invalid numeric unique name.
     *
     * @return void
     * @coversNothing
     */
    public function testInvalidNumericUname(): void
    {
        $authHeader = $this->getUserAuthHeader();
        $this->configRequestHeaders('POST', $authHeader);

        $data = [
            'type' => 'documents',
            'attributes' => [
                'uname' => '123',
                'title' => 'my document',
            ],
        ];
        $this->post('/documents', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotEmpty();
        $body = json_decode((string)$this->_response->getBody(), true);
        static::assertIsArray($body);
        static::assertArrayHasKey('error', $body);
        static::assertEquals('Invalid data', Hash::get($body, 'error.title'));
        static::assertEquals('[uname.notNumeric]: The provided value is invalid', Hash::get($body, 'error.detail'));
    }

    /**
     * Test invalid numeric unique name updating an object.
     *
     * @return void
     * @coversNothing
     */
    public function testUpdateInvalidNumericUname(): void
    {
        $authHeader = $this->getUserAuthHeader();
        $this->configRequestHeaders('PATCH', $authHeader);

        $data = [
            'type' => 'documents',
            'id' => '2',
            'attributes' => [
                'uname' => '789',
            ],
        ];
        $this->patch('/documents/2', json_encode(compact('data')));
        $this->assertResponseCode(400);
        $body = json_decode((string)$this->_response->getBody(), true);
        static::assertIsArray($body);
        static::assertEquals('[uname.notNumeric]: The provided value is invalid', Hash::get($body, 'error.detail'));
    }
}
