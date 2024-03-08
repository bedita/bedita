<?php

declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Channelweb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\ThumbsCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\ThumbsCommand
 */
class ThumbsCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
        Configure::write('Thumbnails.allowAny', true);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('Thumbnails.allowAny');
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @covers ::buildOptionParser()
     */
    public function testBuildOptionParser()
    {
        $this->exec('thumbs --help');
        $this->assertOutputContains('Image id');
    }

    /**
     * Test `execute` method
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecute(): void
    {
        $this->exec('thumbs');
        $this->assertOutputContains('Update image thumbs');
        $this->assertOutputContains('Thumbs updated');
        $this->assertExitSuccess();
    }

    /**
     * Test `execute` method with `--id` option
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteId(): void
    {
        $this->exec('thumbs --id 123');
        $this->assertOutputContains('Update image thumbs');
        $this->assertOutputContains('Thumbs updated');
        $this->assertOutputContains('Updated 0 images');
        $this->assertExitSuccess();
    }
}
