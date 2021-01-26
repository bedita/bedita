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
        Cache::drop('layered');
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
        $this->assertTrue($result);

        $result = Cache::read('secret', 'layered');
        $this->assertSame(42, $result);
    }

    /**
     * Test cache increment.
     *
     * @covers ::increment()
     */
    public function testIncrement()
    {
        $result = Cache::write('increment', 42, 'layered');
        $this->assertTrue($result);

        $result = Cache::increment('increment', 1, 'layered');
        $this->assertSame(43, $result);

        $result = Cache::read('increment', 'layered');
        $this->assertSame(43, $result);

        $result = Cache::increment('increment', 2, 'layered');
        $this->assertSame(45, $result);

        $result = Cache::read('increment', 'layered');
        $this->assertSame(45, $result);
    }

    /**
     * Test cache decrement.
     *
     * @covers ::decrement()
     */
    public function testDecrement()
    {
        $result = Cache::write('decrement', 42, 'layered');
        $this->assertTrue($result);

        $result = Cache::decrement('decrement', 1, 'layered');
        $this->assertSame(41, $result);

        $result = Cache::read('decrement', 'layered');
        $this->assertSame(41, $result);

        $result = Cache::decrement('decrement', 2, 'layered');
        $this->assertSame(39, $result);

        $result = Cache::read('decrement', 'layered');
        $this->assertSame(39, $result);
    }

    /**
     * Test cache delete.
     *
     * @covers ::delete()
     */
    public function testDelete()
    {
        $result = Cache::write('delete', 42, 'layered');
        $this->assertTrue($result);

        $result = Cache::delete('delete', 'layered');
        $this->assertTrue($result);

        $result = Cache::read('delete', 'layered');
        $this->assertFalse($result);
    }

    /**
     * Test cache clear.
     *
     * @covers ::clear()
     */
    public function testClear()
    {
        $result = Cache::write('clear', 42, 'layered');
        $this->assertTrue($result);

        $result = Cache::clear(false, 'layered');
        $this->assertTrue($result);

        $result = Cache::read('clear', 'layered');
        $this->assertFalse($result);
    }
}
