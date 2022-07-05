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

use Cake\Core\Configure;
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
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * Data provider for `testDataCleanup` test case.
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
                    'status' => 'draft',
                ],
                [],
            ],
            'status2' => [
                [
                    'username' => 'lorem',
                    'password_hash' => 'ipsum',
                    'status' => '',
                ],
                [
                    'status' => 'draft',
                ],
                [],
            ],
            'status from config' => [
                [
                    'username' => 'lorem',
                    'password_hash' => 'ipsum',
                    'status' => '',
                ],
                [
                    'status' => 'on',
                ],
                [
                    'users' => [
                        'status' => 'on',
                    ],
                ],
            ],
            'only in config' => [
                [
                    'username' => 'lorem',
                    'password_hash' => 'ipsum',
                ],
                [
                    'status' => 'on',
                ],
                [
                    'users' => [
                        'status' => 'on',
                    ],
                ],
            ],
            'deleted' => [
                [
                    'username' => 'lorem',
                    'password_hash' => 'ipsum',
                    'deleted' => null,
                ],
                [
                    'deleted' => 0,
                ],
                [],
            ],
            'not on existing objects' => [
                [
                    'id' => 999,
                ],
                [
                    'status' => null,
                ],
                [
                    'users' => [
                        'status' => 'on',
                    ],
                ],
            ],

        ];
    }

    /**
     * testDataCleanup method
     *
     * @param array $inputData Input data.
     * @param array $expected Expected result.
     * @param array $defaultValues Defaults values per type
     * @return void
     * @dataProvider cleanupProvider
     * @covers ::beforeMarshal()
     * @covers ::defaultFields()
     */
    public function testDataCleanup(array $inputData, array $expected, array $defaultValues)
    {
        Configure::write('DefaultValues', $defaultValues);
        $Users = TableRegistry::getTableLocator()->get('Users');

        $user = $Users->newEntity($inputData);
        foreach ($expected as $k => $v) {
            $this->assertEquals($user[$k], $v);
        }
    }

    /**
     * Data provider for `testStatusLevel` test case.
     *
     * @return array
     */
    public function statusLevelProvider(): array
    {
        return [
            'status' => [
                [
                    'username' => 'lorem',
                    'password_hash' => 'ipsum',
                    'status' => null,
                ],
                [
                    'status' => 'draft',
                ],
                'draft',
            ],
            'status2' => [
                [
                    'username' => 'lorem',
                    'password_hash' => 'ipsum',
                    'status' => '',
                ],
                [
                    'status' => 'on',
                ],
                'on',
            ],
            'status from config' => [
                [
                    'username' => 'lorem',
                    'password_hash' => 'ipsum',
                    'status' => '',
                ],
                [
                    'status' => 'draft',
                ],
            ],
        ];
    }

    /**
     * Test `Status.level` configurations
     *
     * @param array $inputData Input data.
     * @param array $expected Expected result.
     * @param string $level Status level.
     * @return void
     * @dataProvider statusLevelProvider
     * @covers ::defaultFields()
     */
    public function testStatusLevel(array $inputData, array $expected, string $level = ''): void
    {
        if (!empty($level)) {
            Configure::write('Status.level', $level);
        }
        $Users = TableRegistry::getTableLocator()->get('Users');

        $user = $Users->newEntity($inputData);
        foreach ($expected as $k => $v) {
            $this->assertEquals($user[$k], $v);
        }
    }
}
