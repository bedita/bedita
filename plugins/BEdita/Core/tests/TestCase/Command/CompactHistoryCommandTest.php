<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Model\Entity\History;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\CompactHistoryCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\CompactHistoryCommand
 */
class CompactHistoryCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Applications',
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
        'plugin.BEdita/Core.History',
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
    public function testBuildOptionParser(): void
    {
        $this->exec('compact_history --help');
        $this->assertOutputContains('Min ID to check');
        $this->assertOutputContains('Max ID to check');
        $this->assertOutputContains('dry run mode');
    }

    /**
     * Test execute method, min and max not set
     *
     * @return void
     * @covers ::execute()
     * @covers ::initialize()
     * @covers ::compactHistory()
     * @covers ::objectsGenerator()
     * @covers ::processHistory()
     * @covers ::compare()
     */
    public function testExecuteMinMaxDryrun(): void
    {
        $q = $this->fetchTable('Objects')->find();
        $max = $q->select(['max_id' => $q->func()->max('id')])
            ->first()
            ->get('max_id');
        $this->exec('compact_history --dryrun 1');
        $this->assertExitSuccess();
        $this->assertOutputContains('Dry run mode: yes');
        $this->assertOutputContains('Min ID: 1');
        $this->assertOutputContains('Max ID: ' . $max);
    }

    /**
     * Test execute method with ID 1, no duplicates
     *
     * @return void
     * @covers ::execute()
     * @covers ::initialize()
     * @covers ::compactHistory()
     * @covers ::objectsGenerator()
     * @covers ::processHistory()
     * @covers ::compare()
     */
    public function testNoDuplicates(): void
    {
        $this->exec('compact_history --from 1 --to 1 --verbose');
        $this->assertExitSuccess();
        $this->assertOutputContains('Dry run mode: no');
        $this->assertOutputContains('Min ID: 1 - Max ID: 1');
        $this->assertOutputContains('No duplicates found');
    }

    /**
     * Test execute on missing Ids
     *
     * @return void
     * @covers ::execute()
     * @covers ::initialize()
     * @covers ::compactHistory()
     * @covers ::objectsGenerator()
     */
    public function testExecuteMissingIds(): void
    {
        $this->exec('compact_history --from 1234567 --to 1234568 --verbose');
        $this->assertExitSuccess();
        $this->assertOutputContains('Dry run mode: no');
        $this->assertOutputContains('Min ID: 1234567');
        $this->assertOutputContains('Max ID: 1234568');
        $this->assertOutputContains('ID 1234567 not found. Skip');
        $this->assertOutputContains('ID 1234568 not found. Skip');
    }

    /**
     * Test execute method with dryrun mode
     *
     * @return void
     * @covers ::execute()
     * @covers ::initialize()
     * @covers ::compactHistory()
     * @covers ::objectsGenerator()
     * @covers ::processHistory()
     * @covers ::compare()
     */
    public function testExecuteDryrun(): void
    {
        // insert duplicated history records
        $table = $this->fetchTable('History');
        $countBefore = $table->find()->where(['resource_id' => 1])->count();
        $table->save(new History([
            'resource_id' => 1,
            'application_id' => 1,
            'event' => 'create',
            'data' => json_encode(['foo' => 'bar']),
        ]));
        $table->save(new History([
            'resource_id' => 1,
            'application_id' => 1,
            'event' => 'create',
            'data' => json_encode(['foo2' => 'bar2']),
        ]));
        $table->save(new History([
            'resource_id' => 1,
            'application_id' => 1,
            'event' => 'create',
            'data' => json_encode(['foo' => 'bar']),
        ]));
        $this->exec('compact_history --from 1 --to 1 --dryrun 1 --verbose');
        $this->assertExitSuccess();
        $this->assertOutputContains('Dry run mode: yes');
        $this->assertOutputContains('Min ID: 1 - Max ID: 1');
        $this->assertOutputContains('Dry run mode: do not delete duplicated history records');
        $countActual = $table->find()->where(['resource_id' => 1])->count();
        static::assertEquals($countBefore + 3, $countActual);
    }

    /**
     * Test execute method
     *
     * @return void
     * @covers ::execute()
     * @covers ::initialize()
     * @covers ::compactHistory()
     * @covers ::objectsGenerator()
     * @covers ::processHistory()
     * @covers ::compare()
     */
    public function testExecute(): void
    {
        // insert duplicated history records
        $table = $this->fetchTable('History');
        $countBefore = $table->find()->where(['resource_id' => 1])->count();
        $table->save(new History([
            'resource_id' => 1,
            'application_id' => 1,
            'event' => 'create',
            'data' => json_encode(['foo' => 'bar']),
        ]));
        $table->save(new History([
            'resource_id' => 1,
            'application_id' => 1,
            'event' => 'create',
            'data' => json_encode(['foo2' => 'bar2']),
        ]));
        $table->save(new History([
            'resource_id' => 1,
            'application_id' => 1,
            'event' => 'create',
            'data' => json_encode(['foo2' => 'bar2']),
        ]));
        $table->save(new History([
            'resource_id' => 1,
            'application_id' => 1,
            'event' => 'create',
            'data' => json_encode(['foo' => 'bar']),
        ]));
        $table->save(new History([
            'resource_id' => 1,
            'application_id' => 1,
            'event' => 'create',
            'data' => json_encode(['foo2' => 'bar2']),
        ]));
        $table->save(new History([
            'resource_id' => 1,
            'application_id' => 1,
            'event' => 'create',
            'data' => json_encode(['foo' => 'bar']),
        ]));
        $this->exec('compact_history --from 1 --to 1');
        $this->assertExitSuccess();
        $this->assertOutputContains('Dry run mode: no');
        $this->assertOutputContains('Min ID: 1 - Max ID: 1');
        $this->assertOutputContains('Processed 1, removed duplicates for 1 object(s)');
        $countActual = $table->find()->where(['resource_id' => 1])->count();
        static::assertEquals($countBefore + 1, $countActual);
    }
}
