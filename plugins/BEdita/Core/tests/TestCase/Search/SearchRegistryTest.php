<?php
declare(strict_types=1);

namespace BEdita\Core\Test\TestCase\Search;

use BEdita\Core\Search\BaseAdapter;
use BEdita\Core\Search\SearchRegistry;
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
