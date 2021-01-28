<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Cache\Engine;

use Cake\Cache\Cache;
use Cake\Cache\Engine\ArrayEngine;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * {@see \BEdita\Core\Cache\Engine\LayeredEngine} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Cache\Engine\LayeredEngine
 */
class LayeredEngineTest extends TestCase
{
    /**
     * Default cache engine config
     *
     * @var array
     */
    public $defaultConfig = [
        'className' => 'BEdita/Core.Layered',
        'prefix' => 'test-layered-',
        'persistent' => [
            'className' => 'Array',
            'prefix' => 'test-layered-persistent-',
        ],
    ];

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        Cache::enable();
        Cache::drop('layered');
        Cache::setConfig('layered', $this->defaultConfig);
        Cache::clearAll();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        foreach (Cache::configured() as $name) {
            Cache::drop($name);
        }

        foreach (Cache::getRegistry()->loaded() as $name) {
            Cache::getRegistry()->unload($name);
        }
    }

    /**
     * Test cache init.
     *
     * @throws Exception
     * @covers ::init()
     */
    public function testInit()
    {
        $layered = Cache::getRegistry()->get('layered');
        static::assertAttributeInstanceOf(ArrayEngine::class, 'memory', $layered);
        static::assertAttributeInstanceOf(ArrayEngine::class, 'persistent', $layered);
    }

    /**
     * Test using an alias for persistent cache.
     *
     * @covers ::getEngineInstance()
     */
    public function testPersistentAlias()
    {
        Cache::setConfig('array-alias', ['className' => 'Array']);
        Cache::setConfig('layered-alias', array_merge(
            $this->defaultConfig,
            ['persistent' => 'array-alias']
        ));

        $result = Cache::write('secret', 42, 'array-alias');
        static::assertTrue($result);

        $result = Cache::read('secret', 'layered-alias');
        static::assertSame(42, $result);
    }

    /**
     * Test using a bad persistent config.
     *
     * @covers ::getEngineInstance()
     */
    public function testPersistentBadConfig()
    {
        static::expectException(Exception::class);
        static::expectExceptionMessage('Unknown cache configuration');
        Cache::setConfig('layered-bad-persistent', array_merge(
            $this->defaultConfig,
            ['persistent' => 1]
        ));
        Cache::write('secret', 42, 'layered-bad-persistent');
    }

    /**
     * Test using an nonexistent alias for persistent cache.
     *
     * @covers ::getEngineInstance()
     */
    public function testPersistentMissingAlias()
    {
        static::expectException(Exception::class);
        static::expectExceptionMessage("Cache engine alias 'this-does-not-exist' is not defined");
        Cache::setConfig('layered-missing-alias', array_merge(
            $this->defaultConfig,
            ['persistent' => 'this-does-not-exist']
        ));
        Cache::write('secret', 42, 'layered-missing-alias');
    }

    /**
     * Test using the engine as persistent engine of itself.
     *
     * @covers ::getEngineInstance()
     */
    public function testPersistentRecursive()
    {
        static::expectException(Exception::class);
        Cache::setConfig('layered-recursive', array_merge(
            $this->defaultConfig,
            ['persistent' => 'layered-recursive']
        ));
        Cache::write('secret', 42, 'layered-recursive');
    }

    /**
     * Test using an alias to wrong object type as persistent engine.
     *
     * @covers ::getEngineInstance()
     */
    public function testPersistentWrongObject()
    {
        static::expectException(Exception::class);
        static::expectExceptionMessage("Cache engine alias 'another-object' is not an implementation of CacheEngine");
        Cache::getRegistry()->set('another-object', new \stdClass());
        Cache::setConfig('layered-wrong-object', array_merge(
            $this->defaultConfig,
            ['persistent' => 'another-object']
        ));;
        Cache::write('secret', 42, 'layered-wrong-object');
    }

    /**
     * Test cache write and read.
     *
     * @covers ::write()
     * @covers ::read()
     */
    public function testWriteAndRead()
    {
        $result = Cache::write('secret', 42, 'layered');
        static::assertTrue($result);

        $result = Cache::read('secret', 'layered');
        static::assertSame(42, $result);
    }

    /**
     * Test cache read, with miss in memory engine.
     *
     * @covers ::read()
     */
    public function testMemoryCacheMiss()
    {
        Cache::setConfig('array-miss', ['className' => 'Array']);
        Cache::setConfig('layered-miss', array_merge(
            $this->defaultConfig,
            ['persistent' => 'array-miss']
        ));

        $result = Cache::write('secret', 42, 'array-miss');
        static::assertTrue($result);

        $result = Cache::read('secret', 'layered-miss');
        static::assertSame(42, $result);
    }

    /**
     * Test cache increment.
     *
     * @covers ::increment()
     */
    public function testIncrement()
    {
        $result = Cache::write('increment', 42, 'layered');
        static::assertTrue($result);

        $result = Cache::increment('increment', 1, 'layered');
        static::assertSame(43, $result);

        $result = Cache::read('increment', 'layered');
        static::assertSame(43, $result);

        $result = Cache::increment('increment', 2, 'layered');
        static::assertSame(45, $result);

        $result = Cache::read('increment', 'layered');
        static::assertSame(45, $result);
    }

    /**
     * Test cache decrement.
     *
     * @covers ::decrement()
     */
    public function testDecrement()
    {
        $result = Cache::write('decrement', 42, 'layered');
        static::assertTrue($result);

        $result = Cache::decrement('decrement', 1, 'layered');
        static::assertSame(41, $result);

        $result = Cache::read('decrement', 'layered');
        static::assertSame(41, $result);

        $result = Cache::decrement('decrement', 2, 'layered');
        static::assertSame(39, $result);

        $result = Cache::read('decrement', 'layered');
        static::assertSame(39, $result);
    }

    /**
     * Test cache delete.
     *
     * @covers ::delete()
     */
    public function testDelete()
    {
        $result = Cache::write('delete', 42, 'layered');
        static::assertTrue($result);

        $result = Cache::delete('delete', 'layered');
        static::assertTrue($result);

        $result = Cache::read('delete', 'layered');
        static::assertFalse($result);
    }

    /**
     * Test cache clear.
     *
     * @covers ::clear()
     */
    public function testClear()
    {
        $result = Cache::write('clear', 42, 'layered');
        static::assertTrue($result);

        $result = Cache::clear(false, 'layered');
        static::assertTrue($result);

        $result = Cache::read('clear', 'layered');
        static::assertFalse($result);
    }
}
