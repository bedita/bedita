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
namespace BEdita\Core\Test\TestCase\TestSuite;

use BEdita\Core\TestSuite\ShellTestCase;
use Cake\Console\Shell;
use Cake\TestSuite\TestCase;

/**
 * SimpleShell class
 */
class SimpleShell extends Shell
{
    /**
     * {@inheritDoc}
     */
    protected function _welcome()
    {
        return null;
    }

    /**
     * Write output
     *
     * @return null
     */
    public function writeOutput()
    {
        $this->out('Gustavo is');
        $this->out('the man!');
    }

    /**
     * Write error
     *
     * @return null
     */
    public function writeError()
    {
        $this->err('Gustavo!');
        $this->err('Wait!');
    }

    /**
     * Fatal error
     *
     * @return null
     */
    public function writeFatalError()
    {
        $this->error('Gustavo, noooo... you did it!');
    }

    /**
     * Abort
     *
     * @return null
     */
    public function writeAbort()
    {
        $this->abort('Aborting');
    }
}

/**
 * {@see \BEdita\Core\TestSuite\ShellTestCase} Test Case
 *
 * @coversDefaultClass \BEdita\Core\TestSuite\ShellTestCase
 */
class ShellTestCaseTest extends ShellTestCase
{
    /**
     * Test invoke
     *
     * @return void
     * @covers ::invoke()
     */
    public function testInvoke()
    {
        $ret = $this->invoke([SimpleShell::class, 'writeOutput']);
        $this->assertEquals(0, $ret);

        $ret = $this->invoke([SimpleShell::class, 'writeAbort']);
        $this->assertEquals(1, $ret);
        $this->assertAborted();
    }

    /**
     * Test output
     *
     * @return void
     * @covers ::getOutput()
     * @covers ::assertOutputEquals
     * @covers ::assertOutputContains
     * @covers ::assertNotAborted
     */
    public function testOutput()
    {
        $this->invoke([SimpleShell::class, 'writeOutput']);
        $this->assertOutputEquals('Gustavo is' . PHP_EOL . 'the man!');
        $this->assertOutputContains('is' . PHP_EOL . 'the');
        $this->assertNotAborted();
    }

    /**
     * Test error
     *
     * @return void
     * @covers ::getError()
     * @covers ::assertErrorEquals
     * @covers ::assertErrorContains
     * @covers ::assertNotAborted
     */
    public function testError()
    {
        $this->invoke([SimpleShell::class, 'writeError']);
        $this->assertErrorEquals('<error>Gustavo!</error>' . PHP_EOL . '<error>Wait!</error>');
        $this->assertErrorContains('Wait');
        $this->assertNotAborted();
    }

    /**
     * Test fatal error
     *
     * @return void
     * @covers ::getError()
     * @covers ::assertErrorEquals
     * @covers ::assertErrorContains
     * @covers ::assertAborted
     */
    public function testFatalError()
    {
        $this->invoke([SimpleShell::class, 'writeFatalError']);
        $this->assertErrorEquals('<error>Error:</error> Gustavo, noooo... you did it!');
        $this->assertErrorContains('you did');
        $this->assertAborted();
    }

    /**
     * Test aborted assertions
     *
     * @return void
     * @covers ::assertAborted
     */
    public function testAborted()
    {
        $this->invoke([SimpleShell::class, 'writeAbort']);
        $this->assertAborted();
    }
}
