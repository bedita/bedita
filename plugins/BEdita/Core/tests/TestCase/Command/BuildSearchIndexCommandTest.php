<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Search\Adapter\SimpleAdapter;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Command\BuildSearchIndexCommand Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\BuildSearchIndexCommand
 */
class BuildSearchIndexCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
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
     * Test `buildOptionParser` method
     *
     * @return void
     * @covers ::buildOptionParser()
     */
    public function testBuildOptionParser(): void
    {
        $this->exec('build_search_index --help');
        $this->assertOutputContains('Command to reindex objects for search');
        $this->assertOutputContains('Reindex only objects from one or more specific types');
        $this->assertOutputContains('Reindex only one or more specific objects by ID');
        $this->assertOutputContains('Reindex only one or more specific objects by uname');
        $this->assertOutputContains('Reindex only using one or more specific adapters');
    }

    /**
     * Test `execute` method with no options
     *
     * @return void
     * @covers ::execute()
     * @covers ::objectsIterator()
     * @covers ::doIndexResource()
     */
    public function testExecute(): void
    {
        $adapter1 = new class extends SimpleAdapter
        {
            public $afterSaveCount = 0;
            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount++;
                }
            }
        };
        $adapter2 = new class extends SimpleAdapter
        {
            public $afterSaveCount = 0;
            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount--;
                }
            }
        };
        Configure::write('Search.adapters.default', [
            'className' => $adapter1,
        ]);
        Configure::write('Search.adapters.dummy', [
            'className' => $adapter2,
        ]);
        $this->exec('build_search_index');
        static::assertGreaterThan(0, $adapter1->afterSaveCount);
        static::assertLessThan(0, $adapter2->afterSaveCount);
        static::assertSame(0, $adapter1->afterSaveCount + $adapter2->afterSaveCount);
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `execute` method with exception
     *
     * @return void
     * @covers ::execute()
     * @covers ::objectsIterator()
     * @covers ::doIndexResource()
     */
    public function testExecuteException(): void
    {
        $adapter1 = new class extends SimpleAdapter
        {
            public function indexResource(EntityInterface $entity, string $operation): void
            {
                throw new \Exception('Test exception');
            }
        };
        Configure::write('Search.adapters.default', [
            'className' => $adapter1,
        ]);
        $this->exec('build_search_index');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    /**
     * Test `execute` method with --type option
     *
     * @return void
     * @covers ::execute()
     * @covers ::objectsIterator()
     * @covers ::doIndexResource()
     */
    public function testExecuteByTypes(): void
    {
        $adapter1 = new class extends SimpleAdapter
        {
            public $afterSaveCount = 0;
            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount++;
                }
            }
        };
        $adapter2 = new class extends SimpleAdapter
        {
            public $afterSaveCount = 0;
            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount--;
                }
            }
        };
        Configure::write('Search.adapters.default', [
            'className' => $adapter1,
        ]);
        Configure::write('Search.adapters.dummy', [
            'className' => $adapter2,
        ]);
        $this->exec('build_search_index --type documents,profiles');
        static::assertGreaterThan(0, $adapter1->afterSaveCount);
        static::assertLessThan(0, $adapter2->afterSaveCount);
        static::assertSame(0, $adapter1->afterSaveCount + $adapter2->afterSaveCount);
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `execute` method with --id option
     *
     * @return void
     * @covers ::execute()
     * @covers ::objectsIterator()
     * @covers ::doIndexResource()
     */
    public function testExecuteById(): void
    {
        $adapter1 = new class extends SimpleAdapter
        {
            public $afterSaveCount = 0;
            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount++;
                }
            }
        };
        $adapter2 = new class extends SimpleAdapter
        {
            public $afterSaveCount = 0;
            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount--;
                }
            }
        };
        Configure::write('Search.adapters.default', [
            'className' => $adapter1,
        ]);
        Configure::write('Search.adapters.dummy', [
            'className' => $adapter2,
        ]);
        $this->exec('build_search_index --id 2');
        static::assertSame(1, $adapter1->afterSaveCount);
        static::assertSame(-1, $adapter2->afterSaveCount);
        static::assertSame(0, $adapter1->afterSaveCount + $adapter2->afterSaveCount);
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `execute` method on wrong ID
     *
     * @return void
     * @covers ::execute()
     * @covers ::objectsIterator()
     * @covers ::doIndexResource()
     */
    public function testExecuteWrongId(): void
    {
        $this->exec('build_search_index --id abcdefghi');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    /**
     * Test `execute` method with --uname option
     *
     * @return void
     * @covers ::execute()
     * @covers ::objectsIterator()
     * @covers ::doIndexResource()
     */
    public function testExecuteByUname(): void
    {
        $adapter1 = new class extends SimpleAdapter
        {
            public $afterSaveCount = 0;
            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount++;
                }
            }
        };
        $adapter2 = new class extends SimpleAdapter
        {
            public $afterSaveCount = 0;
            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount--;
                }
            }
        };
        Configure::write('Search.adapters.default', [
            'className' => $adapter1,
        ]);
        Configure::write('Search.adapters.dummy', [
            'className' => $adapter2,
        ]);
        $this->exec('build_search_index --uname title-one');
        static::assertSame(1, $adapter1->afterSaveCount);
        static::assertSame(-1, $adapter2->afterSaveCount);
        static::assertSame(0, $adapter1->afterSaveCount + $adapter2->afterSaveCount);
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `execute` method on wrong uname
     *
     * @return void
     * @covers ::execute()
     * @covers ::objectsIterator()
     * @covers ::doIndexResource()
     */
    public function testExecuteWrongUname(): void
    {
        $this->exec('build_search_index --uname abcdefghi');
        $this->assertExitCode(Command::CODE_ERROR);
    }
}
