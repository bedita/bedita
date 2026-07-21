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
namespace BEdita\Core\Test\TestCase\Filesystem\Adapter;

use BEdita\Core\Filesystem\Adapter\LocalAdapter;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use League\Flysystem\Config;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\Filesystem\Adapter\LocalAdapter} Test Case
 */
#[CoversClass(LocalAdapter::class)]
class LocalAdapterTest extends TestCase
{
    /**
     * Temporary test files path, removed after test
     *
     * @var string
     */
    public const FILES_PATH = WWW_ROOT . 'static-files' . DS . 'subdir';

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        if (file_exists(self::FILES_PATH)) {
            rmdir(self::FILES_PATH);
            rmdir(WWW_ROOT . 'static-files');
        }

        parent::tearDown();
    }

    /**
     * Test adapter initialization.
     *
     * @return void
     */
    public function testInitialize()
    {
        $fullBaseUrl = 'http://example.org/base';
        $expectedBaseUrl = 'http://example.org/base/static-files/subdir';
        $path = self::FILES_PATH;
        $expectedPublicUrl = 'http://example.org/base/static-files/subdir/myObject/image.png';
        $objectPath = '/myObject/image.png';

        Router::fullBaseUrl($fullBaseUrl);

        $adapter = new LocalAdapter();
        $adapter->initialize(compact('path'));

        static::assertSame($expectedBaseUrl, $adapter->getConfig('baseUrl'));
        static::assertSame($expectedPublicUrl, $adapter->getPublicUrl($objectPath));
    }

    /**
     * Test builder of inner adapter.
     *
     * @return void
     */
    public function testBuildAdapter()
    {
        $config = [
            'path' => self::FILES_PATH,
        ];

        $adapter = new LocalAdapter();
        $adapter->initialize($config);

        $innerAdapter = $adapter->getInnerAdapter();

        static::assertInstanceOf(LocalFilesystemAdapter::class, $innerAdapter);
    }

    /**
     * Directories created implicitly while writing a file (e.g. nested thumbnail
     * directories) must use the adapter's configured default visibility, not always
     * fall back to "private", regardless of the `permissions` configuration.
     *
     * @return void
     */
    public function testWriteUsesDefaultVisibilityForImplicitDirectories()
    {
        $config = [
            'path' => self::FILES_PATH,
            'permissions' => [
                'dir' => ['public' => 0755, 'private' => 0700],
            ],
        ];

        $adapter = new LocalAdapter();
        $adapter->initialize($config);

        $innerAdapter = $adapter->getInnerAdapter();
        $innerAdapter->write('nested/file.txt', 'content', new Config());

        $permissions = fileperms(self::FILES_PATH . DS . 'nested') & 0777;
        static::assertSame(0755, $permissions);

        unlink(self::FILES_PATH . DS . 'nested' . DS . 'file.txt');
        rmdir(self::FILES_PATH . DS . 'nested');
    }
}
