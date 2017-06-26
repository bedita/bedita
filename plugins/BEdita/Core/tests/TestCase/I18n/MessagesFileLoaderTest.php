<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\I18n;

use BEdita\Core\I18n\MessagesFileLoader;
use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\I18n\MessagesFileLoader
 */
class MessagesFileLoaderTest extends TestCase
{

    /**
     * Test constructor.
     *
     * @return void
     *
     * @covers ::__construct()
     */
    public function testConstruct()
    {
        $plugins = ['BEdita/Core'];

        $loader = new MessagesFileLoader('bedita', 'it_IT', 'po', $plugins);

        static::assertAttributeSame($plugins, 'plugins', $loader);
    }

    /**
     * Test getter of search paths.
     *
     * @return void
     *
     * @covers ::translationsFolders()
     */
    public function testTranslationsFolders()
    {
        $expected = [
            Plugin::classPath('BEdita/Core') . 'Locale' . DS . 'it_IT' . DS,
            Plugin::classPath('BEdita/Core') . 'Locale' . DS . 'it' . DS,
        ];
        $plugins = ['BEdita/Core', 'MissingPlugin'];

        $loader = new MessagesFileLoader('bedita', 'it_IT', 'po', $plugins);
        $searchPaths = $loader->translationsFolders();

        foreach ($expected as $path) {
            static::assertContains($path, $searchPaths);
        }
    }
}
