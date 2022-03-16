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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Filesystem\FilesystemRegistry;
use BEdita\Core\Model\Entity\Stream as EntityStream;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\StreamInterface;

/**
 * @coversDefaultClass \BEdita\Core\Model\Entity\Stream
 */
class StreamTest extends TestCase
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
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->filesystemSetup();
        $this->Streams = TableRegistry::getTableLocator()->get('Streams');
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
     * Data provider for `testFilesystemPath` test case.
     *
     * @return array
     */
    public function filesystemPathProvider()
    {
        $uuid = Text::uuid();
        $hash = sha1($uuid);
        $hash = [
            substr($hash, 0, 2),
            substr($hash, 2, 2),
            substr($hash, 4, 2),
        ];

        return [
            'no file name' => [
                "default://{$uuid}",
                compact('uuid'),
            ],
            'weird file name' => [
                "default://{$uuid}-il-mio-nuovo-file-e-un-dump.sql.gz",
                [
                    'uuid' => $uuid,
                    'file_name' => 'some/path/il mio nuovo file è un dump.sql.gz',
                ],
            ],
            'another filesystem' => [
                "test://{$uuid}-my-file.pdf",
                [
                    'uuid' => $uuid,
                    'file_name' => 'my File.PDF',
                ],
                'test',
                -19,
            ],
            'organize in sub-levels' => [
                "default://{$hash[0]}/{$uuid}",
                compact('uuid'),
                'default',
                1,
            ],
            'multiple sub-levels' => [
                "test://{$hash[0]}/{$hash[1]}/{$hash[2]}/{$uuid}",
                compact('uuid'),
                'test',
                3.99999,
            ],
        ];
    }

    /**
     * Test method to generate filesystem path.
     *
     * @param string $expected Expected result.
     * @param array $data Properties to be forcibly set to the entity.
     * @param string $filesystem Filesystem name.
     * @param int $subLevels Number of sub-levels.
     * @return void
     * @dataProvider filesystemPathProvider()
     * @covers ::filesystemPath()
     */
    public function testFilesystemPath($expected, array $data, $filesystem = 'default', $subLevels = 0)
    {
        $stream = $this->Streams->newEntity([]);
        $stream->set($data, ['guard' => false]);

        $path = $stream->filesystemPath($filesystem, $subLevels);

        static::assertSame($expected, $path);
    }

    /**
     * Test getter of contents.
     *
     * @return void
     * @covers ::_getContents()
     */
    public function testGetContents()
    {
        $stream = $this->Streams->get('9e58fa47-db64-4479-a0ab-88a706180d59');
        $fileContents = FilesystemRegistry::getMountManager()->read($stream->uri);
        $first = $stream->contents;
        $second = $stream->contents;

        static::assertInstanceOf(StreamInterface::class, $first);
        static::assertSame($first, $second);
        static::assertSame($fileContents, (string)$first);
    }

    /**
     * Test getter of contents for a missing file.
     *
     * @return void
     * @covers ::_getContents()
     */
    public function testGetContentsFileNotReadable()
    {
        $stream = $this->Streams->get('9e58fa47-db64-4479-a0ab-88a706180d59');
        $stream->uri = 'default://missing-file.txt';
        $contents = $stream->contents;

        static::assertNull($contents);
    }

    /**
     * Test getter of contents for a file not uploaded.
     *
     * @return void
     * @covers ::_getContents()
     */
    public function testGetContentsNotUploaded()
    {
        $stream = $this->Streams->newEntity([]);
        $contents = $stream->contents;

        static::assertNull($contents);
    }

    /**
     * Data provider for `testSetContents` test case.
     *
     * @return array
     */
    public function setContentsProvider()
    {
        static $exceptionMessage = 'Invalid contents provided, must be a PSR-7 stream, a resource or a value that can be converted to string';

        $stream = new Stream('php://memory', 'wb+');
        $stream->write('this is a stream');
        $stream->rewind();

        $resource = fopen('php://memory', 'wb+');
        fwrite($resource, 'this is a resource');
        fseek($resource, 0);

        $serializable = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['__toString'])
            ->getMock();
        $serializable
            ->method('__toString')
            ->willReturn('this is an object that can be converted to string');

        return [
            'PSR-7 stream' => [
                'this is a stream',
                $stream,
            ],
            'PHP resource' => [
                'this is a resource',
                $resource,
            ],
            'string' => [
                'this is a string',
                'this is a string',
            ],
            'number' => [
                '123.45',
                123.45,
            ],
            'null' => [
                '',
                null,
            ],
            'object' => [
                'this is an object that can be converted to string',
                $serializable,
            ],
            'array' => [
                new \InvalidArgumentException($exceptionMessage),
                [1, 2, 3],
            ],
            'hash' => [
                new \InvalidArgumentException($exceptionMessage),
                [
                    'hello' => 'it\'s me',
                ],
            ],
            'other object' => [
                new \InvalidArgumentException($exceptionMessage),
                new \stdClass(),
            ],
        ];
    }

    /**
     * Test setter for file contents.
     *
     * @param \Exception|string $expected Expected stream contents.
     * @param mixed $contents Contents.
     * @return void
     * @dataProvider setContentsProvider()
     * @covers ::_setContents()
     * @covers ::createStream()
     */
    public function testSetContents($expected, $contents)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $stream = $this->Streams->newEntity([]);
        $stream->contents = $contents;

        static::assertInstanceOf(StreamInterface::class, $stream->contents);
        static::assertSame($expected, (string)$stream->contents);
        static::assertSame(strlen($expected), $stream->file_size);
        static::assertSame(md5($expected), $stream->hash_md5);
        static::assertSame(sha1($expected), $stream->hash_sha1);

        static::assertTrue($stream->isDirty('contents'));
        static::assertTrue($stream->isDirty('file_size'));
        static::assertTrue($stream->isDirty('hash_md5'));
        static::assertTrue($stream->isDirty('hash_sha1'));
    }

    /**
     * Data provider for `testGetUrl` test case.
     *
     * @return array
     */
    public function getUrlProvider()
    {
        return [
            'available' => [
                'https://static.example.org/files/9e58fa47-db64-4479-a0ab-88a706180d59.txt',
                '9e58fa47-db64-4479-a0ab-88a706180d59',
            ],
        ];
    }

    /**
     * Test URL getter.
     *
     * @param string|null $expected Expected result.
     * @param string $uuid UUID of stream to test.
     * @return void
     * @dataProvider getUrlProvider()
     * @covers ::_getUrl()
     */
    public function testGetUrl($expected, $uuid)
    {
        $stream = $this->Streams->get($uuid);
        $first = $stream->url;
        // Overwrite URI to check that URL is actually being cached. :)
        $stream->uri = 'default://missing-file.txt';
        $second = $stream->url;

        static::assertSame($expected, $first);
        static::assertSame($expected, $second);
    }

    /**
     * Test URL getter with private url.
     *
     * @return void
     * @covers ::_getUrl()
     */
    public function testGetUrlPrivate()
    {
        $stream = $this->Streams->get('9e58fa47-db64-4479-a0ab-88a706180d59');
        $stream->private_url = true;
        static::assertNull($stream->get('url'));
    }

    /**
     * Read data from image if is possible
     *
     * @param Stream $stream stream entity
     */

    public function readDataFromImage($stream): void
    {
        if (!preg_match('/image\//', $stream->mime_type)) {
            static::assertNull($stream->width);
            static::assertNull($stream->height);
        } else {
            if (function_exists('getimagesizefromstring')) {
                static::assertNotNull($stream->width);
                static::assertNotNull($stream->height);
            } else {
                static::assertNull($stream->width);
                static::assertNull($stream->height);
            }

            if (function_exists('exif_read_data') && in_array($stream->mime_type, EntityStream::EXIF_MIME_TYPES)) {
                static::assertNotNull($stream->file_metadata);
            } else {
                static::assertEmpty($stream->file_metadata);
            }
        }
    }

    /**
     * Test read exif data
     *
     * @covers ::readFileMetadata()
     * @covers ::createStream()
     */
    public function testReadFileMetadata()
    {
        $path = Configure::read('Filesystem.default.path');
        $imageTest = new Stream($path . '/a4fbe302-3d5b-4774-a9df-18598def690e-image-metadata.jpeg', 'r');
        $gifTest = new Stream($path . '/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif', 'r');

        $stream = $this->Streams->newEntity([]);
        $stream->mime_type = 'image/jpeg';
        $stream->contents = $imageTest;

        $this->readDataFromImage($stream);
        // mime type not allowed
        $stream = $this->Streams->newEntity([]);
        $stream->mime_type = 'image/gif';
        $stream->contents = $gifTest;
        $this->readDataFromImage($stream);
    }

    /**
     * Test failed read exif data
     *
     * @covers ::readFileMetadata()
     * @covers ::createStream()
     */
    public function testFailedReadFileMetadata()
    {
        $path = Configure::read('Filesystem.default.path');
        $gifTest = new Stream($path . '/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif', 'r');
        $stream = $this->Streams->newEntity([]);
        $stream->mime_type = 'image/jpeg';
        $stream->contents = $gifTest;

        static::assertEmpty($stream->file_metadata);
    }
}
