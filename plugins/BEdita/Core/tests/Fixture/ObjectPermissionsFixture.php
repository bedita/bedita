<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
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
 * ObjectPermissionsFixture
 */
class ObjectPermissionsFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'object_id' => 2,
            'role_id' => 1,
            'created_by' => 1,
            'created' => '2023-03-29 15:08',
        ],
    ];
}
