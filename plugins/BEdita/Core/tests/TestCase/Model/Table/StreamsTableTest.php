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
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Entity\Stream;
use BEdita\Core\Model\Table\ObjectsTable;
use BEdita\Core\Model\Table\StreamsTable;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * {@see \BEdita\Core\Model\Table\StreamsTable} Test Case
 */
#[CoversClass(StreamsTable::class)]
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
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Streams = TableRegistry::getTableLocator()->get('Streams');
        $this->filesystemSetup(true, true);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->filesystemRestore();
        unset($this->Streams);
        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     */
    public function testInitialization()
    {
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
    public static function validationProvider(): array
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
     */
    #[DataProvider('validationProvider')]
    public function testValidation($expected, array $data, $uuid = false)
    {
        $stream = $this->Streams->newEmptyEntity();
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
            sort($errors);
            sort($expected);
            static::assertEquals($expected, $errors, '');
            static::assertEqualsCanonicalizing($expected, $errors, '');
            static::assertEqualsWithDelta($expected, $errors, 0, '');
        }
    }

    /**
     * Test before save event handler.
     *
     * @return void
     */
    public function testBeforeSave()
    {
        $expected = [];
        $data = [
            'file_name' => 'some/path/il mio nuovo file è un dump.sql.gz',
            'mime_type' => 'text/plain',
            'contents' => 'Not really GZipped',
        ];

        $stream = $this->Streams->newEmptyEntity();
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

        $stream = $this->Streams->newEmptyEntity();
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

    /**
     * Data provider for {@see StreamsTableTest::testNextVersion()}.
     *
     * @return array
     */
    public static function nextVersionProvider(): array
    {
        return [
            'object with existing stream' => [2, 10],
            'object with no streams' => [1, 999],
        ];
    }

    /**
     * Test {@see \BEdita\Core\Model\Table\StreamsTable::nextVersion()}.
     *
     * @param int $expected Expected next version number.
     * @param int $objectId Object ID to query.
     * @return void
     */
    #[DataProvider('nextVersionProvider')]
    public function testNextVersion(int $expected, int $objectId): void
    {
        static::assertSame($expected, $this->Streams->nextVersion($objectId));
    }

    /**
     * Test {@see \BEdita\Core\Model\Table\StreamsTable::clone()} method.
     *
     * @param string $uuid UUID of the Stream to clone.
     * @return void
     */
    #[TestWith(['e5afe167-7341-458d-a1e6-042e8791b0fe'])]
    #[TestWith(['9e58fa47-db64-4479-a0ab-88a706180d59'])]
    #[TestWith(['6aceb0eb-bd30-4f60-ac74-273083b921b6'])]
    public function testClone(string $uuid): void
    {
        $src = $this->Streams->get($uuid);
        $expected = $src->extract(Stream::FILE_PROPERTIES);

        $clone = $this->Streams->clone($src);
        $actual = $clone->extract(Stream::FILE_PROPERTIES);

        static::assertNotSame($src, $clone, 'Cloned stream is the same entity as the source stream');
        static::assertTrue($this->Streams->exists(['uuid' => $clone->uuid]), 'Cloned stream has not been persisted');
        static::assertNotSame($src->uuid, $clone->uuid, 'Cloned stream has the same UUID as the source stream');
        static::assertNull($clone->object_id, 'Cloned stream must not be linked to any object');
        static::assertSame($src->contents->getContents(), $clone->contents->getContents(), 'Cloned stream must have the same file contents');
        static::assertSame($expected, $actual, 'Cloned stream must preserve property values');
    }
}
