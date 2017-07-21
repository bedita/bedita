<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
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
 * Fixture for `streams` table.
 *
 * @since 4.0.0
 */
class StreamsFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     */
    public $records = [
        [
            'uuid' => 'e5afe167-7341-458d-a1e6-042e8791b0fe',
            'version' => 1,
            'object_id' => null,
            'uri' => 'default://e5afe167-7341-458d-a1e6-042e8791b0fe-bedita-logo.png',
            'file_name' => 'bedita_logo.png',
            'mime_type' => 'image/png',
            'file_size' => 5650,
            'hash_md5' => 'a1236770b354472e94d891bf83fefa03',
            'hash_sha1' => 'ab9bdc1379a1499eb6eee5762c039a5cd4899d7b',
            'width' => 293,
            'height' => 94,
            'created' => '2017-06-22 12:37:41',
            'modified' => '2017-06-22 12:37:41',
        ],
        [
            'uuid' => '9e58fa47-db64-4479-a0ab-88a706180d59',
            'version' => 1,
            'object_id' => 10,
            'uri' => 'default://9e58fa47-db64-4479-a0ab-88a706180d59.txt',
            'file_name' => 'sample.txt',
            'mime_type' => 'text/plain',
            'file_size' => 22,
            'hash_md5' => '4803449f89ea5eeb42efa1b2889dd770',
            'hash_sha1' => '283b1edb6f051ef1d1770cd9bb08e75066b437e6',
            'created' => '2017-06-22 12:37:41',
            'modified' => '2017-06-22 12:37:41',
        ],
    ];
}
