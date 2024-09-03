<?php
declare(strict_types=1);

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
 * PublicationsFixture
 */
class PublicationsFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        // Beware: this is a `fake` fixture, object with id 15 is acutally a `document`!
        [
            'id' => 15,
            'public_name' => 'Gustavo blog',
            'public_url' => 'https://www.gustavo.com',
            'staging_url' => 'https://staging.gustavo.com',
            'stats_code' => 'abcdef',
        ],
    ];
}
