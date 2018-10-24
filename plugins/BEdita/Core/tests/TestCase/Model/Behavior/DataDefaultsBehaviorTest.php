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
 * {@see \BEdita\Core\Model\Behavior\DataDefaultsBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\DataDefaultsBehavior
 */
class DataDefaultsBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * Data provider for `testDataDefaults` test case.
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
                    'status' => 'draft'
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
                    'status' => 'on'
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
                    'deleted' => 0
                ],
                [],
            ],
        ];
    }

    /**
     * testDataDefaults method
     *
     * @param array $inputData Input data.
     * @param array $expected Expected result.
     * @param array $defaultValues Defaults values per type
     * @return void
     *
     * @dataProvider cleanupProvider
     * @covers ::beforeMarshal()
     */
    public function testDataDefaults(array $inputData, array $expected, array $defaultValues)
    {
        Configure::write('DefaultValues', $defaultValues);
        $Users = TableRegistry::get('Users');

        $user = $Users->newEntity($inputData);
        foreach ($expected as $k => $v) {
            $this->assertEquals($user[$k], $v);
        }
    }
}
