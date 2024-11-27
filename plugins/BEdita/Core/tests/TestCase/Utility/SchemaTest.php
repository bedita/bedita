<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\Database;
use BEdita\Core\Utility\Schema;
use Cake\TestSuite\TestCase;

/**
 * \BEdita\Core\Utility\Schema Test Case
 *
 * @covers \BEdita\Core\Utility\Schema
 */
class SchemaTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.Applications',
    ];

    /**
     * Test `getPrimaryFields` method
     *
     * @return void
     * @covers ::getPrimaryFields()
     */
    public function testGetPrimaryFields(): void
    {
        $table = $this->fetchTable('Users');
        $schema = $table->getSchema();
        $actual = Schema::getPrimaryFields($schema);
        $expected = ['id'];
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getUniqueFields` method
     *
     * @return void
     * @covers ::getUniqueFields()
     */
    public function testGetUniqueFields(): void
    {
        $table = $this->fetchTable('Applications');
        $schema = $table->getSchema();
        $actual = Schema::getUniqueFields($schema);
        $expected = ['api_key', 'name'];
        $info = Database::basicInfo();
        if ($info['vendor'] === 'sqlite') {
            $expected = [];
        }
        static::assertEquals($expected, $actual);
    }
}
