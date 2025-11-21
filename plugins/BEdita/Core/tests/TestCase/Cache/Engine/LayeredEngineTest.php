<?php
declare(strict_types=1);

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

use BEdita\Core\Cache\Engine\LayeredEngine;
use Cake\Cache\Cache;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

/**
 * {@see \BEdita\Core\Cache\Engine\LayeredEngine} Test Case
 */
#[CoversClass(LayeredEngine::class)]
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
    protected function setUp(): void
    {
        parent::setUp();
        Cache::enable();
        Cache::clearAll();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
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
     * Covers persistent engine config with prefix.
     *
     * @return void
     */
    public function testInit(): void
    {
        Cache::setConfig('test-layered', $this->defaultConfig);
        Cache::clear('test-layered');

        $instance = Cache::getRegistry()->get('test-layered');
        static::assertNotNull($instance);
        static::assertEquals($this->defaultConfig['prefix'], $instance->getConfig('prefix'));
        static::assertEquals($this->defaultConfig['persistent'], $instance->getConfig('persistent'));
    }

    /**
     * Test using an alias for persistent cache.
     *
     * @return void
     */
    public function testPersistentAlias(): void
    {
        Cache::setConfig('test-layered-persistent-alias', ['className' => 'Array']);
        Cache::setConfig('test-layered-alias', array_merge(
            $this->defaultConfig,
            ['persistent' => 'test-layered-persistent-alias'],
        ));

        $result = Cache::write('secret', 42, 'test-layered-persistent-alias');
        static::assertTrue($result);

        $result = Cache::read('secret', 'test-layered-alias');
        static::assertSame(42, $result);
    }

    /**
     * Test using a bad persistent config.
     *
     * @return void
     */
    public function testPersistentBadConfig(): void
    {
        static::expectException(TypeError::class);
        Cache::setConfig('test-layered-bad-persistent', array_merge(
            $this->defaultConfig,
            ['persistent' => 1],
        ));
        Cache::clear('test-layered-bad-persistent');
    }

    /**
     * Test using an nonexistent alias for persistent cache.
     *
     * @return void
     */
    public function testPersistentMissingAlias(): void
    {
        static::expectException(Exception::class);
        static::expectExceptionMessage("Cache engine alias 'test-layered-persistent-missing' is not defined");
        Cache::setConfig('test-layered-missing-alias', array_merge(
            $this->defaultConfig,
            ['persistent' => 'test-layered-persistent-missing'],
        ));
        Cache::clear('test-layered-missing-alias');
    }

    /**
     * Test using the engine as persistent engine of itself.
     *
     * @return void
     */
    public function testPersistentRecursive(): void
    {
        static::expectException(Exception::class);
        Cache::setConfig('test-layered-recursive', array_merge(
            $this->defaultConfig,
            ['persistent' => 'test-layered-recursive'],
        ));
        Cache::clear('test-layered-recursive');
    }

    /**
     * Test using an alias to wrong object type as persistent engine.
     *
     * @return void
     */
    public function testPersistentWrongObject(): void
    {
        static::expectException(Exception::class);
        static::expectExceptionMessage("Cache engine alias 'test-layered-persistent-wrong' is not an implementation of CacheEngine");
        Cache::getRegistry()->set('test-layered-persistent-wrong', new stdClass());
        Cache::setConfig('test-layered-wrong-object', array_merge(
            $this->defaultConfig,
            ['persistent' => 'test-layered-persistent-wrong'],
        ));
        Cache::clear('test-layered-wrong-object');
    }

    /**
     * Test cache write and read.
     *
     * @return void
     */
    public function testWriteAndRead(): void
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
     * @return void
     */
    public function testMemoryCacheMiss(): void
    {
        Cache::setConfig('test-layered-persistent-miss', ['className' => 'Array']);
        Cache::setConfig('test-layered-miss', array_merge(
            $this->defaultConfig,
            ['persistent' => 'test-layered-persistent-miss'],
        ));

        $result = Cache::write('secret', 42, 'test-layered-persistent-miss');
        static::assertTrue($result);

        $result = Cache::read('secret', 'test-layered-miss');
        static::assertSame(42, $result);
    }

    /**
     * Test cache increment.
     *
     * @return void
     */
    public function testIncrement(): void
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
     * @return void
     */
    public function testDecrement(): void
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
     * @return void
     */
    public function testDelete(): void
    {
        Cache::setConfig('test-layered', $this->defaultConfig);

        $result = Cache::write('delete', 42, 'test-layered');
        static::assertTrue($result);

        $result = Cache::delete('delete', 'test-layered');
        static::assertTrue($result);

        $result = Cache::read('delete', 'test-layered');
        static::assertNull($result);
    }

    /**
     * Test cache clear.
     *
     * @return void
     */
    public function testClear(): void
    {
        Cache::setConfig('test-layered', $this->defaultConfig);

        $result = Cache::write('clear', 42, 'test-layered');
        static::assertTrue($result);

        $result = Cache::clear('test-layered');
        static::assertTrue($result);

        $result = Cache::read('clear', 'test-layered');
        static::assertNull($result);
    }
}
