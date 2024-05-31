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

namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\Resources;
use Cake\Http\Exception\BadRequestException;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Utility\ResourcesBase} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\ResourcesBase
 */
class ResourcesBaseTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
    ];

    /**
     * Test `getTable` method.
     *
     * @covers ::getTable()
     */
    public function testGetTable(): void
    {
        $result = Resources::create('object_types', [
            [
                'name' => 'cats',
                'singular' => 'cat',
            ],
        ]);

        static::assertNotEmpty($result);
        static::assertEquals(1, count($result));
    }

    /**
     * Test `getTable` method failure.
     *
     * @covers ::getTable()
     */
    public function testGetTableFail(): void
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Resource type not allowed "cats"');

        Resources::create('cats', []);
    }
}
