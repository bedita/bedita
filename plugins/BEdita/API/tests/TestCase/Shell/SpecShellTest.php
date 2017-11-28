<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Shell;

use Cake\TestSuite\ConsoleIntegrationTestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * \BEdita\API\Shell\SpecShell Test Case
 *
 * @coversDefaultClass \BEdita\API\Shell\SpecShell
 */
class SpecShellTest extends ConsoleIntegrationTestCase
{

    /**
     * Name for temporary configuration file.
     *
     * @var string
     */
    const TEMP_FILE = TMP . 'spec.yaml';

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        if (file_exists(static::TEMP_FILE)) {
            unlink(static::TEMP_FILE);
        }

        parent::tearDown();
    }

    /**
     * Test generate method.
     *
     * @return void
     *
     * @covers ::generate()
     */
    public function testGenerate()
    {
        $this->exec(sprintf('spec generate --output %s', static::TEMP_FILE));

        static::assertFileExists(static::TEMP_FILE);

        $result = Yaml::parse(file_get_contents(static::TEMP_FILE));

        static::assertArrayHasKey('paths', $result);
        static::assertArrayHasKey('definitions', $result);
    }
}
