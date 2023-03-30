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
namespace BEdita\Core\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TagsFixture
 */
class TagsFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        // 1
        [
            'name' => 'first-tag',
            'label' => 'First tag',
            'enabled' => 1,
            'created' => '2019-11-26 12:15:51',
            'modified' => '2019-11-26 12:15:51',
        ],
    ];
}
