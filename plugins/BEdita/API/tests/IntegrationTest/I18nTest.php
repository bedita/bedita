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

/**
 * Test internationalization related cases.
 */
class I18nTest extends IntegrationTestCase
{

    /**
     * Test wrong lang tag.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testWrongLang()
    {
        Configure::write(
            'I18n',
            [
                'languages' => [
                    'en' => 'English',
                ]
            ]
        );

        $data = [
            'id' => '2',
            'type' => 'documents',
            'attributes' => [
                'lang' => 'fi',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/documents/2', json_encode(compact('data')));
        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $body = json_decode((string)$this->_response->getBody(), true);

        $expected = [
            'error' => [
                'status' => '400',
                'title' => 'Invalid data',
                'detail' => '[lang.languageTag]: Invalid language tag "fi"',
            ],
        ];

        unset($body['error']['meta']);
        unset($body['links']);
        static::assertEquals($body, $expected);
    }
}
