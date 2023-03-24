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
 * Fixture for `fake_animals` table.
 */
class FakeAnimalsFixture extends TestFixture
{
    /**
     * @inheritDoc
     */
    public $records = [
        ['name' => 'cat', 'legs' => 4, 'modified' => '2018-02-20 09:50:00'],
        ['name' => 'koala', 'legs' => 4],
        ['name' => 'eagle', 'legs' => 2],
    ];
}
