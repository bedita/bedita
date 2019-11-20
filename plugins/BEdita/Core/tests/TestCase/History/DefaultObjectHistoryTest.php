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

use BEdita\Core\History\DefaultObjectHistory;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\History\DefaultObjectHistory
 */
class DefaultObjectHistoryTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectHistory',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * Test `addEvent` method
     *
     * @covers ::addEvent()
     */
    public function testAddEvent()
    {
        $objHistory = new DefaultObjectHistory();
        $data = [
            'object_id' => 2,
            'user_id' => 1,
            'application_id' => 1,
            'user_action' => 'update',
            'changed' => [
                'title' => 'hello history'
            ],
        ];
        $objHistory->addEvent($data);

        $history = TableRegistry::get('ObjectHistory')->find()
            ->where(['object_id' => 2])
            ->order(['created' => 'DESC'])
            ->first()
            ->toArray();
        static::assertNotEmpty($history);
        $expected = ['id' => 3] + $data;
        static::assertNotEmpty($history['created']);
        unset($history['created']);
        $history['changed'] = (array)$history['changed'];
        static::assertEquals($expected, $history);
    }
}
