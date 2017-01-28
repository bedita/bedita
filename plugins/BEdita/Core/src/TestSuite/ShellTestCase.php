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
namespace BEdita\Core\TestSuite;

use Cake\Console\ConsoleIo;
use Cake\Console\Exception\StopException;
use Cake\Console\ShellDispatcher;
use Cake\TestSuite\Stub\ConsoleOutput;
use Cake\TestSuite\TestCase;

/**
 * A class to help with shell testing.
 *
 * @since 4.0.0
 */
abstract class ShellTestCase extends TestCase
{
    /**
     * Mocked stdout.
     *
     * @var \Cake\TestSuite\Stub\ConsoleOutput
     */
    private $_out;

    /**
     * Mocked stderr.
     *
     * @var \Cake\TestSuite\Stub\ConsoleOutput
     */
    private $_err;

    /**
     * Has the shell been aborted?
     *
     * @var bool
     */
    private $aborted = false;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->_out = new ConsoleOutput();
        $this->_err = new ConsoleOutput();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->_out, $this->_err);

        parent::tearDown();
    }

    /**
     * Return contents written to stdout so far.
     *
     * @return string
     */
    protected function getOutput()
    {
        return implode(PHP_EOL, $this->_out->messages());
    }

    /**
     * Return contents written to stderr so far.
     *
     * @return string
     */
    protected function getError()
    {
        return implode(PHP_EOL, $this->_err->messages());
    }

    /**
     * Assert stdout matches expected value.
     *
     * @param string $expected Expected output.
     * @param string $message The failure message that will be appended to the generated message.
     * @return void
     */
    protected function assertOutputEquals($expected, $message = '')
    {
        $this->assertEquals($expected, $this->getOutput(), $message);
    }

    /**
     * Assert stdout contains the expected value.
     *
     * @param string $needle Expected value.
     * @param string $message The failure message that will be appended to the generated message.
     * @return void
     */
    protected function assertOutputContains($needle, $message = '')
    {
        $this->assertContains($needle, $this->getOutput(), $message);
    }

    /**
     * Assert stderr matches expected value.
     *
     * @param string $expected Expected error.
     * @param string $message The failure message that will be appended to the generated message.
     * @return void
     */
    protected function assertErrorEquals($expected, $message = '')
    {
        $this->assertEquals($expected, $this->getError(), $message);
    }

    /**
     * Assert stderr contains the expected value.
     *
     * @param string $needle Expected value.
     * @param string $message The failure message that will be appended to the generated message.
     * @return void
     */
    protected function assertErrorContains($needle, $message = '')
    {
        $this->assertContains($needle, $this->getError(), $message);
    }

    /**
     * Assert that the shell has been aborted.
     *
     * @param string $message The failure message that will be appended to the generated message.
     * @return void
     */
    protected function assertAborted($message = '')
    {
        $this->assertTrue($this->aborted, $message);
    }

    /**
     * Assert that the shell has not been aborted.
     *
     * @param string $message The failure message that will be appended to the generated message.
     * @return void
     */
    protected function assertNotAborted($message = '')
    {
        $this->assertFalse($this->aborted, $message);
    }

    /**
     * Invoke a shell.
     *
     * @param array $args Array of command line arguments.
     * @param array $extra Array of extra parameters to be passed directly to the shell class.
     * @param \Cake\Console\ConsoleIo|null $io Console IO instance.
     * @return mixed Same value returned by invoked command.
     */
    public function invoke(array $args, array $extra = [], ConsoleIo $io = null)
    {
        $shell = array_shift($args);

        $Shell = (new ShellDispatcher())->findShell($shell);
        $Shell->io($io ?: new ConsoleIo($this->_out, $this->_err));
        $Shell->initialize();

        try {
            return $Shell->runCommand($args, true, $extra + ['required' => true]);
        } catch (StopException $e) {
            $this->aborted = true;

            return 1;
        }
    }
}
