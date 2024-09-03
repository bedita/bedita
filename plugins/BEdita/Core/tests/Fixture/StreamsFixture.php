<?php
declare(strict_types=1);

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

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Fixture for `streams` table.
 *
 * @since 4.0.0
 */
class StreamsFixture extends TestFixture
{
    /**
     * @inheritDoc
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
        [
            'uuid' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
            'version' => 1,
            'object_id' => 14,
            'uri' => 'default://6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif',
            'file_name' => 'bedita-logo-gray.gif',
            'mime_type' => 'image/gif',
            'file_size' => 927,
            'hash_md5' => 'a714dbb31ca89d5b1257245dfa5c5153',
            'hash_sha1' => '444b2b42b48b0b815d70f6648f8a7a23d5faf54b',
            'width' => 118,
            'height' => 52,
            'created' => '2018-03-22 15:58:47',
            'modified' => '2018-03-22 15:58:47',
        ],
        [
            'uuid' => '9b06b2cf-fce7-47e8-b367-a3e5b464ca85',
            'version' => 1,
            'object_id' => 16,
            'uri' => 'default://9b06b2cf-fce7-47e8-b367-a3e5b464ca85-sample.svg',
            'file_name' => 'sample.svg',
            'mime_type' => 'image/svg+xml',
            'file_size' => 461,
            'hash_md5' => '',
            'hash_sha1' => '',
            'created' => '2024-03-25 16:11:18',
            'modified' => '2024-03-25 16:11:18',
        ],
        [
            'uuid' => 'eadc9cd3-b0ae-4e43-9251-9f44bd026793',
            'version' => 1,
            'object_id' => 17,
            'uri' => 'default://eadc9cd3-b0ae-4e43-9251-9f44bd026793-snow-on-white.jpg',
            'file_name' => 'snow-on-white.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 140910,
            'hash_md5' => '04fd3cc862a142c114c6f7822996207a',
            'hash_sha1' => 'e3d5556baf1c257d10a146c0ebf84a2ab99c7437',
            'width' => 8000,
            'height' => 4500,
            'created' => '2024-06-25 10:11:18',
            'modified' => '2024-06-25 10:11:18',
        ],
        [
            'uuid' => '7ffcb45e-4cc1-492e-9775-74ee6999503f',
            'version' => 1,
            'object_id' => 18,
            'uri' => 'default://7ffcb45e-4cc1-492e-9775-74ee6999503f-snow-on-white.jpg',
            'file_name' => 'snow-on-white.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 140910,
            'hash_md5' => '04fd3cc862a142c114c6f7822996207a',
            'hash_sha1' => 'e3d5556baf1c257d10a146c0ebf84a2ab99c7437',
            'width' => null,
            'height' => null,
            'created' => '2024-06-25 10:11:18',
            'modified' => '2024-06-25 10:11:18',
        ],
    ];
}
