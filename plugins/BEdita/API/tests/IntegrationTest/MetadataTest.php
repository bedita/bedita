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
use Cake\I18n\Time;
use Cake\Utility\Text;

/**
 * Test meta data
 *
 * @coversNothing
 */
class MetadataTest extends IntegrationTestCase
{

    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.locations',
    ];

    /**
     * Data provider for `testLastModified` test case.
     *
     * @return array
     */
    public function lastModifiedProvider()
    {
        return [
            'documents' => [
                2,
                'documents',
                [
                    'title' => 'My new title is: ' . Text::uuid(),
                ],
            ],
            'locations with own field' => [
                8,
                'locations',
                [
                    'locality' => 'My new locality is: ' . Text::uuid(),
                ],
            ],
            'locations with core field' => [
                8,
                'locations',
                [
                    'title' => 'My new title is: ' . Text::uuid(),
                ],
            ],
            'users with core field' => [
                1,
                'users',
                [
                    'title' => 'My new title is: ' . Text::uuid(),
                ],
            ],
        ];
    }

    /**
     * Test that last modified date is always saved correctly.
     *
     * @param int $id Object ID.
     * @param string $type Object type.
     * @param array $attributes New attributes.
     * @return void
     *
     * @dataProvider lastModifiedProvider
     * @coversNothing
     */
    public function testLastModified($id, $type, array $attributes)
    {
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader('second user', 'password2'));

        $id = (string)$id;
        $data = compact('id', 'type', 'attributes');
        $this->patch(sprintf('/%s/%d', $type, $id), json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotEmpty();
        $body = json_decode((string)$this->_response->getBody(), true);

        static::assertArrayHasKey('data', $body);
        static::assertArrayHasKey('meta', $body['data']);
        static::assertArrayHasKey('modified', $body['data']['meta']);
        static::assertArrayHasKey('modified_by', $body['data']['meta']);

        static::assertEquals(
            Time::now()->timestamp,
            Time::parse($body['data']['meta']['modified'])->getTimestamp(),
            '`modified` field not updated',
            5
        );
        static::assertSame(5, $body['data']['meta']['modified_by'], '`modified_by` field not updated');
    }
}
