<?php
declare(strict_types=1);

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
 * HistoryFixture
 *
 * @since 4.0.1
 */
class HistoryFixture extends TestFixture
{
    /**
     * @inheritDoc
     */
    public $table = 'history';

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'resource_id' => '2',
            'resource_type' => 'objects',
            'created' => '2016-05-13 07:09:22',
            'user_id' => 1,
            'application_id' => 1,
            'user_action' => 'create',
            'changed' => [
                'title' => 'title one',
                'description' => 'description here',
            ],
        ],
        [
            'resource_id' => '2',
            'resource_type' => 'objects',
            'created' => '2016-05-13 07:09:23',
            'user_id' => 5,
            'application_id' => 1,
            'user_action' => 'update',
            'changed' => [
                'body' => 'body here',
                'extra' => [
                    'abstract' => 'abstract here',
                    'list' => ['one', 'two', 'three'],
                ],
            ],
        ],
    ];
}
