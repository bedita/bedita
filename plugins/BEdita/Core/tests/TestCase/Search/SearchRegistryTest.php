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
namespace BEdita\Core\Test\TestCase\Search;

use BEdita\Core\Search\BaseAdapter;
use BEdita\Core\Search\SearchRegistry;
use Cake\ORM\Query;
use Cake\TestSuite\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \BEdita\Core\Search\SearchRegistry
 */
class SearchRegistryTest extends TestCase
{
    protected SearchRegistry $registry;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new SearchRegistry();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->registry);
    }

    /**
     * Data provider for {@see SearchRegistryTest::testLoad()} test case.
     *
     * @return array[]
     */
    public function loadProvider(): array
    {
        return [
            'successful initialization' => [
                null,
                [
                    'className' => new class extends BaseAdapter {
                        public function search(Query $query, string $text, array $options = []): Query
                        {
                            return $query;
                        }
                    },
                ],
            ],
            'failed initialization' => [
                RuntimeException::class,
                [
                    'className' => new class extends BaseAdapter {
                        public function initialize(array $config): bool
                        {
                            return false;
                        }

                        public function search(Query $query, string $text, array $options = []): Query
                        {
                            return $query;
                        }
                    },
                ],
            ],
            'wrong class' => [
                RuntimeException::class,
                [
                    'className' => new \stdClass(),
                ],
            ],
        ];
    }

    /**
     * Test case for {@see SearchRegistry::load()} method.
     *
     * @return void
     * @covers ::_create()
     * @dataProvider loadProvider()
     */
    public function testLoad(?string $expected, array $config): void
    {
        if ($expected !== null) {
            static::expectException($expected);
        }

        $adapter = $this->registry->load('default', $config);

        if (is_object($config['className'])) {
            static::assertSame($config['className'], $adapter);
        }
    }
}
