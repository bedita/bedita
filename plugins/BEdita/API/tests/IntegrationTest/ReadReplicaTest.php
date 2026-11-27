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
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Database\Connection;
use BEdita\Core\Test\Utility\SpyConnection;
use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Cache\Cache;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test which role queries run on, with a `read` driver configured.
 */
#[CoversNothing]
class ReadReplicaTest extends IntegrationTestCase
{
    use TestFilesystemTrait;

    /**
     * @inheritDoc
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.DateRanges',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * Original test connection.
     *
     * @var \Cake\Datasource\ConnectionInterface
     */
    protected ConnectionInterface $original;

    /**
     * Original test connection configuration.
     *
     * @var array
     */
    protected array $originalConfig;

    /**
     * Original `_bedita_core_` cache configuration.
     *
     * @var array
     */
    protected array $coreCacheConfig;

    /**
     * Test connection with a separate `read` driver on the same database.
     *
     * @var \BEdita\Core\Test\Utility\SpyConnection
     */
    protected SpyConnection $spy;

    /**
     * Replica flag before the test.
     *
     * @var bool
     */
    protected bool $replica;

    /**
     * SQLite file the `read` driver uses after `freezeReplica()`.
     *
     * @var string|null
     */
    protected ?string $snapshot = null;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->replica = Connection::hasReplica();
        $this->original = ConnectionManager::get('test');
        $this->originalConfig = (array)ConnectionManager::getConfig('test');
        $this->spy = new SpyConnection(['read' => []] + $this->original->config());
        ConnectionManager::drop('test');
        ConnectionManager::setConfig('test', $this->spy);
        $this->getTableLocator()->clear();

        // Use a cache for object types, otherwise all queries go to the primary because of the `StaticProperties` temp table.
        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'Array']);

        // Reset the core cache between test runs to avoid it expiring partway through.
        $this->coreCacheConfig = (array)Cache::getConfig('_bedita_core_');
        Cache::drop('_bedita_core_');
        Cache::setConfig('_bedita_core_', ['className' => 'Array']);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Connection::setSticky(false);
        Connection::setReplica($this->replica);
        $this->spy->getReadDriver()->disconnect();
        $this->spy->getWriteDriver()->disconnect();
        $this->original->getDriver()->disconnect();
        if ($this->snapshot !== null) {
            unlink($this->snapshot);
        }
        ConnectionManager::drop('test');
        // Restore the configuration, not the instance: the migrations plugin builds its settings from it.
        ConnectionManager::setConfig('test', $this->originalConfig);
        $this->getTableLocator()->clear();
        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'Null']);
        Cache::drop('_bedita_core_');
        Cache::setConfig('_bedita_core_', $this->coreCacheConfig);

        parent::tearDown();
    }

    /**
     * Start recording queries of the next request, as a new process would.
     *
     * @return void
     */
    protected function resetProcess(): void
    {
        Connection::setSticky(false);
        $this->spy->executed = [];
        $this->getTableLocator()->clear();
    }

    /**
     * Queries that didn't run on the given driver.
     *
     * @param string $role Expected driver role.
     * @param array|null $executed Queries to check, all executed queries by default.
     * @return array
     */
    protected function notOn(string $role, ?array $executed = null): array
    {
        return array_values(array_filter(
            $executed ?? $this->spy->executed,
            fn(array $query): bool => $query['actual'] !== $role,
        ));
    }

    /**
     * Test that a created object is read back from the primary.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $authHeader = $this->getUserAuthHeader();
        $this->resetProcess();

        $this->configRequestHeaders('POST', $authHeader);
        $data = [
            'type' => 'documents',
            'attributes' => [
                'title' => 'Read replica',
            ],
        ];
        $this->post('/documents', json_encode(compact('data')));

        $this->assertResponseCode(201);
        static::assertContains(
            ['type' => 'select', 'table' => 'Documents', 'requested' => 'read', 'actual' => 'write'],
            $this->spy->executed,
        );
        static::assertSame([], $this->notOn('write'));
    }

    /**
     * Test that reading an object uses the replica.
     *
     * @return void
     */
    public function testView(): void
    {
        // Warm up the caches.
        $this->configRequestHeaders();
        $this->get('/documents/2');
        $this->resetProcess();

        $this->configRequestHeaders();
        $this->get('/documents/2');

        $this->assertResponseCode(200);
        static::assertContains(
            ['type' => 'select', 'table' => 'Documents', 'requested' => 'read', 'actual' => 'read'],
            $this->spy->executed,
        );
        static::assertFalse(Connection::isSticky());
    }

    /**
     * Data provider for `testReplicaOnly`.
     *
     * @return array
     */
    public static function replicaOnlyProvider(): array
    {
        return [
            'list' => ['/documents?count=test&include=test', 'Documents'],
            'association filter' => ['/documents?filter[test]=4', 'Documents'],
            'categories filter' => ['/documents?filter[categories]=first-cat', 'Documents'],
            'tags filter' => ['/profiles?filter[tags]=first-tag', 'Profiles'],
            'date ranges filter' => ['/events?filter[date_ranges][start_date][gt]=2017-01-01', 'Events'],
            'view' => ['/documents/2?count=test&include=test', 'Documents'],
            'related' => ['/documents/2/test?count=test', 'Test'],
            'relationships' => ['/documents/2/relationships/test', 'Test'],
            'folder' => ['/folders/12', 'Trees'],
            'folders' => ['/folders', 'Trees'],
            'tree' => ['/trees/root-folder/sub-folder', 'Trees'],
            'uname' => ['/documents/title-one', 'Documents'],
            'abstract type' => ['/objects?filter[type]=media', 'Objects'],
            'ancestor' => ['/objects?filter[ancestor]=root-folder', 'TreeNodes'],
            'children' => ['/folders/11/children', 'Folders'],
            'users' => ['/users', 'ExternalAuth', true],
            'whoami' => ['/auth/user', 'ExternalAuth', true],
            'tree path' => ['/tree_paths/root-folder-11/sub-folder-12', 'Trees'],
            'file' => ['/files/14', 'Streams'],
            'files' => ['/files', 'Streams'],
        ];
    }

    /**
     * Test that every query of a read flow runs on the replica.
     *
     * @param string $url Request URL.
     * @param string $table Alias of the table that must be queried.
     * @param bool $auth Whether the request is authenticated.
     * @return void
     */
    #[DataProvider('replicaOnlyProvider')]
    public function testReplicaOnly(string $url, string $table, bool $auth = false): void
    {
        $headers = $auth ? $this->getUserAuthHeader() : [];

        // Warm up the caches.
        $this->configRequestHeaders('GET', $headers);
        $this->get($url);
        $this->resetProcess();

        $this->configRequestHeaders('GET', $headers);
        $this->get($url);

        $this->assertResponseCode(200);
        static::assertContains($table, array_column($this->spy->executed, 'table'));
        static::assertSame([], $this->notOn('read'));
        static::assertFalse(Connection::isSticky());
    }

    /**
     * Assert that the static properties temporary table was used, and no query ran on the read driver after it.
     *
     * @return void
     */
    protected function assertNoReadAfterTemporaryTable(): void
    {
        $first = array_search('StaticProperties', array_column($this->spy->executed, 'table'), true);
        static::assertNotFalse($first);
        static::assertSame([], $this->notOn('write', array_slice($this->spy->executed, $first)));
    }

    /**
     * Data provider for `testStaticProperties`.
     *
     * @return array
     */
    public static function staticPropertiesProvider(): array
    {
        return [
            'property type' => ['/model/property_types/1/properties'],
            'properties' => ['/model/properties'],
        ];
    }

    /**
     * Test that queries on static properties run where the temporary table exists.
     *
     * @param string $url Request URL.
     * @return void
     */
    #[DataProvider('staticPropertiesProvider')]
    public function testStaticProperties(string $url): void
    {
        $authHeader = $this->getUserAuthHeader();
        $this->resetProcess();

        $this->configRequestHeaders('GET', $authHeader);
        $this->get($url);

        $this->assertResponseCode(200);
        static::assertContains(
            ['type' => 'select', 'table' => 'Properties', 'requested' => 'read', 'actual' => 'write'],
            $this->spy->executed,
        );
        $this->assertNoReadAfterTemporaryTable();
        static::assertTrue(Connection::isSticky());
    }

    /**
     * Test that the JSON schema, rebuilt on a cache miss, is read where the temporary table exists.
     *
     * @return void
     */
    public function testSchemaCacheMiss(): void
    {
        $this->configRequestHeaders();
        $this->get('/documents/2');
        $this->resetProcess();
        Cache::clear('_bedita_object_types_');

        $this->configRequestHeaders();
        $this->get('/documents/2');

        $this->assertResponseCode(200);
        $this->assertNoReadAfterTemporaryTable();
        static::assertTrue(Connection::isSticky());
    }

    /**
     * Test that thumbnail generation leaves the rest of the request on the primary.
     *
     * @return void
     */
    public function testThumbsGeneration(): void
    {
        $this->configRequestHeaders();
        $this->get('/media/thumbs/14');
        $this->resetProcess();

        $this->configRequestHeaders();
        $this->get('/media/thumbs/14');

        $this->assertResponseCode(200);
        $insert = array_search('insert', array_column($this->spy->executed, 'type'), true);
        static::assertNotFalse($insert);
        static::assertSame([], $this->notOn('read', array_slice($this->spy->executed, 0, $insert)));
        static::assertSame([], $this->notOn('write', array_slice($this->spy->executed, $insert)));
        static::assertTrue(Connection::isSticky());
    }

    /**
     * Point the `read` driver at a copy of the database as it is now, so that later writes exist only on the primary.
     *
     * @return void
     */
    protected function freezeReplica(): void
    {
        $config = $this->original->config();
        $this->skipIf(
            !is_a($config['driver'], Sqlite::class, true) || $config['database'] === ':memory:',
            'A frozen replica needs an SQLite database file',
        );

        // `VACUUM INTO` requires a missing or empty file.
        $this->snapshot = (string)tempnam(sys_get_temp_dir(), 'replica');
        $this->spy->getWriteDriver()->execute('VACUUM INTO ?', [$this->snapshot]);

        $this->spy->getReadDriver()->disconnect();
        $this->spy->getWriteDriver()->disconnect();
        $this->spy = new SpyConnection(['read' => ['database' => $this->snapshot]] + $config);
        ConnectionManager::drop('test');
        ConnectionManager::setConfig('test', $this->spy);
        $this->getTableLocator()->clear();
    }

    /**
     * Assert that a lookup on the given table missed on the replica and ran again on the primary.
     *
     * @param string $table Alias of the table of the lookup.
     * @return void
     */
    protected function assertFallback(string $table): void
    {
        // The temporary table also sends reads to the primary: only queries before it prove a fallback.
        $executed = $this->spy->executed;
        $temporary = array_search('StaticProperties', array_column($executed, 'table'), true);
        if ($temporary !== false) {
            $executed = array_slice($executed, 0, $temporary);
        }
        $miss = array_search(
            ['type' => 'select', 'table' => $table, 'requested' => 'read', 'actual' => 'read'],
            $executed,
            true,
        );
        static::assertNotFalse($miss, sprintf('No read on the replica for "%s"', $table));
        static::assertContains(
            ['type' => 'select', 'table' => $table, 'requested' => 'read', 'actual' => 'write'],
            array_slice($executed, $miss + 1),
        );
        static::assertTrue(Connection::isSticky());
    }

    /**
     * Create an object and return its id.
     *
     * @param string $type Object type.
     * @param array $attributes Object attributes.
     * @param array $authHeader Authorization header.
     * @return string
     */
    protected function createObject(string $type, array $attributes, array $authHeader): string
    {
        $this->configRequestHeaders('POST', $authHeader);
        $this->post(sprintf('/%s', $type), json_encode(['data' => compact('type', 'attributes')]));
        $this->assertResponseCode(201);

        return json_decode((string)$this->_response->getBody(), true)['data']['id'];
    }

    /**
     * Data provider for `testFallback`.
     *
     * @return array
     */
    public static function fallbackProvider(): array
    {
        return [
            'view' => ['documents', '/documents/{id}', 'Documents'],
            'uname' => ['documents', '/documents/lag-test', 'Objects'],
            'related' => ['documents', '/documents/{id}/test', 'Documents'],
            'ancestor' => ['folders', '/objects?filter[ancestor]={id}', 'TreeNodes'],
            'thumbs' => ['images', '/media/thumbs/{id}', 'Media', ['media_property' => false]],
        ];
    }

    /**
     * Test that a lookup of an object created after the replica stopped falls back to the primary.
     *
     * @param string $type Type of the created object.
     * @param string $url Request URL, `{id}` is replaced with the new object id.
     * @param string $table Alias of the table of the lookup that misses.
     * @param array $attributes Attributes of the created object.
     * @return void
     */
    #[DataProvider('fallbackProvider')]
    public function testFallback(string $type, string $url, string $table, array $attributes = []): void
    {
        $this->freezeReplica();
        $authHeader = $this->getUserAuthHeader();
        $id = $this->createObject($type, ['uname' => 'lag-test'] + $attributes, $authHeader);
        $this->resetProcess();

        $this->configRequestHeaders('GET', $authHeader);
        $this->get(str_replace('{id}', $id, $url));

        $this->assertResponseCode(200);
        $this->assertFallback($table);
    }

    /**
     * Test that a trashed object missing on the replica is read from the primary.
     *
     * @return void
     */
    public function testFallbackTrash(): void
    {
        $this->freezeReplica();
        $authHeader = $this->getUserAuthHeader();
        $id = $this->createObject('documents', ['title' => 'Lag'], $authHeader);
        $this->configRequestHeaders('DELETE', $authHeader);
        $this->delete(sprintf('/documents/%s', $id));
        $this->assertResponseCode(204);
        $this->resetProcess();

        $this->configRequestHeaders('GET', $authHeader);
        $this->get(sprintf('/trash/%s', $id));

        $this->assertResponseCode(200);
        $this->assertFallback('ObjectTypes');
    }

    /**
     * Data provider for `testFallbackTree`.
     *
     * @return array
     */
    public static function fallbackTreeProvider(): array
    {
        return [
            'trees' => ['/trees/root-folder/title-two', 'Trees'],
            'tree paths' => ['/tree_paths/root-folder-11/title-two', 'Objects'],
        ];
    }

    /**
     * Test that a tree position missing on the replica is read from the primary.
     *
     * @param string $url Request URL.
     * @param string $table Alias of the table of the lookup that misses.
     * @return void
     */
    #[DataProvider('fallbackTreeProvider')]
    public function testFallbackTree(string $url, string $table): void
    {
        $this->freezeReplica();
        $authHeader = $this->getUserAuthHeader();
        $this->configRequestHeaders('POST', $authHeader);
        $this->post('/folders/11/relationships/children', json_encode(['data' => [['type' => 'documents', 'id' => '3']]]));
        $this->assertResponseOk();
        $this->resetProcess();

        $this->configRequestHeaders('GET', $authHeader);
        $this->get($url);

        $this->assertResponseCode(200);
        $this->assertFallback($table);
    }

    /**
     * Test that a stream missing on the replica is downloaded from the primary.
     *
     * @return void
     */
    public function testFallbackDownload(): void
    {
        $this->filesystemSetup();
        try {
            $this->freezeReplica();
            $authHeader = $this->getUserAuthHeader();
            $this->configRequestHeaders('POST', $authHeader + ['Content-Type' => 'text/plain']);
            $this->post('/streams/upload/lag.txt', 'Lag');
            $this->assertResponseCode(201);
            $uuid = json_decode((string)$this->_response->getBody(), true)['data']['id'];
            $this->resetProcess();

            $this->configRequestHeaders('GET', $authHeader);
            $this->get(sprintf('/streams/download/%s', $uuid));

            $this->assertResponseCode(200);
            $this->assertResponseEquals('Lag');
            $this->assertFallback('Streams');
        } finally {
            $this->filesystemRestore();
        }
    }

    /**
     * Test that a user missing on the replica is read from the primary.
     *
     * @return void
     */
    public function testFallbackAuthUser(): void
    {
        $this->freezeReplica();
        $this->createObject('users', ['username' => 'lag-user', 'password' => 'lag-password', 'status' => 'on'], $this->getUserAuthHeader());
        $authHeader = $this->getUserAuthHeader('lag-user', 'lag-password');
        $this->resetProcess();

        $this->configRequestHeaders('GET', $authHeader);
        $this->get('/auth/user');

        $this->assertResponseCode(200);
        $this->assertFallback('Users');
    }
}
