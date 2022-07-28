<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\App\Test\TestCase;

use BEdita\App\Application;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\App\Application} Test Case
 *
 * @coversDefaultClass \BEdita\App\Application
 */
class ApplicationTest extends TestCase
{
    /**
     * Test `bootstrap` method
     *
     * @return void
     * @covers ::bootstrap()
     */
    public function testBootstrap()
    {
        Configure::write('Plugins', []);
        $app = new Application(CONFIG);
        $app->bootstrap();
        static::assertTrue($app->getPlugins()->has('BEdita/Core'));
        static::assertTrue($app->getPlugins()->has('BEdita/API'));
        static::assertTrue($app->getPlugins()->has('Migrations'));
    }

    /**
     * Test `bootstrapCli` method
     *
     * @return void
     * @covers ::bootstrapCli()
     */
    public function testBootstrapCli()
    {
        $currDebug = Configure::read('debug');
        Configure::write('debug', true);
        $app = new Application(CONFIG);
        $app->bootstrap();
        static::assertTrue($app->getPlugins()->has('Cake/Repl'));
        static::assertTrue($app->getPlugins()->has('IdeHelper'));
        Configure::write('debug', $currDebug);
    }
}
