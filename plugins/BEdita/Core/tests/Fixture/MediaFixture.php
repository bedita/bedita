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
 * MediaFixture
 */
class MediaFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 10,
            'name' => 'My media name',
            'width' => null,
            'height' => null,
            'duration' => null,
            'provider' => null,
            'provider_uid' => null,
            'provider_url' => null,
            'provider_thumbnail' => null,
        ],
        [
            'id' => 14,
            'name' => 'My other media name',
            'width' => null,
            'height' => null,
            'duration' => null,
            'provider' => null,
            'provider_uid' => null,
            'provider_url' => null,
            'provider_thumbnail' => null,
        ],
        [
            'id' => 16,
            'name' => 'An svg media',
            'width' => null,
            'height' => null,
            'duration' => null,
            'provider' => null,
            'provider_uid' => null,
            'provider_url' => null,
            'provider_thumbnail' => null,
        ],
        [
            'id' => 18,
            'name' => 'Modern art',
            'width' => null,
            'height' => null,
            'duration' => null,
            'provider' => null,
            'provider_uid' => null,
            'provider_url' => null,
            'provider_thumbnail' => null,
        ],
        [
            'id' => 20,
            'name' => 'Contemporary art',
            'width' => null,
            'height' => null,
            'duration' => null,
            'provider' => null,
            'provider_uid' => null,
            'provider_url' => null,
            'provider_thumbnail' => null,
        ],
    ];
}
