<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase;

use BEdita\Core\Plugin;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * \BEdita\Core\Plugin Test Case
 *
 * @coversDefaultClass \BEdita\Core\Plugin
 */
class PluginTest extends TestCase
{

    /**
     * Test load from config method
     *
     * @return void
     */
    public function testLoadConfig()
    {
        $debug = Configure::read('debug');
        $pluginsConfig = [
            'DebugKit' => ['debugOnly' => true],
            'Migrations' => ['debugOnly' => false],
        ];
        Plugin::unload('DebugKit');
        Plugin::unload('Migrations');
        Configure::write('debug', 1);
        Configure::write('Plugins', $pluginsConfig);
        Plugin::loadFromConfig();
        $this->assertTrue(Plugin::loaded('DebugKit'));
        $this->assertTrue(Plugin::loaded('Migrations'));

        Plugin::unload('DebugKit');
        Plugin::unload('Migrations');
        Configure::write('debug', 0);
        Plugin::loadFromConfig();
        $this->assertFalse(Plugin::loaded('DebugKit'));
        $this->assertTrue(Plugin::loaded('Migrations'));

        Configure::write('debug', $debug);
    }
}
