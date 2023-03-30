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
 * AnnotationsFixture
 */
class AnnotationsFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'object_id' => 2,
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Best regards.',
            'user_id' => 1,
            'created' => '2018-02-17 10:23:15',
            'modified' => '2018-02-17 10:23:15',
            'params' => 'something',
        ],
        [
            'object_id' => 3,
            'description' => 'Gustavo for President!',
            'user_id' => 5,
            'created' => '2018-06-17 13:34:25',
            'modified' => '2018-06-17 13:34:25',
            'params' => '1',
        ],
    ];
}
