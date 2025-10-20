<?php
declare(strict_types=1);

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
use BEdita\Core\Model\Entity\Stream;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\Filesystem\ThumbnailGenerator} Test Case
 */
#[CoversClass(ThumbnailGenerator::class)]
class ThumbnailGeneratorTest extends TestCase
{
    /**
     * Test `initialize` method.
     *
     * @return void
     */
    public function testInitialize()
    {
        $config = [
            'my' => 'config',
            'is' => 'bigger',
            'than' => 'yours',
        ];

        $generator = new class extends ThumbnailGenerator {
            public function getUrl(Stream $stream, array $options = []): string
            {
                return '';
            }

            public function generate(Stream $stream, array $options = []): bool
            {
                return true;
            }

            public function exists(Stream $stream, array $options = []): bool
            {
                return true;
            }

            public function delete(Stream $stream): void
            {
            }
        };

        $result = $generator->initialize($config);

        static::assertTrue($result);
        static::assertSame($config['my'], $generator->getConfig('my'));
        static::assertSame($config, $generator->getConfig());
    }
}
