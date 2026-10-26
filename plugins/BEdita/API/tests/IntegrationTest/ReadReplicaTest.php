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
use Cake\Cache\Cache;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;

/**
 * Test which role queries run on, with a `read` driver configured.
 *
 * @coversNothing
 */
class ReadReplicaTest extends IntegrationTestCase
{
    /**
     * @inheritDoc
     */
    protected $fixtures = [
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

        // Use a cache for object types, otherwise all queries go to the primary because of the `StaticPropertiesTabble` temp table.
        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'Array']);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Connection::setSticky(false);
        Connection::setReplica($this->replica);
        $this->spy->getDriver(Connection::ROLE_READ)->disconnect();
        $this->spy->getDriver(Connection::ROLE_WRITE)->disconnect();
        $this->original->getDriver()->disconnect();
        ConnectionManager::drop('test');
        // Restore the configuration, not the instance
        ConnectionManager::setConfig('test', $this->originalConfig);
        $this->getTableLocator()->clear();
        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'Null']);

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
            $this->spy->executed
        );
        static::assertSame([], array_values(array_filter($this->spy->executed, fn (array $query): bool => $query['actual'] === 'read')));
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
            $this->spy->executed
        );
        static::assertContains(
            ['type' => 'select', 'table' => 'Properties', 'requested' => 'read', 'actual' => 'read'],
            $this->spy->executed
        );
        static::assertFalse(Connection::isSticky());
    }

    /**
     * Data provider for `testReplicaOnly`.
     *
     * @return array
     */
    public function replicaOnlyProvider(): array
    {
        return [
            'list' => ['/documents?count=test&include=test', 'Documents'],
            'distinct list' => ['/documents?filter[test]=4', 'Documents'],
            'view' => ['/documents/2?count=test&include=test', 'Documents'],
            'related' => ['/documents/2/test?count=test', 'Test'],
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
     * @dataProvider replicaOnlyProvider()
     */
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

        static::assertSame([], array_values(array_filter(
            $this->spy->executed,
            fn (array $query): bool => $query['actual'] !== 'read'
        )));
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
        static::assertNotContains('read', array_column(array_slice($this->spy->executed, $first), 'actual'));
    }

    /**
     * Data provider for `testStaticProperties`.
     *
     * @return array
     */
    public function staticPropertiesProvider(): array
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
     * @dataProvider staticPropertiesProvider()
     */
    public function testStaticProperties(string $url): void
    {
        $authHeader = $this->getUserAuthHeader();
        $this->resetProcess();

        $this->configRequestHeaders('GET', $authHeader);
        $this->get($url);

        $this->assertResponseCode(200);
        static::assertContains(
            ['type' => 'select', 'table' => 'Properties', 'requested' => 'read', 'actual' => 'write'],
            $this->spy->executed
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
        $types = array_column($this->spy->executed, 'type');
        $insert = array_search('insert', $types, true);
        static::assertNotFalse($insert);
        static::assertSame(['read'], array_unique(array_column(array_slice($this->spy->executed, 0, $insert), 'actual')));
        static::assertSame(['write'], array_unique(array_column(array_slice($this->spy->executed, $insert), 'actual')));
        static::assertTrue(Connection::isSticky());
    }
}
