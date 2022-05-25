<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Filesystem\Thumbnail;

use BEdita\Core\Exception\InvalidDataException;
use BEdita\Core\Filesystem\Exception\InvalidStreamException;
use BEdita\Core\Filesystem\FilesystemRegistry;
use BEdita\Core\Filesystem\Thumbnail\GlideGenerator;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use League\Flysystem\StorageAttributes;

/**
 * @coversDefaultClass \BEdita\Core\Filesystem\Thumbnail\GlideGenerator
 */
class GlideGeneratorTest extends TestCase
{
    use TestFilesystemTrait;

    /**
     * Fixtures.
     *
     * @var string[]
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * Generator instance.
     *
     * @var \BEdita\Core\Filesystem\Thumbnail\GlideGenerator
     */
    protected $generator;

    /**
     * Streams table.
     *
     * @var \BEdita\Core\Model\Table\StreamsTable
     */
    protected $Streams;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->filesystemSetup(false, true);

        $this->Streams = TableRegistry::getTableLocator()->get('Streams');

        $this->generator = new GlideGenerator();
        $this->generator->initialize([]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->filesystemRestore();
        unset($this->generator, $this->Streams);

        parent::tearDown();
    }

    /**
     * Data provider for `testGetUrl` test case.
     *
     * @return array
     */
    public function getUrlProvider()
    {
        return [
            'invalid txt file' => [
                new InvalidDataException('Invalid thumbnail format: txt'),
                '9e58fa47-db64-4479-a0ab-88a706180d59',
                ['fm' => 'txt'],
            ],
            'png file' => [
                'https://static.example.org/thumbs/e5afe167-7341-458d-a1e6-042e8791b0fe-bedita-logo.png/' . sha1(serialize(['w' => 200, 'fm' => 'png'])) . '.png',
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['w' => 200, 'fm' => 'png'],
            ],
            'gif file to default jpg' => [
                'https://static.example.org/thumbs/6aceb0eb-bd30-4f60-ac74-273083b921b6-bedita-logo-gray.gif/' . sha1(serialize(['w' => 200])) . '.jpg',
                '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                ['w' => 200],
            ],
        ];
    }

    /**
     * Test URL getter.
     *
     * @param string $expected Expected URL.
     * @param string $uuid Stream UUID.
     * @param array $options Thumbnail options.
     * @return void
     * @dataProvider getUrlProvider()
     * @covers ::getUrl()
     * @covers ::getFilename()
     */
    public function testGetUrl($expected, $uuid, array $options = [])
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $stream = $this->Streams->get($uuid);

        $url = $this->generator->getUrl($stream, $options);

        static::assertSame($expected, $url);
    }

    /**
     * Data provider for `testExtensionFile` test case.
     *
     * @return array
     */
    public function getExtensionThumb()
    {
        return [
            'png file' => [
                'png',
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['w' => 200, 'fm' => 'png'],
            ],
            'gif file' => [
                'jpg',
                '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                ['w' => 200],
            ],
            'png file to jpg' => [
                'jpg',
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['w' => 200, 'fm' => 'jpg'],
            ],
        ];
    }

    /**
     * Test extension thumb.
     *
     * @param string $expected Expected extension file.
     * @param string $uuid Stream UUID.
     * @param array $options Thumbnail options.
     * @return void
     * @dataProvider getExtensionThumb()
     * @covers ::getUrl()
     * @covers ::getFilename()
     */
    public function testExtensionFile($expected, $uuid, array $options = [])
    {
        $stream = $this->Streams->get($uuid);
        $url = $this->generator->getUrl($stream, $options);
        $result = pathinfo($url, PATHINFO_EXTENSION);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testGenerate` test case.
     *
     * @return array
     */
    public function generateProvider()
    {
        return [
            'text file' => [
                new InvalidStreamException('Unable to generate thumbnail for stream 9e58fa47-db64-4479-a0ab-88a706180d59'),
                '9e58fa47-db64-4479-a0ab-88a706180d59',
            ],
            'png file in txt' => [
                new InvalidDataException('Invalid thumbnail format: txt'),
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['fm' => 'txt'],
            ],
            'png file' => [
                true,
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['w' => 200, 'fm' => 'png'],
            ],
            'gif file to default jpg' => [
                true,
                '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                ['w' => 200],
            ],
        ];
    }

    /**
     * Test thumbnail generation.
     *
     * @param bool|\Exception $expected Expected result.
     * @param string $uuid Stream UUID.
     * @param array $options Thumbnail options.
     * @return void
     * @dataProvider generateProvider()
     * @covers ::generate()
     * @covers ::getFilename()
     * @covers ::getGlideApi()
     * @covers ::makeThumbnail()
     */
    public function testGenerate($expected, $uuid, array $options = [])
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $stream = $this->Streams->get($uuid);

        $result = $this->generator->generate($stream, $options);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testExists` test case.
     *
     * @return array
     */
    public function existsProvider()
    {
        return [
            'not valid' => [
                false,
                '9e58fa47-db64-4479-a0ab-88a706180d59',
            ],
            'existing png file' => [
                true,
                'e5afe167-7341-458d-a1e6-042e8791b0fe',
                ['w' => 200, 'fm' => 'png'],
            ],
        ];
    }

    /**
     * Test existence checks.
     *
     * @param bool $expected Expected result.
     * @param string $uuid Stream UUID.
     * @param array $options Thumbnail options.
     * @return void
     * @dataProvider existsProvider()
     * @covers ::exists()
     * @covers ::getFilename()
     */
    public function testExists($expected, $uuid, array $options = [])
    {
        $stream = $this->Streams->get($uuid);

        $result = $this->generator->exists($stream, $options);

        static::assertSame($expected, $result);
    }

    /**
     * Test deletion of thumbnails.
     *
     * @return void
     * @covers ::delete()
     */
    public function testDelete()
    {
        $uuid = 'e5afe167-7341-458d-a1e6-042e8791b0fe';
        $path = 'e5afe167-7341-458d-a1e6-042e8791b0fe-bedita-logo.png';

        $stream = $this->Streams->get($uuid);

        static::assertContains($path, $this->thumbnailPaths());

        $this->generator->delete($stream);

        static::assertNotContains($path, $this->thumbnailPaths());
    }

    protected function thumbnailPaths(): array
    {
        return array_map(
            function (StorageAttributes $object) {
                return str_replace('thumbnails://', '', $object->path());
            },
            FilesystemRegistry::getMountManager()->listContents('thumbnails://')->toArray()
        );
    }
}
