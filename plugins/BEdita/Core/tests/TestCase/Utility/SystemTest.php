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
namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\Database;
use BEdita\Core\Utility\System;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;

/**
 * \BEdita\Core\Utility\System Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\System
 */
class SystemTest extends TestCase
{

    /**
     * Test status method
     *
     * @return void
     */
    public function testStatus()
    {
        $expected = ['environment' => 'ok'];
        $debug = Configure::read('debug');

        Configure::write('debug', 0);
        $result = System::status();
        $this->assertEquals($expected, $result);

        Configure::write('debug', 1);
        $result = System::status();
        $this->assertEquals('ok', $result['environment']);
        $this->assertContains('BEdita/API', $result['plugins']);
        $this->assertContains('BEdita/Core', $result['plugins']);

        Configure::write('debug', $debug);
    }

    /**
     * Test status method failures
     *
     * @return void
     */
    public function testFail()
    {
        Configure::write('Requirements', [
            'phpMin' => '9.1',
            'extensions' => ['gustavo', 'supporto'],
            'writable' => [TMP . '____missing___']
        ]);

        $this->fakeDbSetup('__test-temp__');
        ConnectionManager::alias('__test-temp__', 'default');

        $result = System::status();

        ConnectionManager::alias('test', 'default');
        ConnectionManager::drop('__test-temp__');

        $this->assertNotEquals('ok', $result['environment']);
        $this->assertNotEmpty($result['errors']);
    }

    /**
     * Set up a fake database connection.
     *
     * @param string $configName Connection name.
     * @return void
     */
    protected function fakeDbSetup($configName)
    {
        $fake = [
            'className' => 'Cake\Database\Connection',
            'username' => '_______fake-tmp_______',
            'database' => '_______fake-tmp_______',
        ];
        $info = Database::basicInfo();
        $fake = array_merge($info, $fake);
        Configure::write('Datasources.' . $configName, $fake);
        ConnectionManager::setConfig($configName, $fake);
    }
}
