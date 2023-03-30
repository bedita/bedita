<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
 * ObjectCategoriesFixture
 */
class ObjectCategoriesFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'object_id' => 2,
            'category_id' => 1,
            'params' => '100',
        ],
        [
            'object_id' => 2,
            'category_id' => 2,
            'params' => null,
        ],
        [
            'object_id' => 2,
            'category_id' => 3,
            'params' => null,
        ],
    ];
}
