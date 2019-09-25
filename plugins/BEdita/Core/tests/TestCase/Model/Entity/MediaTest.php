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

use BEdita\Core\Filesystem\FilesystemRegistry;
use BEdita\Core\Model\Entity\Media;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Media} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Media
 */
class MediaTest extends TestCase
{
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
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        FilesystemRegistry::setConfig(Configure::read('Filesystem'));
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        FilesystemRegistry::dropAll();
        parent::tearDown();
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
        $media = TableRegistry::get('Files')->get(14, ['contain' => ['Streams']]);

        $url = $media->get('media_url');
        static::assertNotEmpty($url);
        static::assertEquals('https://static.example.org/files/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif', $url);
    }

    /**
     * Test empty media url.
     *
     * @return void
     * @covers ::_getMediaUrl()
     */
    public function testEmptyMediaUrl()
    {
        $Files = TableRegistry::get('Files');
        $entity = $Files->newEntity(['title' => 'New file']);
        $entity->created_by = 1;
        $entity->modified_by = 1;
        $entity = $Files->saveOrFail($entity);

        $url = $entity->get('media_url');
        static::assertNull($url);
    }
}
