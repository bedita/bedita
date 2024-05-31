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
namespace BEdita\Core\Test\TestCase\Model\Behavior;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\ResourceNameBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\ResourceNameBehavior
 */
class ResourceNameBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.EndpointPermissions',
    ];

    /**
     * Data provider for `testGetId()`
     *
     * @return array
     */
    public function getIdProvider()
    {
        return [
            'id' => [
                1,
                1,
            ],
            'idString' => [
                1,
                '1',
            ],
            'uname' => [
                1,
                'first role',
            ],
            'notFound' => [
                new RecordNotFoundException('Record not found in table "roles"'),
                'this-name-doesnt-exist',
            ],
            'null' => [
                new \InvalidArgumentException('Expression `Roles.name` is missing operator (IS, IS NOT) with `null` value.'),
                null,
            ],
            'emptyString' => [
                new RecordNotFoundException('Record not found in table "roles"'),
                '',
            ],
        ];
    }

    /**
     * Test `getId()`
     *
     * @param mixed $expected The expected result.
     * @param int|string $name The unique resource identifier.
     * @return void
     * @dataProvider getIdProvider
     * @covers ::getId()
     */
    public function testGetId($expected, $name)
    {
        $Roles = $this->fetchTable('Roles');
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $id = $Roles->getId($name);
        static::assertEquals($expected, $id);
    }
}
