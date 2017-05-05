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

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * Fixture for `object_types` table.
 */
class ObjectTypesFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'singular' => 'document',
            'name' => 'documents',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects'
        ],
        [
            'singular' => 'profile',
            'name' => 'profiles',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Profiles'
        ],
        [
            'singular' => 'user',
            'name' => 'users',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Users'
        ],
        [
            'singular' => 'news',
            'name' => 'news',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects'
        ],
        [
            'singular' => 'location',
            'name' => 'locations',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Locations'
        ],
        [
            'singular' => 'event',
            'name' => 'events',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
            'associations' => '["DateRanges"]',
        ],
    ];
}
