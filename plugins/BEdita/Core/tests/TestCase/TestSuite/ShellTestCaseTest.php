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
     * @return void
     */
    public function writeOutput()
    {
        $this->out('Gustavo is');
        $this->out('the man!');
    }

    /**
     * Write error
     *
     * @return void
     */
    public function writeError()
    {
        $this->err('Gustavo!');
        $this->err('Wait!');
    }

    /**
     * Fatal error
     *
     * @return void
     */
    public function writeFatalError()
    {
        $this->error('Gustavo, noooo... you did it!');
    }

    /**
     * Abort
     *
     * @return void
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
     * Data provider for testInvoke()
     *
     * @return array
     */
    public function invokeProvider()
    {
        return [
            'ok' => [
                0,
                'writeOutput'
            ],
            'errorInfo' => [
                0,
                'writeError'
            ],
            'fatalError' => [
                1,
                'writeFatalError'
            ],
            'abort' => [
                1,
                'writeAbort'
            ]
        ];
    }

    /**
     * Test invoke
     *
     * @param int $expected Expected exit code.
     * @param string $shellMethod Shell method to be invoked.
     * @return void
     *
     * @dataProvider invokeProvider
     * @covers ::invoke()
     */
    public function testInvoke($expected, $shellMethod)
    {
        $ret = $this->invoke([SimpleShell::class, $shellMethod]);
        $this->assertEquals($expected, $ret);
        if ($expected === 0) {
            $this->assertNotAborted();
        } elseif ($expected === 1) {
            $this->assertAborted();
        }
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
