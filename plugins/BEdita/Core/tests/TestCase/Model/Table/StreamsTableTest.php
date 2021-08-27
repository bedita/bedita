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

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\ObjectsTable;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Cake\Utility\Text;

/**
 * @coversDefaultClass \BEdita\Core\Model\Table\StreamsTable
 */
class StreamsTableTest extends TestCase
{
    use TestFilesystemTrait;

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\StreamsTable
     */
    public $Streams;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->Streams = TableRegistry::getTableLocator()->get('Streams');
        $this->filesystemSetup(true, true);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $this->filesystemRestore();
        unset($this->Streams);
        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testInitialization()
    {
        $this->Streams->initialize([]);

        static::assertEquals('streams', $this->Streams->getTable());
        static::assertEquals('uuid', $this->Streams->getPrimaryKey());
        static::assertEquals('uri', $this->Streams->getDisplayField());

        static::assertInstanceOf(BelongsTo::class, $this->Streams->Objects);
        static::assertInstanceOf(ObjectsTable::class, $this->Streams->Objects->getTarget());
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'empty' => [
                [
                    'contents._required',
                    'file_name._required',
                    'mime_type._required',
                ],
                [],
            ],
            'valid' => [
                true,
                [
                    'uuid' => Text::uuid(),
                    'file_name' => 'myFileName.txt',
                    'mime_type' => 'text/plain',
                    'contents' => 'plain text contents',
                ],
            ],
            'not unique' => [
                [
                    'uuid.unique',
                ],
                [
                    'uuid' => 'e5afe167-7341-458d-a1e6-042e8791b0fe',
                    'file_name' => 'myFileName.txt',
                    'mime_type' => 'text/plain',
                    'contents' => 'plain text contents',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param array|bool $expected Expected result.
     * @param array $data Data to be validated.
     * @param string|bool $uuid UUID of stream to patch.
     * @return void
     *
     * @dataProvider validationProvider()
     * @coversNothing
     */
    public function testValidation($expected, array $data, $uuid = false)
    {
        $stream = $this->Streams->newEntity();
        if ($uuid !== false) {
            $stream = $this->Streams->get($uuid);
        }

        $stream = $this->Streams->patchEntity($stream, $data);

        if ($expected === true) {
            static::assertEmpty($stream->getErrors());

            $success = $this->Streams->save($stream);

            static::assertTrue((bool)$success);
        } else {
            $errors = array_keys(Hash::flatten($stream->getErrors()));

            static::assertEquals($expected, $errors, '', 0, 10, true);
        }
    }

    /**
     * Test before save event handler.
     *
     * @return void
     *
     * @covers ::beforeSave()
     */
    public function testBeforeSave()
    {
        $expected = [];
        $data = [
            'file_name' => 'some/path/il mio nuovo file è un dump.sql.gz',
            'mime_type' => 'text/plain',
            'contents' => 'Not really GZipped',
        ];

        $stream = $this->Streams->newEntity();
        $stream = $this->Streams->patchEntity($stream, $data);

        $this->Streams->saveOrFail($stream);
        $expected['uri'] = sprintf('default://%s-il-mio-nuovo-file-e-un-dump.sql.gz', $stream->uuid);
        $result = $stream->extract(array_keys($expected));

        static::assertSame($expected, $result);
    }

    /**
     * Test before save event handler with a custom UUID.
     *
     * @return void
     *
     * @covers ::beforeSave()
     */
    public function testBeforeSaveWithUuid()
    {
        $uuid = Text::uuid();
        $expected = [
            'uuid' => $uuid,
            'uri' => sprintf('default://%s-il-mio-nuovo-file-e-un-dump.sql.gz', $uuid),
        ];
        $data = [
            'file_name' => 'some/path/il mio nuovo file è un dump.sql.gz',
            'mime_type' => 'text/plain',
            'contents' => 'Not really GZipped',
        ];

        $stream = $this->Streams->newEntity();
        $stream->uuid = $uuid;
        $stream = $this->Streams->patchEntity($stream, $data);

        $this->Streams->saveOrFail($stream);
        $result = $stream->extract(array_keys($expected));

        static::assertSame($expected, $result);
    }

    /**
     * Test after save event.
     *
     * @return void
     *
     * @covers ::afterDelete()
     */
    public function testAfterDelete()
    {
        $uuid = 'e5afe167-7341-458d-a1e6-042e8791b0fe';
        $stream = $this->Streams->get($uuid);
        $success = $this->Streams->delete($stream);
        static::assertNotEmpty($success);
    }

    /**
     * Test before save event handler with an already persisted entity.
     *
     * @return void
     *
     * @covers ::beforeSave()
     */
    public function testBeforeSaveNotNew()
    {
        $uuid = '9e58fa47-db64-4479-a0ab-88a706180d59';
        $expected = [
            'uuid' => $uuid,
            'uri' => 'default://9e58fa47-db64-4479-a0ab-88a706180d59.txt',
        ];
        $data = [
            'file_name' => 'new file.sql.gz',
        ];

        $stream = $this->Streams->get($uuid);
        $stream = $this->Streams->patchEntity($stream, $data);

        $this->Streams->saveOrFail($stream);
        $result = $stream->extract(array_keys($expected));

        static::assertSame($expected, $result);
    }
}
