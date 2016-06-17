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

namespace BEdita\Core\Test\TestCase\Configure;

use BEdita\Core\Configure\Engine\DatabaseConfig;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Configure\Engine\DatabaseConfig} Test Case
 */
class ConfigureTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.config',
    ];

    /**
     * Test read method
     *
     * @return void
     */
    public function testRead()
    {
        Configure::config('database', new DatabaseConfig());
        Configure::load('group1', 'database', false);

        $this->assertFalse(Configure::read('Key1'));
        $this->assertEquals('some data', Configure::read('Key2.test1'));
        $this->assertEquals('other data', Configure::read('Key2.test2'));
    }

    /**
     * Data provider for `testDump` test case.
     *
     * @return array
     */
    public function configProvider()
    {
        return [
            'success' => [
                true,
                'somecontext',
                [
                    'Name3' => 'some value',
                    'Name4' => 'other data',
                ],
            ],
            'failure' => [
                false,
                'someother',
                [
                    'Name3' => '',
                    'Name.Four' => 'other data',
                ],
            ],
        ];
    }

    /**
     * Test dump method
     *
     * @param bool $expected Expected result.
     * @param string $context Config group context.
     * @param array $data Config data array.
     *
     * @return void
     *
     * @dataProvider configProvider
     */
    public function testDump($expected, $context, $data)
    {
        Configure::config('database', new DatabaseConfig());
        foreach ($data as $key => $value) {
            Configure::write($key, $value);
        }

        if (!$expected) {
            $this->setExpectedException('Exception');
        }

        $result = Configure::dump($context, 'database', array_keys($data));

        $this->assertEquals($expected, $result);

        Configure::load($context, 'database', false);
        foreach ($data as $key => $value) {
            $cfgVal = Configure::read($key);
            $this->assertEquals($value, $cfgVal);
        }
    }
}
