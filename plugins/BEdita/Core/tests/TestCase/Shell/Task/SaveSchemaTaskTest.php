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

namespace BEdita\Core\Test\TestCase\Shell\Task;

use BEdita\Core\TestSuite\ShellTestCase;
use Cake\Core\Plugin;

/**
 * @covers \BEdita\Core\Shell\Task\SaveSchemaTask
 */
class SaveSchemaTaskTest extends ShellTestCase
{

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        Plugin::load('Migrations');
    }

    /**
     * Test main execution.
     *
     * @return void
     */
    public function testMain()
    {
        $this->invoke(['db_admin', 'save_schema']);

        $this->assertOutputContains('This command is DEPRECATED!');
    }

    /**
     * Test controlled failure on missing "Migrations" plugin.
     *
     * @return void
     */
    public function testMissingMigrationsPlugin()
    {
        Plugin::unload('Migrations');

        $this->invoke(['db_admin', 'save_schema']);
        $this->assertErrorContains('Plugin "Migrations" must be loaded');

        $this->assertAborted();
    }
}
