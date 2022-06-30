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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Media} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Media
 */
class MediaTest extends TestCase
{
    use TestFilesystemTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Streams',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.History',
    ];

    /**
     * Files table.
     *
     * @var \BEdita\Core\Model\Table\MediaTable
     */
    protected $Files;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->filesystemSetup();
        $this->Files = $this->fetchTable('Files');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->filesystemRestore();
    }

    /**
     * Test virtual properties.
     *
     * @return void
     * @covers ::__construct()
     * @covers ::_getMediaUrl()
     */
    public function testMediaUrl()
    {
        $media = $this->Files->get(14, ['contain' => ['Streams']]);

        $url = $media->get('media_url');
        static::assertNotEmpty($url);
        static::assertEquals('https://static.example.org/files/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif', $url);
    }

    /**
     * Test empty media url and provider url fallback in case of no streams.
     *
     * @return void
     * @covers ::_getMediaUrl()
     */
    public function testGetMediaUrl()
    {
        $entity = $this->Files->newEntity([
            'title' => 'New file',
            'media_property' => false,
        ]);
        $entity->created_by = 1;
        $entity->modified_by = 1;
        $entity = $this->Files->saveOrFail($entity);

        $url = $entity->get('media_url');
        static::assertNull($url);

        $providerUrl = 'https://example.com/myfile.zip';
        $entity->set('provider_url', $providerUrl);
        $entity = $this->Files->saveOrFail($entity);
        static::assertEquals($providerUrl, $entity->get('media_url'));
    }
}
