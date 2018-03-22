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

namespace BEdita\Core\Test\TestCase\Filesystem\Thumbnail;

use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\Filesystem\Thumbnail\AsyncGenerator;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Filesystem\Thumbnail\AsyncGenerator
 */
class AsyncGeneratorTest extends TestCase
{

    /**
     * Fixtures.
     *
     * @var string[]
     */
    public $fixtures = [
        'plugin.BEdita/Core.async_jobs',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.media',
        'plugin.BEdita/Core.streams',
    ];

    /**
     * Generator instance.
     *
     * @var \BEdita\Core\Filesystem\Thumbnail\AsyncGenerator
     */
    protected $generator;

    /**
     * Async Jobs table.
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected $AsyncJobs;

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
     * Original thumbnail config.
     *
     * @var string
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

        Thumbnail::setConfig('test', ['className' => TestGenerator::class]);

        $this->AsyncJobs = TableRegistry::get('AsyncJobs');
        $this->Streams = TableRegistry::get('Streams');
        $this->generator = new AsyncGenerator();
        $this->generator->initialize(['baseGenerator' => 'test']);
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

        unset($this->AsyncJobs, $this->Streams, $this->generator);

        parent::tearDown();
    }

    /**
     * Test `getUrl` method.
     *
     * @return void
     *
     * @covers ::getUrl()
     */
    public function testGetUrl()
    {
        $uuid = 'e5afe167-7341-458d-a1e6-042e8791b0fe';
        $stream = $this->Streams->get($uuid);
        $options = ['gustavo' => 'supporto'];

        $url = $this->generator->getUrl($stream, $options);

        static::assertSame(TestGenerator::THUMBNAIL_URL, $url);
    }

    /**
     * Test `generate` method.
     *
     * @return void
     *
     * @covers ::generate()
     */
    public function testGenerate()
    {
        $uuid = 'e5afe167-7341-458d-a1e6-042e8791b0fe';
        $stream = $this->Streams->get($uuid);
        $options = ['gustavo' => 'supporto'];

        $ready = $this->generator->generate($stream, $options);

        static::assertFalse($ready);

        /** @var \BEdita\Core\Model\Entity\AsyncJob $asyncJob */
        $asyncJob = $this->AsyncJobs->find()
            ->where([
                'service' => $this->generator->getConfig('service'),
                'created' => Time::now(),
            ])
            ->firstOrFail();
        $generator = 'test';

        static::assertArraySubset(compact('uuid', 'options', 'generator'), $asyncJob->payload);
    }

    /**
     * Test `exists` method.
     *
     * @return void
     *
     * @covers ::exists()
     */
    public function testExists()
    {
        $uuid = 'e5afe167-7341-458d-a1e6-042e8791b0fe';
        $stream = $this->Streams->get($uuid);
        $options = ['gustavo' => 'supporto'];

        $expected = Thumbnail::getGenerator('test')->exists($stream, $options);
        $exists = $this->generator->exists($stream, $options);

        static::assertSame($expected, $exists);
    }
}
