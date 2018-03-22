<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Job\Service;

use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\Filesystem\ThumbnailGenerator;
use BEdita\Core\Job\Service\ThumbnailService;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\MockObject\Matcher\InvokedCount;

/**
 * @coversDefaultClass \BEdita\Core\Job\Service\ThumbnailService
 */
class ThumbnailServiceTest extends TestCase
{

    /**
     * Fixtures.
     *
     * @var string[]
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.media',
        'plugin.BEdita/Core.streams',
    ];

    /**
     * Streams table.
     *
     * @var \BEdita\Core\Model\Table\StreamsTable
     */
    protected $Streams;

    /**
     * Original thumbnail registry.
     *
     * @var \BEdita\Core\Filesystem\ThumbnailRegistry
     */
    protected $originalRegistry;

    /**
     * Original thumbnail configuration.
     *
     * @var array
     */
    protected $originalConfig;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $keys = Thumbnail::configured();
        $this->originalRegistry = Thumbnail::getRegistry();
        $this->originalConfig = array_combine(
            $keys,
            array_map([Thumbnail::class, 'getConfig'], $keys)
        );

        Thumbnail::setRegistry(null);
        foreach ($keys as $config) {
            Thumbnail::drop($config);
        }

        $this->Streams = TableRegistry::get('Streams');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        foreach (Thumbnail::configured() as $config) {
            Thumbnail::drop($config);
        }
        Thumbnail::setRegistry($this->originalRegistry);
        Thumbnail::setConfig($this->originalConfig);
        unset($this->originalConfig, $this->originalRegistry);

        unset($this->Streams);

        parent::tearDown();
    }

    /**
     * Data provider for `testRun` test case.
     *
     * @return array
     */
    public function runProvider()
    {
        return [
            'ok' => [
                true,
                static::once(),
                [
                    'uuid' => 'e5afe167-7341-458d-a1e6-042e8791b0fe',
                    'generator' => 'test',
                    'options' => [
                        'gustavo' => 'supporto',
                    ],
                ],
            ],
            'not found' => [
                true,
                static::never(),
                [
                    'uuid' => 'this-uuid-does-not-exist',
                    'generator' => 'test',
                    'options' => [
                        'gustavo' => 'supporto',
                    ],
                ],
            ],
            'error' => [
                false,
                static::once(),
                [
                    'uuid' => 'e5afe167-7341-458d-a1e6-042e8791b0fe',
                    'generator' => 'test',
                    'options' => [
                        'gustavo' => 'supporto',
                    ],
                ],
                true,
            ],
        ];
    }

    /**
     * Test `run` method.
     *
     * @param bool $expected Expected result.
     * @param \PHPUnit\Framework\MockObject\Matcher\InvokedCount $count Invocation count of base generator method.
     * @param array $payload Async job payload.
     * @param bool $shouldThrow Should the base generator throw an exception when invoked?
     * @return void
     *
     * @dataProvider runProvider()
     * @covers ::run()
     */
    public function testRun($expected, InvokedCount $count, array $payload, $shouldThrow = false)
    {
        $stream = $this->Streams->find()->where(['uuid' => $payload['uuid']])->first();

        $generator = $this->getMockBuilder(ThumbnailGenerator::class)
            ->setMethods(['generate'])
            ->getMockForAbstractClass();
        $method = $generator->expects($count)
            ->method('generate')
            ->with($stream, $payload['options']);
        if ($shouldThrow) {
            $method->willThrowException(new \Exception('This is an exception'));
        } else {
            $method->willReturn(true);
        }
        Thumbnail::setConfig('test', ['className' => $generator]);

        $result = (new ThumbnailService())->run($payload);

        static::assertSame($expected, $result);
    }
}
