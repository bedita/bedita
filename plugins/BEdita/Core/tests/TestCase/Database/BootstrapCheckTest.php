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
use Cake\Core\Exception\CakeException;
use Cake\Core\Plugin;
use Cake\Database\Connection as CakeConnection;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

/**
 * Test the datasource check in Core's `config/bootstrap.php`.
 */
#[CoversNothing]
class BootstrapCheckTest extends TestCase
{
    /**
     * Name of the datasource configured by each test.
     *
     * @var string
     */
    protected const string DATASOURCE = '__bootstrap_check__';

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        ConnectionManager::drop(static::DATASOURCE);

        parent::tearDown();
    }

    /**
     * Run Core's bootstrap again.
     *
     * @return void
     */
    protected function bootstrap(): void
    {
        require Plugin::configPath('BEdita/Core') . 'bootstrap.php';
    }

    /**
     * Data provider for `testAccepted`.
     *
     * @return array
     */
    public static function acceptedProvider(): array
    {
        return [
            'BEdita connection with read' => [
                ['className' => Connection::class, 'driver' => Sqlite::class, 'read' => []],
                Connection::class,
            ],
            'BEdita connection subclass with write' => [
                ['className' => SpyConnection::class, 'driver' => Sqlite::class, 'write' => []],
                SpyConnection::class,
            ],
            'Cake connection without replica' => [
                ['className' => CakeConnection::class, 'driver' => Sqlite::class],
                CakeConnection::class,
            ],
            'Cake connection with read' => [
                ['className' => CakeConnection::class, 'driver' => Sqlite::class, 'read' => []],
                Connection::class,
            ],
            'DSN with read' => [
                ['url' => 'sqlite:///:memory:', 'read' => []],
                Connection::class,
            ],
        ];
    }

    /**
     * Test the datasources the check accepts, and the `className` they end up with.
     *
     * @param array $config Datasource configuration.
     * @param string $expected Expected `className` after the check.
     * @return void
     */
    #[DataProvider('acceptedProvider')]
    public function testAccepted(array $config, string $expected): void
    {
        ConnectionManager::setConfig(static::DATASOURCE, $config);

        $this->bootstrap();

        $actual = (array)ConnectionManager::getConfig(static::DATASOURCE);
        static::assertSame($expected, $actual['className']);
        static::assertSame(array_key_exists('read', $config), array_key_exists('read', $actual));
    }

    /**
     * Test that a datasource with `read` and a class that isn't a BEdita connection is refused.
     *
     * @return void
     */
    public function testRefused(): void
    {
        ConnectionManager::setConfig(static::DATASOURCE, ['className' => stdClass::class, 'read' => []]);

        $this->expectException(CakeException::class);
        $this->expectExceptionMessage(sprintf('Datasource "%s" has `read` or `write` configuration', static::DATASOURCE));

        $this->bootstrap();
    }
}
