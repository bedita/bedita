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

use BEdita\Core\Filesystem\ThumbnailGenerator;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Filesystem\ThumbnailGenerator
 */
class ThumbnailGeneratorTest extends TestCase
{

    /**
     * Test `initialize` method.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testInitialize()
    {
        $config = [
            'my' => 'config',
            'is' => 'bigger',
            'than' => 'yours',
        ];

        /** @var \BEdita\Core\Filesystem\ThumbnailGenerator $mock */
        $mock = $this->getMockBuilder(ThumbnailGenerator::class)
            ->getMockForAbstractClass();

        $result = $mock->initialize($config);

        static::assertTrue($result);
        static::assertSame($config['my'], $mock->getConfig('my'));
        static::assertSame($config, $mock->getConfig());
    }
}
