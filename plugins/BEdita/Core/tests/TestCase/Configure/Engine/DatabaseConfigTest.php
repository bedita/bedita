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

namespace BEdita\Core\Test\TestCase\Configure\Engine;

use BEdita\Core\Configure\Engine\DatabaseConfig;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Configure\Engine\DatabaseConfig} Test Case
 */
class DatabaseConfigTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Configure\Engine\DatabaseConfig
     */
    public $DatabaseConfig;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.config',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->DatabaseConfig = new DatabaseConfig();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DatabaseConfig);
        parent::tearDown();
    }

    /**
     * Test read method
     *
     * @return void
     */
    public function testRead()
    {
        $configData = $this->DatabaseConfig->read();
        $this->assertEquals(true, $configData['Name2']);
        $this->assertEquals(14, $configData['IntVal']);

        $configData = $this->DatabaseConfig->read('group1');
        $expected = [
                'test1' => 'some data',
                'test2' => 'other data',
        ];
        $this->assertEquals($expected, $configData['Key2']);
        $this->assertArrayNotHasKey('IntVal', $configData);
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
        if (!$expected) {
            $this->expectException('Exception'); // TODO: be more specific! Assertions on exceptions should be strict.
        }
        $check = $this->DatabaseConfig->dump($context, $data);
        $this->assertEquals($expected, $check);

        $configData = $this->DatabaseConfig->read($context);
        foreach ($data as $key => $value) {
            $this->assertArrayHasKey($key, $configData);
            $this->assertEquals($value, $configData[$key]);
        }
    }
}
