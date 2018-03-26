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

namespace BEdita\Core\Test\TestCase\Filesystem;

use BEdita\Core\Filesystem\Exception\InvalidStreamException;
use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\Filesystem\ThumbnailGenerator;
use BEdita\Core\Filesystem\ThumbnailRegistry;
use BEdita\Core\Model\Entity\Stream;
use BEdita\Core\Test\TestCase\Filesystem\Thumbnail\TestGenerator;
use BEdita\Core\Utility\Text;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Filesystem\Thumbnail
 */
class ThumbnailTest extends TestCase
{

    /**
     * Name of test configuration for generating thumbnails.
     *
     * @var string
     */
    const TEST_CONFIG = 'test';

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

        parent::tearDown();
    }

    /**
     * Test `setRegistry` method.
     *
     * @return void
     *
     * @covers ::setRegistry()
     */
    public function testSetRegistry()
    {
        $registry = new ThumbnailRegistry();

        Thumbnail::setRegistry($registry);

        static::assertAttributeSame($registry, '_registry', Thumbnail::class);
    }

    /**
     * Test `getRegistry` method.
     *
     * @return void
     *
     * @covers ::getRegistry()
     */
    public function testGetRegistry()
    {
        static::assertAttributeSame(null, '_registry', Thumbnail::class);

        $registry = Thumbnail::getRegistry();

        static::assertInstanceOf(ThumbnailRegistry::class, $registry);
        static::assertAttributeSame($registry, '_registry', Thumbnail::class);

        $nextCall = Thumbnail::getRegistry();

        static::assertSame($registry, $nextCall);
    }

    /**
     * Test `getGenerator` with a generator that hasn't been loaded yet.
     *
     * @return void
     *
     * @covers ::getGenerator()
     */
    public function testGetGeneratorNotLoaded()
    {
        Thumbnail::setConfig(static::TEST_CONFIG, ['className' => TestGenerator::class]);

        static::assertFalse(Thumbnail::getRegistry()->has(static::TEST_CONFIG));

        $generator = Thumbnail::getGenerator(static::TEST_CONFIG);

        static::assertInstanceOf(TestGenerator::class, $generator);
        static::assertTrue(Thumbnail::getRegistry()->has(static::TEST_CONFIG));
    }

    /**
     * Test `getGenerator` with a generator that has already been loaded.
     *
     * @return void
     *
     * @covers ::getGenerator()
     */
    public function testGetGeneratorLoaded()
    {
        $generator = Thumbnail::getRegistry()->load(static::TEST_CONFIG, ['className' => TestGenerator::class]);

        static::assertTrue(Thumbnail::getRegistry()->has(static::TEST_CONFIG));

        $nextCall = Thumbnail::getGenerator(static::TEST_CONFIG);

        static::assertSame($generator, $nextCall);
    }

    /**
     * Data provider for `testGet` test case.
     *
     * @return array
     */
    public function getProvider()
    {
        return [
            'existing' => [
                [
                    'url' => TestGenerator::THUMBNAIL_URL,
                    'ready' => true,
                ],
                true,
            ],
            'synchronous generation' => [
                [
                    'url' => TestGenerator::THUMBNAIL_URL,
                    'ready' => true,
                ],
                false,
                true,
            ],
            'asynchronous generation' => [
                [
                    'url' => TestGenerator::THUMBNAIL_URL,
                    'ready' => false,
                ],
                false,
                false,
            ],
            'unable to generate thumbnail' => [
                [
                    'url' => TestGenerator::THUMBNAIL_URL,
                    'ready' => false,
                    'acceptable' => false,
                ],
                false,
                new InvalidStreamException(),
            ],
            'other error' => [
                new \RuntimeException('Some exception', -1),
                false,
                new \RuntimeException('Some exception', -1),
            ]
        ];
    }

    /**
     * Test `get` method.
     *
     * @param array|\Exception $expected Expected result, or exception.
     * @param bool $exists Does thumbnail already exist?
     * @param \Exception|bool|null $result If thumbnail does not exist, what should be the result
     *      of its generation? `true` for successful synchronous generation, `false` for successful
     *      asynchronous generation, exception for errors.
     * @return void
     *
     * @dataProvider getProvider()
     * @covers ::get()
     * @covers ::getOptions()
     */
    public function testGet($expected, $exists, $result = null)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $options = [
            'some' => 'option',
        ];
        Configure::write('Thumbnails.presets.gustavo', $options + ['generator' => static::TEST_CONFIG]);

        $stream = new Stream(['uuid' => Text::uuid()]);

        $mock = $this->getMockBuilder(ThumbnailGenerator::class)
            ->setMethods(['getUrl', 'exists', 'generate'])
            ->getMockForAbstractClass();
        $mock->expects(static::once())
            ->method('getUrl')
            ->with($stream, $options)
            ->willReturn(TestGenerator::THUMBNAIL_URL);
        $mock->expects(static::once())
            ->method('exists')
            ->with($stream, $options)
            ->willReturn($exists);
        $invocation = $mock->expects($exists ? static::never() : static::once())
            ->method('generate');
        if (!$exists) {
            $invocation = $invocation->with($stream, $options);
            if ($result instanceof \Exception) {
                $invocation->willThrowException($result);
            } else {
                $invocation->willReturn($result);
            }
        }

        Thumbnail::setConfig(static::TEST_CONFIG, ['className' => $mock]);

        $result = Thumbnail::get($stream, 'gustavo');

        static::assertEquals($expected, $result);
    }

    /**
     * Test `getOptions` method with a missing preset.
     *
     * @return void
     *
     * @expectedException \BEdita\Core\Filesystem\Exception\InvalidThumbnailOptionsException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Preset "gustavo" not found
     * @covers ::getOptions()
     */
    public function testGetOptionsMissingPreset()
    {
        Configure::delete('Thumbnails.presets.gustavo');

        Thumbnail::get(new Stream(), 'gustavo');
    }

    /**
     * Test `getOptions` method with custom options disallowed.
     *
     * @return void
     *
     * @expectedException \BEdita\Core\Filesystem\Exception\InvalidThumbnailOptionsException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Thumbnails can only be generated for one of the configured presets
     * @covers ::getOptions()
     */
    public function testGetOptionsCustomNotAllowed()
    {
        Configure::write('Thumbnails.allowAny', false);

        Thumbnail::get(new Stream(), ['generator' => 'whatever', 'w' => -1, 'h' => 'maybe']);
    }

    /**
     * Test `delete` method.
     *
     * @return void
     *
     * @covers ::delete()
     */
    public function testDelete()
    {
        $stream = new Stream(['uuid' => Text::uuid()]);

        $mock = $this->getMockBuilder(ThumbnailGenerator::class)
            ->setMethods(['delete'])
            ->getMockForAbstractClass();
        $mock->expects(static::once())
            ->method('delete')
            ->with($stream);

        Thumbnail::setConfig(static::TEST_CONFIG, ['className' => $mock]);

        Thumbnail::delete($stream);
    }
}
