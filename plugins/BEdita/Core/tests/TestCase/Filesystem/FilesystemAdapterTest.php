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
namespace BEdita\Core\Test\TestCase\Filesystem;

use BEdita\Core\Filesystem\FilesystemAdapter;
use Cake\TestSuite\TestCase;
use League\Flysystem\FilesystemAdapter as LeagueFilesystemAdapter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Filesystem\FilesystemAdapter} Test Case
 */
#[CoversClass(FilesystemAdapter::class)]
class FilesystemAdapterTest extends TestCase
{
    /**
     * Test class initialization.
     *
     * @return void
     */
    public function testInitialize()
    {
        $config = [
            'baseUrl' => 'http://example.org',
            'visibility' => 'private',
            'key' => 'value',
        ];

        $leagueAdapter = $this->getMockBuilder(LeagueFilesystemAdapter::class)->getMock();
        $adapter = new class ($leagueAdapter) extends FilesystemAdapter {
            protected LeagueFilesystemAdapter $leagueAdapter;

            public function __construct(LeagueFilesystemAdapter $leagueAdapter)
            {
                $this->leagueAdapter = $leagueAdapter;
            }

            public function buildAdapter(array $config): LeagueFilesystemAdapter
            {
                return $this->leagueAdapter;
            }
        };

        $result = $adapter->initialize($config);

        static::assertTrue($result);
        static::assertSame($config, $adapter->getConfig());
    }

    /**
     * Test inner adapter getter.
     *
     * @return void
     */
    public function testGetInnerAdapter(): void
    {
        $innerAdapter = $this->getMockBuilder(LeagueFilesystemAdapter::class)->getMock();
        $config = [
            'baseUrl' => 'http://example.org',
            'key' => 'value',
            'visibility' => 'private',
            'innerAdapter' => $innerAdapter,
        ];

        $adapter = new class () extends FilesystemAdapter {
            public function buildAdapter(array $config): LeagueFilesystemAdapter
            {
                return $config['innerAdapter'];
            }
        };

        /** @var \BEdita\Core\Filesystem\FilesystemAdapter $adapter */
        $adapter->initialize($config);
        $result = $adapter->getInnerAdapter();
        static::assertSame($innerAdapter, $result);

        // Test that subsequent executions return the same result.
        $result = $adapter->getInnerAdapter();
        static::assertSame($innerAdapter, $result);
    }

    /**
     * Data provider for `testGetPublicUrl` test case.
     *
     * @return array
     */
    public static function getPublicUrlProvider(): array
    {
        return [
            [
                'http://example.org/base/path/to/object.png',
                'http://example.org/base',
                'path/to/object.png',
            ],
            [
                'http://example.org/base/path/to/object.png',
                'http://example.org/base/',
                'path/to/object.png',
            ],
            [
                'http://example.org/base/path/to/object.png',
                'http://example.org/base',
                '/path/to/object.png',
            ],
            [
                'http://example.org/base/path/to/object.png',
                'http://example.org/base/',
                '/path/to/object.png',
            ],
        ];
    }

    /**
     * Test public URL getter.
     *
     * @param string $expected Expected result.
     * @param string $baseUrl Base URL.
     * @param string $path Object path.
     * @return void
     */
    #[DataProvider('getPublicUrlProvider')]
    public function testGetPublicUrl($expected, $baseUrl, $path)
    {
        $leagueAdapter = $this->getMockBuilder(LeagueFilesystemAdapter::class)->getMock();
        $adapter = new class ($leagueAdapter) extends FilesystemAdapter {
            protected LeagueFilesystemAdapter $leagueAdapter;

            public function __construct(LeagueFilesystemAdapter $leagueAdapter)
            {
                $this->leagueAdapter = $leagueAdapter;
            }

            public function buildAdapter(array $config): LeagueFilesystemAdapter
            {
                return $this->leagueAdapter;
            }
        };

        $adapter->initialize(compact('baseUrl'));
        $result = $adapter->getPublicUrl($path);

        static::assertSame($expected, $result);
    }

    /**
     * Test getter for default visibility.
     *
     * @return void
     */
    public function testGetVisibility()
    {
        $leagueAdapter = $this->getMockBuilder(LeagueFilesystemAdapter::class)->getMock();
        $adapter = new class ($leagueAdapter) extends FilesystemAdapter {
            protected LeagueFilesystemAdapter $leagueAdapter;

            public function __construct(LeagueFilesystemAdapter $leagueAdapter)
            {
                $this->leagueAdapter = $leagueAdapter;
            }

            public function buildAdapter(array $config): LeagueFilesystemAdapter
            {
                return $this->leagueAdapter;
            }
        };
        $visibility = 'private';

        $adapter->initialize(compact('visibility'));
        $result = $adapter->getVisibility();

        static::assertSame($visibility, $result);
    }
}
