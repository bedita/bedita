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
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Table\MediaTable Test Case
 */
class MediaTableTest extends TestCase
{
    use TestFilesystemTrait;

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\MediaTable
     */
    public $Media;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.History',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Media = TableRegistry::getTableLocator()->get('Media');
        LoggedUser::setUserAdmin();
        $this->filesystemSetup(true, true);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->filesystemRestore();
        unset($this->Media);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Data provider for `testSave` test case.
     *
     * @return array
     */
    public function saveProvider()
    {
        return [
            'valid' => [
                false,
                [
                    'name' => 'Cool media file',
                    'width' => null,
                    'height' => null,
                    'duration' => null,
                    'provider' => null,
                    'provider_uid' => null,
                    'provider_url' => null,
                    'provider_thumbnail' => null,
                    'media_property' => false,
                ],
            ],
            'notUniqueUname' => [
                true,
                [
                    'name' => 'Cooler media file',
                    'width' => null,
                    'height' => null,
                    'duration' => null,
                    'provider' => null,
                    'provider_uid' => null,
                    'provider_url' => null,
                    'provider_thumbnail' => null,
                    'media_property' => false,
                    'uname' => 'media-one',
                ],
            ],
        ];
    }

    /**
     * Test entity save.
     *
     * @param bool $changed
     * @param array $data
     * @return void
     * @dataProvider saveProvider
     * @coversNothing
     */
    public function testSave(bool $changed, array $data)
    {
        $entity = $this->Media->newEntity($data);
        $entity->object_type_id = 9;
        $success = (bool)$this->Media->save($entity);

        $this->assertTrue($success, print_r($entity->getErrors(), true));

        if ($changed) {
            $this->assertNotEquals($data['uname'], $entity->uname);
        } elseif (isset($data['uname'])) {
            $this->assertEquals($data['uname'], $entity->uname);
        }
    }

    /**
     * Test delete of a media with stream(s).
     *
     * @return void
     * @covers ::beforeDelete()
     * @covers ::afterDelete()
     */
    public function testDeleteMediaAndStream(): void
    {
        // create media
        $media = $this->Media->newEntity([
            'name' => 'A media file',
            'width' => null,
            'height' => null,
            'duration' => null,
            'provider' => null,
            'provider_uid' => null,
            'provider_url' => null,
            'provider_thumbnail' => null,
            'media_property' => false,
            'uname' => 'a-media-file',
        ]);
        $media->object_type_id = 9;
        $this->Media->save($media);
        $id = $media->id;

        // create stream and attach it to the media
        $streamsTable = $this->fetchTable('Streams');
        $entity = $streamsTable->newEmptyEntity();
        $action = new SaveEntityAction(['table' => $streamsTable]);
        $data = [
            'file_name' => 'sample-to-delete.txt',
            'mime_type' => 'text/plain',
            'contents' => 'This is a sample file to delete.',
        ];
        $entity->set('object_id', $id);
        $entity->set('private_url', false);
        $data = $action(compact('entity', 'data'));

        // load streams into media entity
        $this->Media->loadInto($media, ['Streams']);
        $streams = (array)$media->get('streams');

        // delete media
        unset($media->streams);
        $this->Media->delete($media);

        // check that media and streams are deleted
        $result = $this->Media->find()->where(['id' => $id])->first();
        $this->assertNull($result);
        foreach ($streams as $stream) {
            $result = $streamsTable->find()->where(['uuid' => $stream->uuid])->first();
            $this->assertNull($result);
        }
    }
}
