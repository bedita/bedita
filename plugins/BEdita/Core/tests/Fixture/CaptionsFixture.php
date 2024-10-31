<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
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
 * CaptionsFixture
 */
class CaptionsFixture extends TestFixture
{
    /**
     * @inheritDoc
     */
    public $records = [
        // 1
        [
            'object_id' => 19,
            'status' => 'on',
            'label' => 'Generic subtitles',
            'lang' => 'en',
            'format' => 'vtt',
            'caption_text' => 'WEBVTT\n\n00:00:00.500 --> 00:00:02.000\nHi, my name is Gustavo\n\n00:00:02.500 --> 00:00:04.300\nand this is my funny adventures',
            'params' => null,
            'created' => '2024-10-22 07:51:26',
            'modified' => '2024-10-22 07:51:26',
        ],
    ];
}
