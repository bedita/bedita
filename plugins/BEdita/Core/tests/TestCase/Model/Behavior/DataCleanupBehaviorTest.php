<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Model\Behavior\DataCleanupBehavior;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\DataCleanupBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\DataCleanupBehavior
 */
class DataCleanupBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * Data provider for `testUnique` test case.
     *
     * @return array
     */
    public function cleanupProvider()
    {
        return [
            'status' => [
                [
                    'username' => 'lorem',
                    'password_hash' => 'ipsum',
                    'status' => null,
                ],
                [
                    'status' => 'draft'
                ]
            ],
            'status2' => [
                [
                    'username' => 'lorem',
                    'password_hash' => 'ipsum',
                    'status' => '',
                ],
                [
                    'status' => 'draft'
                ]
            ],
            'deleted' => [
                [
                    'username' => 'lorem',
                    'password_hash' => 'ipsum',
                    'deleted' => null,
                ],
                [
                    'deleted' => 0
                ]
            ],
        ];
    }

    /**
     * testDataCleanup method
     *
     * @return void
     *
     * @dataProvider cleanupProvider
     */
    public function testDataCleanup($inputData, $expected)
    {
        $this->Users = TableRegistry::get('Users');

        $user = $this->Users->newEntity($inputData);
        foreach ($expected as $k => $v) {
            $this->assertEquals($user[$k], $v);
        }
    }
}
