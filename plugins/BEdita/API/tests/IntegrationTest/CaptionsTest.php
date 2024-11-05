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
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Utility\Hash;

/**
 * Test captions.
 *
 * @coversNothing
 */
class CaptionsTest extends IntegrationTestCase
{
    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Captions',
    ];

    /**
     * Test get video with captions.
     *
     * @return void
     * @coversNothing
     */
    public function testVideoWithCaptions(): void
    {
        $this->configRequestHeaders();
        $this->get('/videos/19');

        $this->assertResponseCode(200);
        $body = json_decode((string)$this->_response->getBody(), true);

        $expected = [
            [
                'status' => 'on',
                'label' => 'Generic subtitles',
                'format' => 'vtt',
                'lang' => 'en',
                'caption_text' => 'WEBVTT\n\n00:00:00.500 --> 00:00:02.000\nHi, my name is Gustavo\n\n00:00:02.500 --> 00:00:04.300\nand these are my funny adventures',
                'params' => null,
            ],
        ];

        static::assertEquals($expected, Hash::get((array)$body, 'data.attributes.captions'));
    }

    /**
     * Test add captions.
     *
     * @return void
     * @coversNothing
     */
    public function testAddCaptions(): void
    {
        $expected = [
            [
                'status' => 'on',
                'label' => 'Generic subtitles',
                'format' => 'vtt',
                'lang' => 'en',
                'caption_text' => 'WEBVTT\n\n00:00:00.500 --> 00:00:02.000\nHi, my name is Gustavo\n\n00:00:02.500 --> 00:00:04.300\nand these are my funny adventures',
                'params' => null,
            ],
            [
                'status' => 'draft',
                'label' => 'Esempio',
                'format' => 'text',
                'lang' => 'it',
                'caption_text' => 'Testo semplice',
                'params' => [
                    'key' => 'value',
                ],
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $data = [
            'type' => 'videos',
            'id' => '19',
            'attributes' => [
                'captions' => $expected,
            ],
        ];
        $this->patch('/videos/19', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $body = json_decode((string)$this->_response->getBody(), true);

        static::assertEquals($expected, Hash::get((array)$body, 'data.attributes.captions'));
    }

    /**
     * Test replace captions.
     *
     * @return void
     * @coversNothing
     */
    public function testReplaceCaption(): void
    {
        $expected = [
            [
                'status' => 'draft',
                'label' => 'Esempio',
                'format' => 'text',
                'lang' => 'it',
                'caption_text' => 'Testo semplice',
                'params' => [
                    'key' => 'value',
                ],
            ],
        ];
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $data = [
            'type' => 'videos',
            'id' => '19',
            'attributes' => [
                'captions' => $expected,
            ],
        ];
        $this->patch('/videos/19', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $body = json_decode((string)$this->_response->getBody(), true);

        static::assertEquals($expected, Hash::get((array)$body, 'data.attributes.captions'));
    }

    /**
     * Test delete captions.
     *
     * @return void
     * @coversNothing
     */
    public function testDeleteCaptions(): void
    {
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $data = [
            'type' => 'videos',
            'id' => '19',
            'attributes' => [
                'captions' => null,
            ],
        ];
        $this->patch('/videos/19', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $body = json_decode((string)$this->_response->getBody(), true);

        static::assertEquals([], Hash::get((array)$body, 'data.attributes.captions'));
    }
}
