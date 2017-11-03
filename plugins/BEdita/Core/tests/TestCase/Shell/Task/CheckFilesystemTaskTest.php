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

namespace BEdita\Core\Test\TestCase\Shell\Task;

use BEdita\Core\Shell\Task\CheckFilesystemTask;
use BEdita\Core\TestSuite\ShellTestCase;

/**
 * @coversDefaultClass \BEdita\Core\Shell\Task\CheckFilesystemTask
 */
class CheckFilesystemTaskTest extends ShellTestCase
{

    /**
     * Temporary directory for permissions tests.
     *
     * @string
     */
    const TEMP_DIR = TMP . 'test-permissions';

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        if (file_exists(static::TEMP_DIR)) {
            if (!is_writable(static::TEMP_DIR)) {
                chmod(static::TEMP_DIR, 0755);
            }
            rmdir(static::TEMP_DIR);
        }

        parent::tearDown();
    }

    /**
     * Test execution when permissions are ok.
     *
     * @return void
     */
    public function testExecuteOk()
    {
        mkdir(static::TEMP_DIR);

        $result = $this->invoke([CheckFilesystemTask::class, '--httpd-user', exec('whoami'), static::TEMP_DIR]);

        $this->assertNotAborted();
        static::assertTrue($result);
        $this->assertOutputContains('Filesystem permissions look alright. Time to write something in those shiny folders');
    }

    /**
     * Test execution with auto-detection of Web server user when Web server is running.
     *
     * @return void
     *
     * @covers ::main()
     * @covers ::getHttpdUser()
     */
    public function testExecuteAutodetectOk()
    {
        $user = exec('ps aux | grep -E "[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx" | grep -v root | head -1 | cut -d\\  -f1');
        if (!$user) {
            static::markTestSkipped('No webserver running');
        }

        mkdir(static::TEMP_DIR);
        chmod(static::TEMP_DIR, 0757);

        $result = $this->invoke([CheckFilesystemTask::class, '--verbose', static::TEMP_DIR]);

        $this->assertNotAborted();
        static::assertTrue($result);
        $this->assertOutputContains(sprintf('Detected webserver user: <info>%s</info>', $user));
    }

    /**
     * Test execution with auto-detection of Web server user when Web server is **NOT** running.
     *
     * @return void
     *
     * @covers ::main()
     * @covers ::getHttpdUser()
     */
    public function testExecuteAutodetectFail()
    {
        $user = exec('ps aux | grep -E "[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx" | grep -v root | head -1 | cut -d\\  -f1');
        if ($user) {
            static::markTestSkipped('Webserver is running');
        }

        mkdir(static::TEMP_DIR);
        chmod(static::TEMP_DIR, 0757);

        $result = $this->invoke([CheckFilesystemTask::class, static::TEMP_DIR]);

        $this->assertNotAborted();
        static::assertFalse($result);
        $this->assertOutputContains('Unable to detect webserver user');
    }

    /**
     * Test execution when the path to check does not exist.
     *
     * @return void
     *
     * @covers ::main()
     * @covers ::checkPaths()
     */
    public function testExecuteMissingDirectory()
    {
        $this->invoke([CheckFilesystemTask::class, '--httpd-user', exec('whoami'), static::TEMP_DIR]);

        $this->assertAborted();
        $this->assertErrorContains(sprintf('Path "%s" does not exist or is not a directory', static::TEMP_DIR));
    }

    /**
     * Test execution when the path is not writable for the CLI user.
     *
     * @return void
     *
     * @covers ::main()
     * @covers ::checkPaths()
     */
    public function testExecuteNotWritableCli()
    {
        mkdir(static::TEMP_DIR);
        chmod(static::TEMP_DIR, 0555);

        $result = $this->invoke([CheckFilesystemTask::class, '--httpd-user', 'nobody', static::TEMP_DIR]);

        $this->assertNotAborted();
        static::assertFalse($result);
        $this->assertOutputContains(sprintf('Path "%s" might not be writable by CLI user', static::TEMP_DIR));
        $this->assertOutputContains('Potential issues were found, please check your installation');
    }

    /**
     * Data provider for `testExecuteNotWritableWebServer` test case.
     *
     * @return array
     */
    public function executeNotWritableWebServerProvider()
    {
        return [
            'no one can write' => [0555],
            'me can write' => [0755],
            'me fellas can write' => [0575],
            'me and me fellas can write' => [0775],
        ];
    }

    /**
     * Test execution when the path is not writable for HTTPD user.
     *
     * @param int $perms Permissions to be set on folder.
     * @return void
     *
     * @dataProvider executeNotWritableWebServerProvider()
     * @covers ::main()
     * @covers ::checkPaths()
     */
    public function testExecuteNotWritableWebServer($perms)
    {
        mkdir(static::TEMP_DIR);
        chmod(static::TEMP_DIR, $perms);

        $result = $this->invoke([CheckFilesystemTask::class, '--httpd-user', 'nobody', static::TEMP_DIR]);

        $this->assertNotAborted();
        static::assertFalse($result);
        $this->assertOutputContains(sprintf('Path "%s" might not be writable by webserver user', static::TEMP_DIR));
        $this->assertOutputContains('Potential issues were found, please check your installation');
    }

    /**
     * Test execution when the path is world writable.
     *
     * @return void
     *
     * @covers ::main()
     * @covers ::checkPaths()
     */
    public function testExecuteWorldWritable()
    {
        mkdir(static::TEMP_DIR);
        chmod(static::TEMP_DIR, 0757);

        $result = $this->invoke([CheckFilesystemTask::class, '--httpd-user', 'nobody', static::TEMP_DIR]);

        $this->assertNotAborted();
        static::assertTrue($result);
        $this->assertOutputContains(sprintf('Path "%s" is world writable!', static::TEMP_DIR));
        $this->assertOutputContains('Filesystem permissions look alright. Time to write something in those shiny folders');
    }
}
