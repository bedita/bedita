<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Filesystem\Adapter\LocalAdapter;
use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\CustomPropsCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\CustomPropsCommand
 */
class CustomPropsCommandTest extends TestCase
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
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @covers ::buildOptionParser()
     */
    public function testBuildOptionParser()
    {
        $this->exec('custom_props --help');
        $this->assertOutputContains('Object ID to check');
        $this->assertOutputContains('Object type name to check');
    }

    /**
     * Test `execute` method
     *
     * @return void
     * @covers ::execute()
     * @covers ::customPropsByType()
     * @covers ::objectsGenerator()
     */
    public function testExecute(): void
    {
        FilesystemRegistry::drop('default');
        FilesystemRegistry::setConfig('default', [
            'className' => LocalAdapter::class,
        ]);
        $this->exec('custom_props');
        FilesystemRegistry::dropAll();
        $this->assertOutputContains('Updated 2 users without errors');
        $this->assertExitSuccess();
    }

    /**
     * Test `execute` with `id` and `type` option
     *
     * @return void
     * @covers ::execute()
     * @covers ::customPropsByType()
     */
    public function testOptionsExecute(): void
    {
        $this->exec('custom_props --type users --id 5');
        $this->assertOutputContains('Updated 1 users without errors');
        $this->assertExitSuccess();
    }

    /**
     * Test `execute` method
     *
     * @return void
     * @covers ::execute()
     * @covers ::customPropsByType()
     * @covers ::objectsGenerator()
     */
    public function testFail(): void
    {
        $table = TableRegistry::getTableLocator()->get('Documents');
        $table->removeBehavior('CustomProperties');
        $document = $table->get(2);
        $document->set('custom_props', ['another_title' => true]);
        $table->saveOrFail($document);
        $table->addBehavior('BEdita/Core.CustomProperties');

        $this->exec('custom_props --type documents --id 2');
        $this->assertErrorContains('errors updating documents');
        $this->assertExitError();
    }
}
