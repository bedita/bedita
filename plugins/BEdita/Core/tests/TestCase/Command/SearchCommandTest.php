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
 * BEdita\Core\Command\SearchCommand Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\SearchCommand
 */
class SearchCommandTest extends TestCase
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
        $this->exec('search --help');
        $this->assertOutputContains('Interface to handle search indexes and data');
        $this->assertOutputContains('Dry run, do not perform any operation');
        $this->assertOutputContains('Index a single object');
        $this->assertOutputContains('Reindex all or multiple objects in the system');
    }

    /**
     * Test `execute` method with no options
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteNoOptions(): void
    {
        $this->exec('search');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    /**
     * Test `reindex` method
     *
     * @return void
     * @covers ::reindex()
     * @covers ::doMultiIndex()
     * @covers ::doIndexResource()
     */
    public function testReindex(): void
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
        $this->exec('search --reindex');
        static::assertGreaterThan(0, $adapter1->afterSaveCount);
        static::assertLessThan(0, $adapter2->afterSaveCount);
        static::assertSame(0, $adapter1->afterSaveCount + $adapter2->afterSaveCount);
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `reindex` method
     *
     * @return void
     * @covers ::reindex()
     * @covers ::doMultiIndex()
     * @covers ::doIndexResource()
     */
    public function testReindexByTypes(): void
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
        $this->exec('search --reindex documents,profiles');
        static::assertGreaterThan(0, $adapter1->afterSaveCount);
        static::assertLessThan(0, $adapter2->afterSaveCount);
        static::assertSame(0, $adapter1->afterSaveCount + $adapter2->afterSaveCount);
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `reindex` method with exception
     *
     * @return void
     * @covers ::reindex()
     * @covers ::doMultiIndex()
     * @covers ::doIndexResource()
     */
    public function testReindexException(): void
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
        $this->exec('search --reindex');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    /**
     * Test `index` method
     *
     * @return void
     * @covers ::index()
     * @covers ::doSingleIndex()
     * @covers ::doIndexResource()
     */
    public function testIndex(): void
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
        $this->exec('search --index 2');
        static::assertSame(1, $adapter1->afterSaveCount);
        static::assertSame(-1, $adapter2->afterSaveCount);
        static::assertSame(0, $adapter1->afterSaveCount + $adapter2->afterSaveCount);
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test `index` method on missing ID
     *
     * @return void
     * @covers ::index()
     * @covers ::doSingleIndex()
     * @covers ::doIndexResource()
     */
    public function testIndexMissingId(): void
    {
        $this->exec('search --index');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    /**
     * Test `index` method on wrong ID
     *
     * @return void
     * @covers ::index()
     * @covers ::doSingleIndex()
     * @covers ::doIndexResource()
     */
    public function testIndexWrongId(): void
    {
        $this->exec('search --index abcdefghi');
        $this->assertExitCode(Command::CODE_ERROR);
    }
}
