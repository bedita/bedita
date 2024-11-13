<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Command;

use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\CheckFilesystemCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\CheckFilesystemCommand
 */
class CheckFilesystemCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Web server user.
     *
     * @var string
     */
    protected $wwwUser;

    /**
     * Temporary directory for permissions tests.
     *
     * @string
     */
    public const TEMP_DIR = TMP . 'test-permissions';

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
        $this->wwwUser = exec('ps aux | grep -E "[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx" | grep -v root | head -1 | cut -d\\  -f1');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        if (file_exists(static::TEMP_DIR)) {
            if (!is_writable(static::TEMP_DIR)) {
                chmod(static::TEMP_DIR, 0755);
            }
            rmdir(static::TEMP_DIR);
        }
        unset($this->wwwUser);
        parent::tearDown();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @covers ::buildOptionParser()
     * @covers ::getDescription()
     */
    public function testBuildOptionParser(): void
    {
        $this->exec('check_filesystem --help');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Check filesystem permissions');
        $this->assertOutputContains('cake check_filesystem [-h] [--httpd-user] [-q] [-v] [<paths ...>]');
        $this->assertOutputContains('--httpd-user');
        $this->assertOutputContains('Manually set HTTPD user');
        $this->assertOutputContains('paths ...');
        $this->assertOutputContains('List of directories to check if they are writable.');
    }

    /**
     * Test execution when permissions are ok.
     *
     * @return void
     * @covers ::execute()
     * @covers ::getHttpdUser()
     * @covers ::checkPaths()
     */
    public function testExecuteOk()
    {
        mkdir(static::TEMP_DIR);

        $this->exec(sprintf('check_filesystem --httpd-user %s %s', exec('whoami'), static::TEMP_DIR));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertErrorEmpty();
        $this->assertOutputContains('Filesystem permissions look alright. Time to write something in those shiny folders');
    }

    /**
     * Test execution with auto-detection of Web server user when Web server is running.
     *
     * @return void
     * @covers ::execute()
     * @covers ::getHttpdUser()
     */
    public function testExecuteAutodetectOk()
    {
        if (!$this->wwwUser) {
            static::markTestSkipped('No webserver running');
        }

        mkdir(static::TEMP_DIR);
        chmod(static::TEMP_DIR, 0757);

        $this->exec(sprintf('check_filesystem --verbose %s', static::TEMP_DIR));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains(sprintf('Detected webserver user: <info>%s</info>', $this->wwwUser));
        $this->assertErrorEmpty();
    }

    /**
     * Test execution with auto-detection of Web server user when Web server is **NOT** running.
     *
     * @return void
     * @covers ::execute()
     * @covers ::getHttpdUser()
     */
    public function testExecuteAutodetectFail()
    {
        if ($this->wwwUser) {
            static::markTestSkipped('Webserver is running');
        }

        mkdir(static::TEMP_DIR);
        chmod(static::TEMP_DIR, 0757);

        $this->exec(sprintf('check_filesystem %s', static::TEMP_DIR));

        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertOutputContains('Unable to detect webserver user');
        $this->assertErrorEmpty();
    }

    /**
     * Test execution when the path to check does not exist.
     *
     * @return void
     * @covers ::execute()
     * @covers ::checkPaths()
     */
    public function testExecuteMissingDirectory()
    {
        $this->exec(sprintf('check_filesystem --httpd-user %s %s', exec('whoami'), static::TEMP_DIR));

        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains(sprintf('Path "%s" does not exist or is not a directory', static::TEMP_DIR));
    }

    /**
     * Test execution when the path is not writable for the CLI user.
     *
     * @return void
     * @covers ::execute()
     * @covers ::checkPaths()
     */
    public function testExecuteNotWritableCli()
    {
        mkdir(static::TEMP_DIR);
        chmod(static::TEMP_DIR, 0555);

        $this->exec(sprintf('check_filesystem --httpd-user nobody %s', static::TEMP_DIR));

        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertOutputContains(sprintf('Path "%s" might not be writable by CLI user', static::TEMP_DIR));
        $this->assertOutputContains('Potential issues were found, please check your installation');
        $this->assertErrorEmpty();
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
     * @dataProvider executeNotWritableWebServerProvider()
     * @covers ::execute()
     * @covers ::checkPaths()
     */
    public function testExecuteNotWritableWebServer($perms)
    {
        mkdir(static::TEMP_DIR);
        chmod(static::TEMP_DIR, $perms);

        $this->exec(sprintf('check_filesystem --httpd-user nobody %s', static::TEMP_DIR));

        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertOutputContains(sprintf('Path "%s" might not be writable by webserver user', static::TEMP_DIR));
        $this->assertOutputContains('Potential issues were found, please check your installation');
        $this->assertErrorEmpty();
    }

    /**
     * Test execution when the path is world writable.
     *
     * @return void
     * @covers ::execute()
     * @covers ::checkPaths()
     */
    public function testExecuteWorldWritable()
    {
        mkdir(static::TEMP_DIR);
        chmod(static::TEMP_DIR, 0757);

        $this->exec(sprintf('check_filesystem --httpd-user nobody %s', static::TEMP_DIR));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains(sprintf('Path "%s" is world writable!', static::TEMP_DIR));
        $this->assertOutputContains('Filesystem permissions look alright. Time to write something in those shiny folders');
        $this->assertErrorEmpty();
    }
}
