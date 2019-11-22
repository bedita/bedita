<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\History;

use BEdita\Core\History\HistoryTableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\History\HistoryTableRegistry
 */
class HistoryTableRegistryTest extends TestCase
{
    /**
     * Test `get` method
     *
     * @covers ::get()
     */
    public function testGet()
    {
        $history = HistoryTableRegistry::get('History');
        static::assertNotEmpty($history);
    }

    /**
     * Test `get` method failure
     *
     * @covers ::get()
     */
    public function testGetFailure()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('History table must implement "history" and "activity" finders');
        HistoryTableRegistry::get('NotFound');
    }
}
