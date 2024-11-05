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

use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\StreamsCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\StreamsCommand
 */
class StreamsCommandTest extends TestCase
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
    protected function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
        $this->filesystemSetup(true, true);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->filesystemRestore();
        parent::tearDown();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @covers ::buildOptionParser()
     */
    public function testBuildOptionParser(): void
    {
        $this->exec('streams --help');
        $this->assertOutputContains('cake streams [--days 1] [--force] [-h] [-q] [-v] <removeOrphans|refreshMetadata>');
        $this->assertOutputContains('Days to consider for stream research for orphans');
        $this->assertOutputContains('Force refreshing all streams');
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
        /** \BEdita\Core\Model\Table\StreamsTable $Streams */
        $Streams = $this->fetchTable('Streams');

        // check width population if initial width is not available
        $Streams->updateAll(['width' => null], []);
        $this->exec('streams refreshMetadata');

        $results = $Streams->find('all')->all();
        $data = $results->toList();
        foreach ($data as $entry) {
            $entry['original_width'] = $entry['width'];
            if (preg_match('/image\//', $entry['mime_type']) && $entry['mime_type'] != 'image/svg+xml') {
                $this->assertNotNull($entry['width']);
            }
        }
    }

    /**
     * Test `refreshMetadata` method with --force option
     *
     * @return void
     * @covers ::refreshMetadata()
     * @covers ::updateStreamMetadata()
     * @covers ::streamsGenerator()
     */
    public function testRefreshMetadataForce(): void
    {
        /** \BEdita\Core\Model\Table\StreamsTable $Streams */
        $Streams = $this->fetchTable('Streams');

        // refresh all metadata
        foreach ($Streams->find('all')->all() as $stream) {
            try {
                $content = $stream->contents;
                if ($content !== null) {
                    $stream->contents = $content;
                    $Streams->saveOrFail($stream);
                }
            } catch (\Throwable $t) {
            }
        }

        $results = $Streams->find('all')->all();
        $data = $results->toList();

        // check width population with force option
        $Streams->updateAll(['width' => 800], []);
        $this->exec('streams refreshMetadata --force');

        $results = $Streams->find('all')->all();
        $lastData = $results->toList();

        foreach ($lastData as $entry) {
            if (preg_match('/image\//', $entry['mime_type']) && $entry['mime_type'] != 'image/svg+xml') {
                $originalEntry = current(array_filter($data, function ($e) use ($entry) {
                    return $e['uuid'] === $entry['uuid'];
                }));

                $this->assertEquals($originalEntry['width'], $entry['width']);
            }
        }
    }

    /**
     * Data provider for `testRemoveOrphans` test case.
     *
     * @return array
     */
    public function removeOrphansProvider(): array
    {
        return [
            'basic test' => [
                1,
                10,
            ],
        ];
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
    public function testRemoveOrphans(int $expected, int $days): void
    {
        /** \BEdita\Core\Model\Table\StreamsTable $Streams */
        $Streams = $this->fetchTable('Streams');
        $count = $Streams->find()->count();
        $this->exec(sprintf('streams removeOrphans --days %d', $days));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertErrorEmpty();

        $count -= $Streams->find()->count();
        static::assertEquals($expected, $count);
    }
}
