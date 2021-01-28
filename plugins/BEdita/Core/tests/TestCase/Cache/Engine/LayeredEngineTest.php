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
        'persistent' => ['className' => 'Array'],
    ];

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        Cache::enable();
        Cache::clearAll();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        // remove registered cache engines (Array is the internal memory engine of Layered)
        foreach (Cache::configured() as $name) {
            if (strpos($name, 'test-layered') !== 0 && $name !== 'Array') {
                continue;
            }

            Cache::drop($name);
        }

        foreach (Cache::getRegistry()->loaded() as $name) {
            if (strpos($name, 'test-layered') !== 0 && $name !== 'Array') {
                continue;
            }

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
        Cache::setConfig('test-layered', $this->defaultConfig);
        Cache::clear(false, 'test-layered');

        $instance = Cache::getRegistry()->get('test-layered');
        static::assertAttributeInstanceOf(ArrayEngine::class, 'memory', $instance);
        static::assertAttributeInstanceOf(ArrayEngine::class, 'persistent', $instance);
    }

    /**
     * Test using an alias for persistent cache.
     *
     * @covers ::getEngineInstance()
     */
    public function testPersistentAlias()
    {
        Cache::setConfig('test-layered-persistent-alias', ['className' => 'Array']);
        Cache::setConfig('test-layered-alias', array_merge(
            $this->defaultConfig,
            ['persistent' => 'test-layered-persistent-alias']
        ));

        $result = Cache::write('secret', 42, 'test-layered-persistent-alias');
        static::assertTrue($result);

        $result = Cache::read('secret', 'test-layered-alias');
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
        Cache::setConfig('test-layered-bad-persistent', array_merge(
            $this->defaultConfig,
            ['persistent' => 1]
        ));
        Cache::clear(false, 'test-layered-bad-persistent');
    }

    /**
     * Test using an nonexistent alias for persistent cache.
     *
     * @covers ::getEngineInstance()
     */
    public function testPersistentMissingAlias()
    {
        static::expectException(Exception::class);
        static::expectExceptionMessage("Cache engine alias 'test-layered-persistent-missing' is not defined");
        Cache::setConfig('test-layered-missing-alias', array_merge(
            $this->defaultConfig,
            ['persistent' => 'test-layered-persistent-missing']
        ));
        Cache::clear(false, 'test-layered-missing-alias');
    }

    /**
     * Test using the engine as persistent engine of itself.
     *
     * @covers ::getEngineInstance()
     */
    public function testPersistentRecursive()
    {
        static::expectException(Exception::class);
        Cache::setConfig('test-layered-recursive', array_merge(
            $this->defaultConfig,
            ['persistent' => 'test-layered-recursive']
        ));
        Cache::clear(false, 'test-layered-recursive');
    }

    /**
     * Test using an alias to wrong object type as persistent engine.
     *
     * @covers ::getEngineInstance()
     */
    public function testPersistentWrongObject()
    {
        static::expectException(Exception::class);
        static::expectExceptionMessage("Cache engine alias 'test-layered-persistent-wrong' is not an implementation of CacheEngine");
        Cache::getRegistry()->set('test-layered-persistent-wrong', new \stdClass());
        Cache::setConfig('test-layered-wrong-object', array_merge(
            $this->defaultConfig,
            ['persistent' => 'test-layered-persistent-wrong']
        ));
        Cache::clear(false, 'test-layered-wrong-object');
    }

    /**
     * Test cache write and read.
     *
     * @covers ::write()
     * @covers ::read()
     */
    public function testWriteAndRead()
    {
        Cache::setConfig('test-layered', $this->defaultConfig);

        $result = Cache::write('secret', 42, 'test-layered');
        static::assertTrue($result);

        $result = Cache::read('secret', 'test-layered');
        static::assertSame(42, $result);
    }

    /**
     * Test cache read, with miss in memory engine.
     *
     * @covers ::read()
     */
    public function testMemoryCacheMiss()
    {
        Cache::setConfig('test-layered-persistent-miss', ['className' => 'Array']);
        Cache::setConfig('test-layered-miss', array_merge(
            $this->defaultConfig,
            ['persistent' => 'test-layered-persistent-miss']
        ));

        $result = Cache::write('secret', 42, 'test-layered-persistent-miss');
        static::assertTrue($result);

        $result = Cache::read('secret', 'test-layered-miss');
        static::assertSame(42, $result);
    }

    /**
     * Test cache increment.
     *
     * @covers ::increment()
     */
    public function testIncrement()
    {
        Cache::setConfig('test-layered', $this->defaultConfig);

        $result = Cache::write('increment', 42, 'test-layered');
        static::assertTrue($result);

        $result = Cache::increment('increment', 1, 'test-layered');
        static::assertSame(43, $result);

        $result = Cache::read('increment', 'test-layered');
        static::assertSame(43, $result);

        $result = Cache::increment('increment', 2, 'test-layered');
        static::assertSame(45, $result);

        $result = Cache::read('increment', 'test-layered');
        static::assertSame(45, $result);
    }

    /**
     * Test cache decrement.
     *
     * @covers ::decrement()
     */
    public function testDecrement()
    {
        Cache::setConfig('test-layered', $this->defaultConfig);

        $result = Cache::write('decrement', 42, 'test-layered');
        static::assertTrue($result);

        $result = Cache::decrement('decrement', 1, 'test-layered');
        static::assertSame(41, $result);

        $result = Cache::read('decrement', 'test-layered');
        static::assertSame(41, $result);

        $result = Cache::decrement('decrement', 2, 'test-layered');
        static::assertSame(39, $result);

        $result = Cache::read('decrement', 'test-layered');
        static::assertSame(39, $result);
    }

    /**
     * Test cache delete.
     *
     * @covers ::delete()
     */
    public function testDelete()
    {
        Cache::setConfig('test-layered', $this->defaultConfig);

        $result = Cache::write('delete', 42, 'test-layered');
        static::assertTrue($result);

        $result = Cache::delete('delete', 'test-layered');
        static::assertTrue($result);

        $result = Cache::read('delete', 'test-layered');
        static::assertFalse($result);
    }

    /**
     * Test cache clear.
     *
     * @covers ::clear()
     */
    public function testClear()
    {
        Cache::setConfig('test-layered', $this->defaultConfig);

        $result = Cache::write('clear', 42, 'test-layered');
        static::assertTrue($result);

        $result = Cache::clear(false, 'test-layered');
        static::assertTrue($result);

        $result = Cache::read('clear', 'test-layered');
        static::assertFalse($result);
    }
}
