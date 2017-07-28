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

use BEdita\Core\Filesystem\Adapter\LocalAdapter;
use BEdita\Core\Filesystem\FilesystemAdapter;
use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\TestSuite\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemNotFoundException;
use League\Flysystem\MountManager;

/**
 * @coversDefaultClass \BEdita\Core\Filesystem\FilesystemRegistry
 */
class FilesystemRegistryTest extends TestCase
{

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        FilesystemRegistry::dropAll();
    }

    /**
     * Test static configuration.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testStaticConfiguration()
    {
        $expected = [
            'default' => [
                'baseUrl' => 'http://example.org',
                'scheme' => 'local',
                'path' => '/my/base/path',
                'className' => LocalAdapter::class,
            ],
        ];
        $config = [
            'default' => [
                'url' => 'local:///my/base/path?baseUrl=http://example.org',
            ],
        ];

        FilesystemRegistry::setConfig($config);

        foreach ($expected as $key => $config) {
            static::assertSame($config, FilesystemRegistry::getConfig($key));
        }
    }

    /**
     * Data provider for `testRegistry` test case.
     *
     * @return array
     */
    public function registryProvider()
    {
        $failedInitialization = $this->getMockBuilder(FilesystemAdapter::class)->getMock();
        $failedInitialization
            ->method('initialize')
            ->willReturn(false);

        return [
            'found' => [
                LocalAdapter::class,
                'BEdita/Core.Local',
            ],
            'instance' => [
                LocalAdapter::class,
                'Local',
                [
                    'className' => new LocalAdapter(),
                ],
            ],
            'class not found' => [
                new \BadMethodCallException('Filesystem adapter ThisDoesNotExist is not available.'),
                'BEdita/Core.ThisDoesNotExist',
            ],
            'bad instance' => [
                new \RuntimeException(
                    sprintf('Filesystem adapters must use %s as a base class.', FilesystemAdapter::class)
                ),
                'Bad',
                [
                    'className' => new \stdClass(),
                ],
            ],
            'failed initialization' => [
                new \RuntimeException(
                    sprintf('Filesystem adapter %s is not properly configured', get_class($failedInitialization))
                ),
                'Bad',
                [
                    'className' => $failedInitialization,
                ],
            ],
        ];
    }

    /**
     * Test registry functionality.
     *
     * @param string|\Exception $expected Expected result.
     * @param string $objectName Name of registry object.
     * @param array $config Adapter configuration.
     * @return void
     *
     * @dataProvider registryProvider()
     * @covers ::_resolveClassName()
     * @covers ::_throwMissingClassError()
     * @covers ::_create()
     */
    public function testRegistry($expected, $objectName, array $config = [])
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $result = FilesystemRegistry::getInstance()->load($objectName, $config);

        if (is_string($expected)) {
            static::assertInstanceOf($expected, $result);
        }
    }

    /**
     * Test getter.
     *
     * @return void
     *
     * @covers ::get()
     */
    public function testGet()
    {
        $instance = $this->getMockBuilder(LocalAdapter::class)->getMock();
        $instance->expects(static::once())
            ->method('initialize')
            ->willReturn(true);

        FilesystemRegistry::setConfig('default', [
            'className' => $instance,
        ]);

        $first = FilesystemRegistry::getInstance()->get('default');
        static::assertInstanceOf(LocalAdapter::class, $first);

        // Since we expected `initialize()` method to be invoked exactly one, test should fail if
        // subsequent calls to `get()` instantiate a new object.
        $second = FilesystemRegistry::getInstance()->get('default');
        static::assertSame($first, $second);
    }

    /**
     * Test getter when adapter is missing.
     *
     * @return void
     *
     * @covers ::get()
     */
    public function testGetMissing()
    {
        $result = FilesystemRegistry::getInstance()->get('missing');

        static::assertNull($result);
    }

    /**
     * Test getter for mount manager.
     *
     * @return void
     *
     * @covers ::getMountManager()
     */
    public function testGetMountManager()
    {
        FilesystemRegistry::setConfig([
            'default' => [
                'className' => LocalAdapter::class,
            ],
            'alternative' => [
                'className' => LocalAdapter::class,
            ],
        ]);

        $manager = FilesystemRegistry::getMountManager();

        static::assertInstanceOf(MountManager::class, $manager);
        static::assertInstanceOf(Filesystem::class, $manager->getFilesystem('default'));
        static::assertInstanceOf(Filesystem::class, $manager->getFilesystem('alternative'));
        static::assertAttributeSame($manager, 'mountManager', FilesystemRegistry::getInstance());

        $second = FilesystemRegistry::getMountManager();
        static::assertSame($manager, $second);
    }

    /**
     * Test dropping all configurations.
     *
     * @return void
     *
     * @covers ::dropAll()
     * @expectedException \League\Flysystem\FilesystemNotFoundException
     * @expectedExceptionMessage No filesystem mounted with prefix default
     */
    public function testDropAll()
    {
        FilesystemRegistry::setConfig('default', [
            'className' => LocalAdapter::class,
        ]);
        $manager = FilesystemRegistry::getMountManager();
        FilesystemRegistry::dropAll();

        static::assertAttributeEmpty('_config', FilesystemRegistry::class);
        static::assertAttributeEmpty('_loaded', FilesystemRegistry::getInstance());
        static::assertAttributeEmpty('mountManager', FilesystemRegistry::getInstance());

        $newManager = FilesystemRegistry::getMountManager();

        static::assertNotSame($manager, $newManager);

        $newManager->getFilesystem('default');
    }

    /**
     * Data provider for `testGetPublicUrl` test case.
     *
     * @return array
     */
    public function getPublicUrlProvider()
    {
        return [
            'ok' => [
                'http://example.org/base/path/image.png',
                'default://path/image.png',
                [
                    'baseUrl' => 'http://example.org/base',
                ],
            ],
            'missing prefix' => [
                new \InvalidArgumentException('No prefix detected in path: path/image.png'),
                'path/image.png',
                [],
            ],
            'filesystem not found' => [
                new FilesystemNotFoundException('No filesystem mounted with prefix missing'),
                'missing://path/image.png',
                [],
            ],
        ];
    }

    /**
     * Test getter for public URL.
     *
     * @param string|\Exception $expected
     * @param string $path
     * @param array $config
     * @return void
     *
     * @dataProvider getPublicUrlProvider()
     * @covers ::getPublicUrl()
     * @covers ::getPrefixAndPath()
     */
    public function testGetPublicUrl($expected, $path, array $config)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        FilesystemRegistry::setConfig('default', $config + ['className' => 'BEdita/Core.Local']);

        $result = FilesystemRegistry::getPublicUrl($path);

        static::assertSame($expected, $result);
    }
}
