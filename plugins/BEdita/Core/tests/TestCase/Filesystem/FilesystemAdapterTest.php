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

namespace BEdita\Core\Test\TestCase\Filesystem;

use BEdita\Core\Filesystem\FilesystemAdapter;
use Cake\TestSuite\TestCase;
use League\Flysystem\AdapterInterface;

/**
 * @coversDefaultClass \BEdita\Core\Filesystem\FilesystemAdapter
 */
class FilesystemAdapterTest extends TestCase
{

    /**
     * Test class initialization.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testInitialize()
    {
        $config = [
            'baseUrl' => 'http://example.org',
            'visibility' => 'private',
            'key' => 'value',
        ];

        /* @var \BEdita\Core\Filesystem\FilesystemAdapter $adapter */
        $adapter = $this->getMockForAbstractClass(FilesystemAdapter::class);

        $result = $adapter->initialize($config);

        static::assertTrue($result);
        static::assertSame($config, $adapter->getConfig());
        static::assertAttributeSame($config, '_config', $adapter);
    }

    /**
     * Data provider for `testGetInnerAdapter` test case.
     *
     * @return array
     */
    public function getInnerAdapterProvider()
    {
        return [
            'ok' => [
                true,
                $this->getMockBuilder(AdapterInterface::class)->getMock(),
            ],
            'wrong class' => [
                new \RuntimeException('Filesystem adapters must use League\Flysystem\AdapterInterface as a base class.'),
                new \stdClass(),
            ],
            'definitely not an object' => [
                new \RuntimeException('Filesystem adapters must use League\Flysystem\AdapterInterface as a base class.'),
                [null, 'gustavo supporto'],
            ],
        ];
    }

    /**
     * Test inner adapter getter.
     *
     * @param \Exception|bool $expected Expected result.
     * @param mixed $innerAdapter Built inner adapter.
     * @return void
     *
     * @dataProvider getInnerAdapterProvider()
     * @covers ::getInnerAdapter()
     */
    public function testGetInnerAdapter($expected, $innerAdapter)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $config = [
            'baseUrl' => 'http://example.org',
            'key' => 'value',
            'visibility' => 'private',
        ];

        $adapter = $this->getMockForAbstractClass(FilesystemAdapter::class);

        $adapter->expects(static::once())
            ->method('buildAdapter')
            ->willReturn($innerAdapter)
            ->with(static::equalTo($config));

        /* @var \BEdita\Core\Filesystem\FilesystemAdapter $adapter */
        $adapter->initialize($config);
        $result = $adapter->getInnerAdapter();
        if ($expected === true) {
            static::assertSame($innerAdapter, $result);

            // Test that subsequent executions return the same result.
            $result = $adapter->getInnerAdapter();
            static::assertSame($innerAdapter, $result);
        }
    }

    /**
     * Data provider for `testGetPublicUrl` test case.
     *
     * @return array
     */
    public function getPublicUrlProvider()
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
     *
     * @dataProvider getPublicUrlProvider()
     * @covers ::getPublicUrl()
     */
    public function testGetPublicUrl($expected, $baseUrl, $path)
    {
        /* @var \BEdita\Core\Filesystem\FilesystemAdapter $adapter */
        $adapter = $this->getMockForAbstractClass(FilesystemAdapter::class);

        $adapter->initialize(compact('baseUrl'));
        $result = $adapter->getPublicUrl($path);

        static::assertSame($expected, $result);
    }

    /**
     * Test getter for default visibility.
     *
     * @return void
     *
     * @covers ::getVisibility()
     */
    public function testGetVisibility()
    {
        /* @var \BEdita\Core\Filesystem\FilesystemAdapter $adapter */
        $adapter = $this->getMockForAbstractClass(FilesystemAdapter::class);
        $visibility = 'private';

        $adapter->initialize(compact('visibility'));
        $result = $adapter->getVisibility();

        static::assertSame($visibility, $result);
    }
}
