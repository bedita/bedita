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

use BEdita\API\Shell\SpecShell;
use Cake\TestSuite\TestCase;

/**
 * \BEdita\API\Shell\SpecShell Test Case
 *
 * @coversDefaultClass \BEdita\API\Shell\SpecShell
 */
class SpecShellTest extends TestCase
{

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \BEdita\API\Shell\SpecShell
     */
    public $SpecShell;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->SpecShell = new SpecShell($this->io);
        $this->SpecShell->initialize();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SpecShell);

        parent::tearDown();
    }

    /**
     * Test getOptionParser method
     *
     * @return void
     * @coversNothing
     */
    public function testGetOptionParser()
    {
        $parser = $this->SpecShell->getOptionParser();
        $subCommands = $parser->subcommands();
        $this->assertCount(1, $subCommands);
    }

    /**
     * Test generate method
     *
     * @return void
     *
     * @covers ::generate()
     */
    public function testGenerate()
    {
        $yamlFile = tempnam(TMP, '__testyaml');
        if (file_exists($yamlFile)) {
            unlink($yamlFile);
        }
        $this->assertFileNotExists($yamlFile);
        $this->SpecShell->params['output'] = $yamlFile;

        $this->SpecShell->generate();
        $this->assertFileExists($yamlFile);

        $mapChoice = [
            ['Overwrite yaml file "' . $yamlFile . '"?', ['y', 'n'], 'n', 'n'],
        ];
        $this->io->method('askChoice')
             ->will($this->returnValueMap($mapChoice));
        $this->SpecShell->generate();

        unlink($yamlFile);
        $this->assertFileNotExists($yamlFile);
    }
}
