<?php
declare(strict_types=1);

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
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\Command\ProjectModelCommand} Test Case
 */
#[CoversClass(ProjectModelCommand::class)]
class ProjectModelCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
    ];

    /**
     * Test buildOptionParser method
     *
     * @return void
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
     */
    public function testPluginFailure2(): void
    {
        $this->exec('project_model -p Test');
        $expected = current(Configure::read('App.paths.plugins')) . 'Test' . DS . 'config' . DS . ProjectModelCommand::PROJECT_MODEL_FILE;
        $this->assertErrorContains('File not found ' . $expected);
        $this->assertExitError();
    }

    /**
     * Test default file failure
     *
     * @return void
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
     */
    public function testRemove(): void
    {
        $model = ProjectModelTest::PROJECT_MODEL;
        unset($model['property_types'][0]);
        $path = TMP . '__test.json';
        file_put_contents($path, json_encode($model));
        $this->exec('project_model --file ' . $path . ' --delete', ['y']);
        unlink($path);
        $this->assertErrorContains('Items to remove');
        $this->assertExitSuccess();
    }

    /**
     * Test update model items
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $model = ProjectModelTest::PROJECT_MODEL;
        $model['relations'][1] = [
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

    /**
     * Test prepareContent method
     *
     * @return void
     */
    public function testPrepareContent(): void
    {
        $cmd = new class () extends ProjectModelCommand
        {
            public function prepareContent(array $data): array
            {
                return parent::prepareContent($data);
            }
        };
        $data = [
            [
                'name' => 'test',
                'content' => ['sample' => 'something'],
            ],
        ];
        $actual = $cmd->prepareContent($data);
        $expected = [
            [
                'name' => 'test',
                'content' => '{"sample":"something"}',
            ],
        ];
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test modelFileFromFolder method
     *
     * @return void
     */
    public function testModelFileFromFolder(): void
    {
        // CONFIG . DS . 'project-model' not exists
        $folder = CONFIG . DS . 'project-model';
        if (is_dir($folder)) {
            rmdir($folder);
        }
        $cmd = new class () extends ProjectModelCommand
        {
            public function modelFileFromFolder(): ?string
            {
                return parent::modelFileFromFolder();
            }
        };
        $this->assertNull($cmd->modelFileFromFolder());

        // CONFIG . DS . 'project-model' exists but empty
        mkdir($folder, 0777, true);
        $this->assertNull($cmd->modelFileFromFolder());

        // CONFIG . DS . 'project-model' exists and contains files
        $file1 = $folder . DS . 'test1.json';
        $file2 = $folder . DS . 'test2.json';
        file_put_contents($file1, json_encode(['test1' => 'value1']));
        file_put_contents($file2, json_encode(['test2' => 'value2']));
        $expected = TMP . DS . 'project-model.json';
        $result = $cmd->modelFileFromFolder();
        $this->assertFileExists($expected);
        $this->assertJsonStringEqualsJsonFile($expected, json_encode([
            'test1' => ['test1' => 'value1'],
            'test2' => ['test2' => 'value2'],
        ]));
        unlink($file1);
        unlink($file2);
        rmdir($folder);
        $this->assertEquals($expected, $result);
    }
}
