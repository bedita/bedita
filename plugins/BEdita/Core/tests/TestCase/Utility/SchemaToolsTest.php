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

use BEdita\Core\Utility\SchemaTools;
use Cake\TestSuite\TestCase;

/**
 * \BEdita\Core\Utility\SchemaTools Test Case
 *
 * @covers \BEdita\Core\Utility\SchemaTools
 */
class SchemaToolsTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
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
        $actual = SchemaTools::getPrimaryFields($schema, ['count' => 1]);
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
        $actual = SchemaTools::getUniqueFields($schema, ['count' => 1]);
        $expected = ['api_key', 'name'];
        sort($actual);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getNullableFields` method
     *
     * @return void
     * @covers ::getNullableFields()
     */
    public function testGetNullableFields(): void
    {
        $table = $this->fetchTable('Users');
        $schema = $table->getSchema();
        $actual = SchemaTools::getNullableFields($schema);
        $expected = ['password_hash', 'last_login', 'last_login_err', 'verified', 'password_modified', 'user_preferences'];
        static::assertEquals($expected, $actual);
    }
}
