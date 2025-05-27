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

use BEdita\Core\Command\StreamsCommand;
use BEdita\Core\Model\Entity\Stream;
use BEdita\Core\Model\Table\StreamsTable;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Command\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Console\TestSuite\StubConsoleInput;
use Cake\Console\TestSuite\StubConsoleOutput;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Query\SelectQuery;
use Cake\TestSuite\TestCase;
use Generator;
use Throwable;

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
    protected array $fixtures = [
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
     * @covers ::execute()
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
            if (preg_match('/image\//', $entry['mime_type']) && $entry['mime_type'] != 'image/svg+xml') {
                $this->assertNotNull($entry['width']);
            }
        }
    }

    /**
     * Test `refreshMetadata` method with --force option
     *
     * @return void
     * @covers ::execute()
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
                    $stream->set('contents', $content, ['asOriginal' => true]);
                    $Streams->saveOrFail($stream);
                }
            } catch (Throwable $t) {
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
    public static function removeOrphansProvider(): array
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
     * @covers ::execute()
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

    /**
     * Test `streamsGenerator` method
     *
     * @return void
     * @covers ::streamsGenerator()
     */
    public function testStreamsGenerator(): void
    {
        $query = $this->fetchTable('Streams')->find()->where(['uuid' => '00000000-0000-0000-0000-000000000001']);
        $command = new class extends StreamsCommand {
            public function getStreams(SelectQuery $query, int $limit = 100): Generator
            {
                $this->table = $this->fetchTable('Streams');

                return $this->streamsGenerator($query, $limit);
            }
        };
        $actual = true;
        foreach ($command->getStreams($query) as $stream) {
            $actual = $stream;
        }
        static::assertTrue($actual);

        $expected = 'e5afe167-7341-458d-a1e6-042e8791b0fe';
        /** Cake\ORM\Query $query */
        $query = $this->fetchTable('Streams')->find()->where(['uuid' => $expected]);
        $actual = null;
        foreach ($command->getStreams($query) as $stream) {
            $actual = $stream->uuid;
        }
        static::assertSame($expected, $actual);
    }

    /**
     * Test `updateStreamMetadata` method on exception
     *
     * @return void
     * @covers ::updateStreamMetadata()
     */
    public function testUpdateStreamMetadataException(): void
    {
        $command = new class extends StreamsCommand {
            public function update(Stream $stream): bool
            {
                $this->io = new ConsoleIo(new StubConsoleOutput(), new StubConsoleOutput(), new StubConsoleInput([]));
                $this->table = new class extends StreamsTable {
                    public function saveOrFail(EntityInterface $entity, array $options = []): EntityInterface
                    {
                        throw new RecordNotFoundException();
                    }
                };

                return $this->updateStreamMetadata($stream);
            }
        };
        $stream = new Stream();
        $stream->contents = 'test';
        static::assertFalse($command->update($stream));
    }
}
