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
 * Fixture for `profiles` table.
 */
class ProfilesFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'name' => 'First',
            'surname' => 'User',
            'email' => 'first.user@example.com',
            'person_title' => 'Mr.',
            'gender' => null,
            'birthdate' => '1945-04-25',
            'deathdate' => null,
        ],
        [
            'id' => 4,
            'name' => 'Gustavo',
            'surname' => 'Supporto',
            'email' => 'gustavo.supporto@channelweb.it',
            'person_title' => 'Doct.',
            'gender' => 'monkey',
            'birthdate' => null,
            'deathdate' => null,
        ],
        [
            'id' => 5,
            'name' => 'Second',
            'surname' => 'User',
            'email' => 'second.user@example.com',
            'person_title' => 'Miss',
            'gender' => null,
            'birthdate' => null,
            'deathdate' => null,
        ],
    ];
}
