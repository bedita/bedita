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
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Configure\Engine\DatabaseConfig} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Configure\Engine\DatabaseConfig
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
     * @covers ::read()
     * @covers ::valueFromString()
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

        foreach (['lowercaseGroup', 'uppercaseGroup'] as $context) {
            $configData = $this->DatabaseConfig->read($context);
            $this->assertTrue($configData[$context . '.trueVal']);
            $this->assertFalse($configData[$context . '.falseVal']);
            $this->assertNull($configData[$context . '.nullVal']);
        }
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
                    'nullConf' => null,
                    'trueConf' => true,
                    'falseConf' => false
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
            'avoidReservedWords' => [
                [
                    'Name5' => 'just another name',
                ],
                'appcontext',
                [
                    'Name5' => 'just another name',
                    'Datasources' => 'You cannot touch me!',
                    'Cache' => 'Me too :('
                ]
            ]
        ];
    }

    /**
     * Test dump method
     *
     * @param bool|array $expected Expected result.
     * @param string $context Config group context.
     * @param array $data Config data array.
     *
     * @return void
     *
     * @dataProvider configProvider
     * @covers ::dump()
     * @covers ::valueToString()
     */
    public function testDump($expected, $context, $data)
    {
        if (!$expected) {
            $this->expectException('Cake\Database\Exception');
        }
        $check = $this->DatabaseConfig->dump($context, $data);
        $this->assertEquals((bool)$expected, $check);

        $configData = $this->DatabaseConfig->read($context);

        $expectedData = !is_array($expected) ? $data : $expected;
        foreach ($expectedData as $key => $value) {
            $this->assertArrayHasKey($key, $configData);
            $this->assertEquals($value, $configData[$key]);
        }
    }

    /**
     * Test read method using Configure class
     *
     * @return void
     *
     * @coversNothing
     */
    public function testReadByConfigure()
    {
        Configure::config('test-database', $this->DatabaseConfig);
        Configure::load('group1', 'test-database');

        $this->assertTrue(Configure::read('Name2'));
        $this->assertEquals('some data', Configure::read('Key2.test1'));
        $this->assertEquals('other data', Configure::read('Key2.test2'));
    }

    /**
     * Test dump method using Configure class
     *
     * @param bool|array $expected Expected result.
     * @param string $context Config group context.
     * @param array $data Config data array.
     *
     * @return void
     *
     * @dataProvider configProvider
     * @coversNothing
     */
    public function testDumpByConfigureClass($expected, $context, $data)
    {
        Configure::config('test-database', $this->DatabaseConfig);
        foreach ($data as $key => $value) {
            Configure::write($key, $value);
        }

        if (!$expected) {
            $this->expectException('Cake\Database\Exception');
        }

        $result = Configure::dump($context, 'test-database', array_keys($data));

        $this->assertEquals((bool)$expected, $result);

        Configure::load($context, 'test-database', false);
        $expectedData = !is_array($expected) ? $data : $expected;
        foreach ($data as $key => $value) {
            $cfgVal = Configure::read($key);
            $this->assertEquals($value, $cfgVal);
        }
    }
}
