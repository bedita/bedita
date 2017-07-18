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
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

/**
 * @coversDefaultClass \BEdita\Core\Model\Entity\Stream
 */
class StreamTest extends TestCase
{

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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.streams',
    ];

    /**
     * List of files to keep in test filesystem, and their contents.
     *
     * @var \Cake\Collection\Collection
     */
    private $keep = [];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        FilesystemRegistry::setConfig(Configure::read('Filesystem'));
        $this->Streams = TableRegistry::get('Streams');

        $mountManager = FilesystemRegistry::getMountManager();
        $this->keep = collection($mountManager->listContents('default://'))
            ->map(function (array $object) use ($mountManager) {
                $path = sprintf('%s://%s', $object['filesystem'], $object['path']);
                $contents = fopen('php://memory', 'wb+');
                fwrite($contents, $mountManager->read($path));
                fseek($contents, 0);

                return compact('contents', 'path');
            })
            ->compile();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        // Cleanup test filesystem.
        $mountManager = FilesystemRegistry::getMountManager();
        $keep = $this->keep
            ->each(function (array $object) use ($mountManager) {
                $mountManager->putStream($object['path'], $object['contents']);
            })
            ->map(function (array $object) {
                return $object['path'];
            })
            ->toList();
        collection($mountManager->listContents('default://'))
            ->map(function (array $object) {
                return sprintf('%s://%s', $object['filesystem'], $object['path']);
            })
            ->reject(function ($uri) use ($keep) {
                return in_array($uri, $keep);
            })
            ->each([$mountManager, 'delete']);

        unset($this->Streams);
        FilesystemRegistry::dropAll();

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
     *
     * @dataProvider filesystemPathProvider()
     * @covers ::filesystemPath()
     */
    public function testFilesystemPath($expected, array $data, $filesystem = 'default', $subLevels = 0)
    {
        $stream = $this->Streams->newEntity();
        $stream->set($data, ['guard' => false]);

        $path = $stream->filesystemPath($filesystem, $subLevels);

        static::assertSame($expected, $path);
    }

    /**
     * Test getter of contents.
     *
     * @return void
     *
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
     *
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
     *
     * @covers ::_getContents()
     */
    public function testGetContentsNotUploaded()
    {
        $stream = $this->Streams->newEntity();
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
     *
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

        $stream = $this->Streams->newEntity();
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
            'not available' => [
                null,
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
            ],
        ];
    }

    /**
     * Test URL getter.
     *
     * @param string|null $expected Expected result.
     * @param string $uuid UUID of stream to test.
     * @return void
     *
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
}
