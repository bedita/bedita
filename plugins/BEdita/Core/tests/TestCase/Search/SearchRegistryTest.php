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

use BEdita\Core\Search\Adapter\SimpleAdapter;
use BEdita\Core\Search\BaseAdapter;
use BEdita\Core\Search\SearchRegistry;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use RuntimeException;

/**
 * @coversDefaultClass \BEdita\Core\Search\SearchRegistry
 */
class SearchRegistryTest extends TestCase
{
    /**
     * SearchRegistry instance
     *
     * @var \BEdita\Core\Search\SearchRegistry
     */
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
                'default',
                [
                    'className' => new class extends BaseAdapter {
                        public function search(Query $query, string $text, array $options = [], array $config = []): Query
                        {
                            return $query;
                        }

                        public function indexResource(EntityInterface $entity, string $operation): void
                        {
                        }
                    },
                ],
            ],
            'wrong class' => [
                new RuntimeException(sprintf('Search adapters must use %s as a base class.', BaseAdapter::class)),
                'default',
                [
                    'className' => new \stdClass(),
                ],
            ],
            'SimpleAdapter by name' => [
                SimpleAdapter::class,
                'BEdita/Core.Simple',
                [],
            ],
            'Adapter not found' => [
                new \BadMethodCallException('Search adapter FakeAdapter is not available.'),
                'FakeAdapter',
                [],
            ],
        ];
    }

    /**
     * Test case for {@see SearchRegistry::load()} method.
     *
     * @param \Exception|string|null $expected The expected result
     * @param string $name Adapter name
     * @param array $config Adapter configuration
     * @return void
     * @covers ::_create()
     * @covers ::_resolveClassName()
     * @covers ::_throwMissingClassError()
     * @dataProvider loadProvider()
     */
    public function testLoad($expected, string $name, array $config): void
    {
        if ($expected instanceof \Exception) {
            $this->expectExceptionObject($expected);
        }

        $adapter = $this->registry->load($name, $config);

        $className = Hash::get($config, 'className');
        if (is_object($className)) {
            static::assertSame($className, $adapter);
        }

        if (is_string($expected)) {
            static::assertInstanceOf($expected, $adapter);
        }
    }
}
