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
            'id' => 3,
            'name' => 'Gustavo',
            'surname' => 'Supporto',
            'email' => 'gustavo.supporto@channelweb.it',
            'person_title' => 'Doct.',
            'gender' => 'monkey',
            'birthdate' => null,
            'deathdate' => null,
        ],
    ];
}
