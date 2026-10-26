<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Database;

use BEdita\Core\Database\Connection;
use BEdita\Core\Test\Utility\SpyConnection;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Database\Connection} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Database\Connection
 */
class ConnectionTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Config',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Connection::setSticky(false);
        Connection::setReplica(false);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Connection::setSticky(false);
        Connection::setReplica(false);

        parent::tearDown();
    }

    /**
     * Create a connection to the test database, with a separate `read` driver if requested.
     *
     * @param array|null $read Read configuration, `null` for no `read` key.
     * @param class-string<\BEdita\Core\Database\Connection> $className Connection class.
     * @return \BEdita\Core\Database\Connection
     */
    protected function createConnection(?array $read = [], string $className = Connection::class): Connection
    {
        $config = ConnectionManager::get('test')->config();
        unset($config['read'], $config['write']);
        if ($read !== null) {
            $config['read'] = $read;
        }

        return new $className($config);
    }

    /**
     * Test that a single driver is used when neither `read` nor `write` are configured.
     *
     * @return void
     * @covers ::createDrivers()
     */
    public function testCreateDriversSingle(): void
    {
        $connection = $this->createConnection(null);

        static::assertSame($connection->getDriver(Connection::ROLE_READ), $connection->getDriver(Connection::ROLE_WRITE));
        static::assertSame(Connection::ROLE_WRITE, $connection->getDriver(Connection::ROLE_READ)->getRole());
        static::assertFalse(Connection::hasReplica());
    }

    /**
     * Test that `read` configuration is merged with the shared one.
     *
     * @return void
     * @covers ::createDrivers()
     */
    public function testCreateDriversMergeConfig(): void
    {
        $connection = $this->createConnection(['timezone' => 'Europe/Rome']);

        $read = $connection->getDriver(Connection::ROLE_READ);
        $write = $connection->getDriver(Connection::ROLE_WRITE);
        static::assertNotSame($read, $write);
        static::assertSame(Connection::ROLE_READ, $read->getRole());
        static::assertSame(Connection::ROLE_WRITE, $write->getRole());
        static::assertSame('Europe/Rome', $read->config()['timezone']);
        static::assertNotSame('Europe/Rome', $write->config()['timezone'] ?? null);
        static::assertSame($write->config()['database'], $read->config()['database']);
        static::assertArrayNotHasKey('read', $read->config());
        static::assertTrue(Connection::hasReplica());
    }

    /**
     * Test that `read` role goes to the primary when sticky or in a transaction.
     *
     * @return void
     * @covers ::getDriver()
     * @covers ::setSticky()
     * @covers ::isSticky()
     */
    public function testGetDriver(): void
    {
        $connection = $this->createConnection();
        $read = $connection->getDriver(Connection::ROLE_READ);
        $write = $connection->getDriver(Connection::ROLE_WRITE);
        static::assertNotSame($read, $write);

        $connection->transactional(function (Connection $connection) use ($write): void {
            static::assertSame($write, $connection->getDriver(Connection::ROLE_READ));
        });
        static::assertSame($read, $connection->getDriver(Connection::ROLE_READ));
        static::assertFalse(Connection::isSticky());

        Connection::setSticky(true);
        static::assertTrue(Connection::isSticky());
        static::assertSame($write, $connection->getDriver(Connection::ROLE_READ));
        static::assertSame($write, $connection->getDriver(Connection::ROLE_WRITE));
    }

    /**
     * Test that reads don't make the process sticky.
     *
     * @return void
     * @covers ::run()
     */
    public function testRunSelect(): void
    {
        $connection = $this->createConnection();
        $connection->selectQuery(['name'], 'config')->useReadRole()->execute()->closeCursor();

        static::assertFalse(Connection::isSticky());
    }

    /**
     * Data provider for `testRunWrite`.
     *
     * @return array
     */
    public function runWriteProvider(): array
    {
        return [
            'insert' => [
                fn (Connection $connection) => $connection->insertQuery('config', [
                    'name' => 'Sticky',
                    'context' => 'test',
                    'content' => 'yes',
                    'created' => '2026-01-01 00:00:00',
                    'modified' => '2026-01-01 00:00:00',
                ]),
            ],
            'update' => [
                fn (Connection $connection) => $connection->updateQuery('config', ['content' => 'yes'], ['name' => 'Sticky']),
            ],
            'delete' => [
                fn (Connection $connection) => $connection->deleteQuery('config', ['name' => 'Sticky']),
            ],
        ];
    }

    /**
     * Test that writes make the process sticky, also for other connections.
     *
     * @param callable $query Query factory.
     * @return void
     * @dataProvider runWriteProvider()
     * @covers ::run()
     */
    public function testRunWrite(callable $query): void
    {
        $connection = $this->createConnection();
        $other = $this->createConnection();
        $query($connection)->execute()->closeCursor();

        static::assertTrue(Connection::isSticky());
        static::assertSame($other->getDriver(Connection::ROLE_WRITE), $other->getDriver(Connection::ROLE_READ));
    }

    /**
     * Test that raw SQL makes the process sticky only when it's not a read.
     *
     * @return void
     * @covers ::prepare()
     */
    public function testPrepareRaw(): void
    {
        $connection = $this->createConnection();

        $connection->execute('SELECT 1')->closeCursor();
        static::assertFalse(Connection::isSticky());

        $connection->execute("UPDATE config SET content = 'yes' WHERE name = 'Sticky'")->closeCursor();
        static::assertTrue(Connection::isSticky());
    }

    /**
     * Data provider for `testIsRead`.
     *
     * @return array
     */
    public function isReadProvider(): array
    {
        return [
            'select' => ['SELECT 1', true],
            'lowercase' => ['select 1', true],
            'show' => ['SHOW TABLES', true],
            'describe' => ['DESCRIBE config', true],
            'desc' => ['DESC config', true],
            'pragma' => ['PRAGMA table_info(config)', true],
            'leading comments' => ["  -- comment\n# other\n/* block\n */ SELECT 1", true],
            'update' => ['UPDATE config SET content = 1', false],
            'insert' => ['INSERT INTO config (name) VALUES (1)', false],
            'cte' => ['WITH t AS (SELECT 1) SELECT * FROM t', false],
            'cte update' => ['WITH t AS (SELECT 1) UPDATE config SET content = 1', false],
            'call' => ['CALL procedure()', false],
            'create temporary' => ['CREATE TEMPORARY TABLE t (id INT)', false],
            'prefix of a keyword' => ['SELECTED', false],
            'commented select' => ['/* SELECT */ DELETE FROM config', false],
            'empty' => ['', false],
        ];
    }

    /**
     * Test raw SQL classification.
     *
     * @param string $sql Raw SQL.
     * @param bool $expected Expected result.
     * @return void
     * @dataProvider isReadProvider()
     * @covers ::isRead()
     */
    public function testIsRead(string $sql, bool $expected): void
    {
        $method = new \ReflectionMethod(Connection::class, 'isRead');
        $method->setAccessible(true);

        static::assertSame($expected, $method->invoke($this->createConnection(), $sql));
    }

    /**
     * Test that an entity saved inside a transaction keeps reads on the primary after commit.
     *
     * @return void
     * @covers ::run()
     * @covers ::getDriver()
     */
    public function testWriteInTransaction(): void
    {
        $connection = $this->createConnection();
        $table = new Table(['table' => 'config', 'connection' => $connection]);

        $connection->transactional(function () use ($table): void {
            $entity = $table->newEntity([
                'name' => 'Sticky',
                'context' => 'test',
                'content' => 'yes',
                'created' => '2026-01-01 00:00:00',
                'modified' => '2026-01-01 00:00:00',
            ]);
            $table->saveOrFail($entity);
        });

        static::assertFalse($connection->inTransaction());
        static::assertTrue(Connection::isSticky());
        $query = $table->find()->useReadRole()->where(['name' => 'Sticky']);
        static::assertSame($connection->getDriver(Connection::ROLE_WRITE), $connection->getDriver($query->getConnectionRole()));
        static::assertSame(1, $query->count());
    }

    /**
     * Data provider for `testWithPrimaryFallback`.
     *
     * @return array
     */
    public function withPrimaryFallbackProvider(): array
    {
        return [
            'hit' => [
                ['found'],
                null,
                1,
                false,
            ],
            'record not found' => [
                [new RecordNotFoundException(), 'found'],
                null,
                2,
                true,
            ],
            'http not found' => [
                [new NotFoundException(), 'found'],
                null,
                2,
                true,
            ],
            'miss by result' => [
                [null, 'found'],
                fn ($result): bool => $result === null,
                2,
                true,
            ],
            'hit by result' => [
                ['found'],
                fn ($result): bool => $result === null,
                1,
                false,
            ],
        ];
    }

    /**
     * Test lookup with fallback on primary.
     *
     * @param array $results Results (or exceptions) of the lookup, in order.
     * @param callable|null $isMiss Check on the result.
     * @param int $expectedCalls Expected number of lookups.
     * @param bool $expectedSticky Expected sticky flag after the lookup.
     * @return void
     * @dataProvider withPrimaryFallbackProvider()
     * @covers ::withPrimaryFallback()
     */
    public function testWithPrimaryFallback(array $results, ?callable $isMiss, int $expectedCalls, bool $expectedSticky): void
    {
        Connection::setReplica(true);
        $calls = 0;
        $stickyOnCall = [];
        $callback = function () use (&$calls, &$stickyOnCall, $results) {
            $stickyOnCall[] = Connection::isSticky();
            $result = $results[$calls++];
            if ($result instanceof \Exception) {
                throw $result;
            }

            return $result;
        };

        $actual = Connection::withPrimaryFallback($callback, $isMiss);

        static::assertSame('found', $actual);
        static::assertSame($expectedCalls, $calls);
        static::assertSame($expectedSticky, Connection::isSticky());
        static::assertFalse($stickyOnCall[0]);
        if ($expectedCalls === 2) {
            static::assertTrue($stickyOnCall[1]);
        }
    }

    /**
     * Test that a record missing also on the primary is still not found.
     *
     * @return void
     * @covers ::withPrimaryFallback()
     */
    public function testWithPrimaryFallbackMissing(): void
    {
        Connection::setReplica(true);
        $calls = 0;

        try {
            Connection::withPrimaryFallback(function () use (&$calls): void {
                $calls++;

                throw new RecordNotFoundException();
            });
            static::fail('Expected exception not thrown');
        } catch (RecordNotFoundException) {
            static::assertSame(2, $calls);
        }
    }

    /**
     * Test that no fallback happens when already sticky.
     *
     * @return void
     * @covers ::withPrimaryFallback()
     */
    public function testWithPrimaryFallbackSticky(): void
    {
        Connection::setSticky(true);
        $calls = 0;

        $actual = Connection::withPrimaryFallback(
            function () use (&$calls) {
                $calls++;

                return null;
            },
            fn ($result): bool => $result === null,
        );

        static::assertNull($actual);
        static::assertSame(1, $calls);
    }

    /**
     * Test that no fallback happens without a replica.
     *
     * @return void
     * @covers ::withPrimaryFallback()
     */
    public function testWithPrimaryFallbackNoReplica(): void
    {
        $calls = 0;

        $actual = Connection::withPrimaryFallback(
            function () use (&$calls) {
                $calls++;

                return null;
            },
            fn ($result): bool => $result === null,
        );
        static::assertNull($actual);
        static::assertSame(1, $calls);

        try {
            Connection::withPrimaryFallback(function () use (&$calls): void {
                $calls++;

                throw new RecordNotFoundException();
            });
            static::fail('Expected exception not thrown');
        } catch (RecordNotFoundException) {
            static::assertSame(2, $calls);
        }
        static::assertFalse(Connection::isSticky());
    }

    /**
     * Data provider for `testRunContain`.
     *
     * @return array
     */
    public function runContainProvider(): array
    {
        return [
            'read' => [Connection::ROLE_READ],
            'write' => [Connection::ROLE_WRITE],
        ];
    }

    /**
     * Test that separately loaded containments, nested ones included, use the role of the parent query.
     *
     * @param string $role Role of the parent query.
     * @return void
     * @dataProvider runContainProvider()
     * @covers ::run()
     * @covers ::propagateReadRole()
     */
    public function testRunContain(string $role): void
    {
        /** @var \BEdita\Core\Test\Utility\SpyConnection $connection */
        $connection = $this->createConnection([], SpyConnection::class);
        $applications = new Table(['table' => 'applications', 'alias' => 'Applications', 'connection' => $connection]);
        $config = new Table(['table' => 'config', 'alias' => 'Config', 'connection' => $connection]);
        $owners = new Table(['table' => 'applications', 'alias' => 'Owners', 'connection' => $connection]);
        $applications->hasMany('Config', ['targetTable' => $config, 'foreignKey' => 'application_id']);
        $config->hasMany('Owners', ['targetTable' => $owners, 'foreignKey' => 'id', 'bindingKey' => 'application_id']);

        $result = $applications->find()
            ->where(['Applications.id' => 1])
            ->contain('Config', fn (Query $query): Query => $query->where(['Config.name' => 'appVal']))
            ->contain('Config.Owners')
            ->setConnectionRole($role)
            ->firstOrFail();

        static::assertSame(['appVal'], array_column($result->get('config'), 'name'));
        static::assertSame([1], array_column($result->get('config')[0]->get('owners'), 'id'));
        static::assertSame(['Applications', 'Config', 'Owners'], array_column($connection->executed, 'table'));
        static::assertSame([$role, $role, $role], array_column($connection->executed, 'actual'));
    }

    /**
     * Test that the count of a complex query, run as a subquery, uses the role of the counted query.
     *
     * @param string $role Role of the counted query.
     * @return void
     * @dataProvider runContainProvider()
     * @covers ::run()
     * @covers ::selectsFromReadQueries()
     */
    public function testRunComplexCount(string $role): void
    {
        /** @var \BEdita\Core\Test\Utility\SpyConnection $connection */
        $connection = $this->createConnection([], SpyConnection::class);
        $applications = new Table(['table' => 'applications', 'alias' => 'Applications', 'connection' => $connection]);

        $count = $applications->find()
            ->distinct()
            ->setConnectionRole($role)
            ->count();

        static::assertSame(2, $count);
        static::assertSame(
            [['type' => 'select', 'table' => null, 'requested' => $role, 'actual' => $role]],
            $connection->executed
        );
    }
}
