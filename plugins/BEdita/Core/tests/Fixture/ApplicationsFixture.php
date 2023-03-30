<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ApplicationsFixture
 *
 * @since 4.0.0
 */
class ApplicationsFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'api_key' => API_KEY,
            'client_secret' => null,
            'name' => 'First app',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat.',
            'created' => '2016-10-28 07:10:57',
            'modified' => '2016-10-28 07:10:57',
            'enabled' => 1,
        ],
        [
            'api_key' => 'abcdef12345',
            'client_secret' => 'topsecretstring',
            'name' => 'Disabled app',
            'description' => 'This app has been disabled',
            'created' => '2017-02-17 15:51:29',
            'modified' => '2017-02-17 15:51:29',
            'enabled' => 0,
        ],
    ];
}
