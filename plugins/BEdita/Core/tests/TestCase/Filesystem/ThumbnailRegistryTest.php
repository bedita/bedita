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

namespace BEdita\Core\Test\TestCase\Filesystem;

use BEdita\Core\Filesystem\ThumbnailGenerator;
use BEdita\Core\Filesystem\ThumbnailRegistry;
use BEdita\Core\Test\TestCase\Filesystem\Thumbnail\TestGenerator;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Filesystem\ThumbnailRegistry
 */
class ThumbnailRegistryTest extends TestCase
{

    /**
     * Test load when everything goes just fine.
     *
     * @return void
     *
     * @covers ::_create()
     * @covers ::_resolveClassName()
     */
    public function testLoad()
    {
        $config = [
            'my' => 'config',
        ];

        $registry = new ThumbnailRegistry();

        $generator = $registry->load('test', $config + ['className' => TestGenerator::class]);

        static::assertInstanceOf(ThumbnailGenerator::class, $generator);
        static::assertSame($config, $generator->getConfig());
        static::assertTrue($registry->has('test'));
        static::assertSame($generator, $registry->get('test'));
    }

    /**
     * Test `_create` method when generator is not an instance of the expected class.
     *
     * @return void
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Thumbnail generators must use .+ as a base class\.$/
     * @covers ::_create()
     * @covers ::_resolveClassName()
     */
    public function testLoadNotAGenerator()
    {
        $object = new \stdClass();

        $registry = new ThumbnailRegistry();

        $registry->load('test', ['className' => $object]);
    }

    /**
     * Test `_create` method when generator initialization fails.
     *
     * @return void
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Thumbnail generator .+ is not properly configured\.$/
     * @covers ::_create()
     * @covers ::_resolveClassName()
     */
    public function testLoadNotInitialized()
    {
        $config = [
            'my' => 'config',
        ];

        $mock = $this->getMockBuilder(ThumbnailGenerator::class)
            ->setMethods(['initialize'])
            ->getMockForAbstractClass();
        $mock->method('initialize')
            ->with($config)
            ->willReturn(false);

        $registry = new ThumbnailRegistry();

        $registry->load('test', $config + ['className' => $mock]);
    }

    /**
     * Test `_throwMissingClassError` method.
     *
     * @return void
     *
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessageRegExp /^Thumbnail generator .+ is not available\.$/
     * @covers ::_throwMissingClassError()
     */
    public function testLoadMissingClass()
    {
        $registry = new ThumbnailRegistry();

        $registry->load('\This\Class\Does\Not\Exist');
    }
}
