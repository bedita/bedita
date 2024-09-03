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
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * \BEdita\Core\Shell\StreamsShell Test Case
 *
 * @property \BEdita\Core\Model\Table\StreamsTable $Streams
 * @coversDefaultClass \BEdita\Core\Shell\StreamsShell
 */
#[\AllowDynamicProperties]
class StreamsShellTest extends TestCase
{
    use ConsoleIntegrationTestTrait;
    use TestFilesystemTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->filesystemSetup(true, true);
        $this->Streams = TableRegistry::getTableLocator()->get('Streams');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->filesystemRestore();
        unset($this->Streams);
        parent::tearDown();
    }

    /**
     * Data provider for `testRemoveOrphans` test case.
     *
     * @return array
     */
    public function removeOrphansProvider()
    {
        return [
            'basic test' => [
                1,
                10,
            ],
        ];
    }

    /**
     * Test `refreshMetadata` method
     *
     * @return void
     * @covers ::refreshMetadata()
     * @covers ::updateStreamMetadata()
     * @covers ::streamsGenerator()
     */
    public function testRefreshMetadata(): void
    {
        // check width population if initial width is not available
        $this->Streams->updateAll(['width' => null], []);
        $this->exec('streams refreshMetadata');

        $results = $this->Streams->find('all')->all();
        $data = $results->toList();

        foreach ($data as $entry) {
            $entry['original_width'] = $entry['width'];
            if (preg_match('/image\//', $entry['mime_type']) && $entry['mime_type'] != 'image/svg+xml') {
                $this->assertNotNull($entry['width']);
            }
        }

        // check width population with force option
        $this->Streams->updateAll(['width' => 800], []);
        $this->exec('streams refreshMetadata --force');

        $results = $this->Streams->find('all')->all();
        $lastData = $results->toList();

        foreach ($lastData as $entry) {
            if (preg_match('/image\//', $entry['mime_type']) && $entry['mime_type'] != 'image/svg+xml') {
                $originalEntry = current(array_filter($data, function ($e) use ($entry) {
                    return $e['uuid'] === $entry['uuid'];
                }));

                $this->assertEquals($originalEntry['original_width'], $entry['width']);
            }
        }
    }

    /**
     * Test `removeOrphans` method
     *
     * @param int $expected Expected number of removed streams
     * @param int $days The days.
     * @return void
     * @dataProvider removeOrphansProvider()
     * @covers ::removeOrphans()
     */
    public function testRemoveOrphans($expected, $days)
    {
        $count = TableRegistry::getTableLocator()->get('Streams')->find()->count();
        $this->exec(sprintf('streams removeOrphans --days %d', $days));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertErrorEmpty();

        $count -= TableRegistry::getTableLocator()->get('Streams')->find()->count();
        static::assertEquals($expected, $count);
    }
}
