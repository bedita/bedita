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

use BEdita\Core\Command\ProjectModelCommand;
use BEdita\Core\Test\TestCase\Utility\ProjectModelTest;
use Cake\Core\App;
use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\ProjectModelCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\ProjectModelCommand
 */
class ProjectModelCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     *
     * @covers ::buildOptionParser()
     */
    public function testBuildOptionParser()
    {
        $this->exec('project_model --help');
        $this->assertOutputContains('Path of JSON file containing project model to apply');
        $this->assertOutputContains('Plugin to use for loading default');
    }

    /**
     * Test execute method
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::modelFilePath()
     */
    public function testExecute(): void
    {
        $model = ProjectModelTest::PROJECT_MODEL;
        $path = TMP . '__test.json';
        file_put_contents($path, json_encode($model));
        $this->exec('project_model --file ' . $path);
        unlink($path);
        $this->assertOutputContains('Project model in sync, exiting.');
        $this->assertExitSuccess();
    }

    /**
     * Test file load failure
     *
     * @return void
     *
     * @covers ::modelFilePath()
     * @covers ::execute()
     */
    public function testFileFail(): void
    {
        $this->exec('project_model --file project.json');
        $this->assertErrorContains('File not found project.json');
        $this->assertExitError();
    }

    /**
     * Test default file failure
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::modelFilePath()
     */
    public function testDefaultFileFail(): void
    {
        $this->exec('project_model');
        $this->assertErrorContains('File not found ' . CONFIG . ProjectModelCommand::PROJECT_MODEL_FILE);
        $this->assertExitError();
    }

    /**
     * Test default file failure
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::modelFilePath()
     */
    public function testPluginFailure2(): void
    {
        $this->exec('project_model -p Test');
        $expected = current(App::path('Plugin')) . 'Test' . DS . 'config' . DS . ProjectModelCommand::PROJECT_MODEL_FILE;
        $this->assertErrorContains('File not found ' . $expected);
        $this->assertExitError();
    }

    /**
     * Test default file failure
     *
     * @return void
     *
     * @covers ::execute()
     */
    public function testContentFailure(): void
    {
        $path = TMP . '__test.json';
        file_put_contents($path, '');
        $this->exec('project_model --file ' . $path);
        unlink($path);
        $this->assertErrorContains('Bad file content in ' . $path);
        $this->assertExitError();
    }

    /**
     * Test remove from model
     *
     * @return void
     *
     * @covers ::execute()
     */
    public function testRemove(): void
    {
        $model = ProjectModelTest::PROJECT_MODEL;
        unset($model['property_types'][0]);
        $path = TMP . '__test.json';
        file_put_contents($path, json_encode($model));
        $this->exec('project_model --file ' . $path);
        unlink($path);
        $this->assertErrorContains('Items to remove');
        $this->assertExitSuccess();
    }

    /**
     * Test update model items
     *
     * @return void
     *
     * @covers ::execute()
     */
    public function testUpdate(): void
    {
        $model = ProjectModelTest::PROJECT_MODEL;
        $model['relations'][0] = [
            'name' => 'test',
            'inverse_name' => 'inverse_test',
            'right_object_types' => ['documents', 'profiles'],
            'left_object_types' => ['events'],
        ];
        $path = TMP . '__test.json';
        file_put_contents($path, json_encode($model));
        $this->exec('project_model --cache-clear --file ' . $path);
        unlink($path);
        $this->assertOutputContains('Project model updated');
        $this->assertOutputContains('Cache cleared');
        $this->assertExitSuccess();
    }
}
