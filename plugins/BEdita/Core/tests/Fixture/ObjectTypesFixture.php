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
            'name' => 'document',
            'pluralized' => 'documents',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects'
        ],
        [
            'name' => 'profile',
            'pluralized' => 'profiles',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Profiles'
        ],
        [
            'name' => 'user',
            'pluralized' => 'users',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Users'
        ],
        [
            'name' => 'news',
            'pluralized' => 'news',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects'
        ],
    ];
}
