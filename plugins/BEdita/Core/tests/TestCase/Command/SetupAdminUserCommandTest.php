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

use BEdita\Core\Command\SetupAdminUserCommand;
use BEdita\Core\Model\Table\UsersTable;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\SetupAdminUserCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\SetupAdminUserCommand
 */
class SetupAdminUserCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Users table.
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
        $this->Users = $this->fetchTable('Users');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Users);
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
        $this->exec('setup_admin_user --help');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Setup admin user');
        $this->assertOutputContains('cake setup_admin_user [options]');
        $this->assertOutputContains('--admin-overwrite');
        $this->assertOutputContains('--admin-username');
        $this->assertOutputContains('--admin-password');
        $this->assertOutputContains('--no-admin-overwrite');
    }

    /**
     * Test execution when default admin user is missing.
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteMissingUser(): void
    {
        $this->Users->getConnection()->disableConstraints(function () {
            $this->Users->deleteAll(['id' => UsersTable::ADMIN_USER]);
        });

        $this->exec('setup_admin_user');

        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains(sprintf('Missing user %d!', UsersTable::ADMIN_USER));
    }

    /**
     * Test execution when default admin users has already been configured and is kept as is.
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteExistingUsersNoOverwrite(): void
    {
        $username = 'gustavo-supporto';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        // Invoke task.
        $this->exec('setup_admin_user', ['n']);

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains(sprintf('Administrator user <comment>%s</comment> has already been configured', $username));
        $this->assertOutputContains('Do you want to overwrite current admin user?');
        $this->assertOutputContains('Existing administrator user has been preserved. Don\'t panic!');
        $this->assertErrorEmpty();

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($username, $user->username);
    }

    /**
     * Test execution when default admin users has already been configured and is kept as is with CLI options.
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteExistingUsersNoOverwriteNonInteractive(): void
    {
        $username = 'gustavo-supporto';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        // Invoke task.
        $this->exec(sprintf('%s --no-admin-overwrite', 'setup_admin_user'));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $output = implode(PHP_EOL, $this->_out->messages());
        $this->assertOutputContains(sprintf('Administrator user <comment>%s</comment> has already been configured', $username));
        static::assertStringNotContainsString('Do you want to overwrite current admin user?', $output);
        $this->assertOutputContains('Existing administrator user has been preserved. Don\'t panic!');
        $this->assertErrorEmpty();

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($username, $user->username);
    }

    /**
     * Test execution when default admin users has already been configured and is overwritten.
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteExistingUsersOverwrite(): void
    {
        $username = 'gustavo-supporto';
        $newUsername = 'ovatsug';
        $newPassword = 'otroppus';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        // Invoke task.
        $this->exec('setup_admin_user', ['y', $newUsername, $newPassword]);

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains(sprintf('Administrator user <comment>%s</comment> has already been configured', $username));
        $this->assertOutputContains('Do you want to overwrite current admin user?');
        $this->assertOutputContains('Enter new username for default admin user:');
        $this->assertOutputContains('Enter new password for default admin user:');
        $this->assertOutputContains('Administrator user set up. You are now ready to rock BEdita!');
        $this->assertErrorEmpty();

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($newUsername, $user->username);
    }

    /**
     * Test execution when default admin users has already been configured and is overwritten with CLI options.
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteExistingUsersOverwriteNonInteractive(): void
    {
        $username = 'gustavo-supporto';
        $newUsername = 'ovatsug';
        $newPassword = 'otroppus';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        // Invoke task.
        $this->exec(sprintf('%s --admin-overwrite --admin-username %s --admin-password %s', 'setup_admin_user', $newUsername, $newPassword));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $output = implode(PHP_EOL, $this->_out->messages());
        $this->assertOutputContains(sprintf('Administrator user <comment>%s</comment> has already been configured', $username));
        static::assertStringNotContainsString('Do you want to overwrite current admin user?', $output);
        static::assertStringNotContainsString('Enter new username for default admin user:', $output);
        static::assertStringNotContainsString('Enter new password for default admin user:', $output);
        $this->assertOutputContains('Administrator user set up. You are now ready to rock BEdita!');
        $this->assertErrorEmpty();

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($newUsername, $user->username);
    }

    /**
     * Test execution when default admin users hasn't been configured yet.
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecuteDefaultUser(): void
    {
        $username = SetupAdminUserCommand::DEFAULT_USERNAME;
        $newUsername = 'ovatsug';
        $newPassword = 'otroppus';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        // Invoke task.
        $this->exec(sprintf('%s --admin-username %s --admin-password %s', 'setup_admin_user', $newUsername, $newPassword));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $output = implode(PHP_EOL, $this->_out->messages());
        static::assertStringNotContainsString('has already been configured', $output);
        static::assertStringNotContainsString('Do you want to overwrite current admin user?', $output);
        static::assertStringNotContainsString('Enter new username for default admin user:', $output);
        static::assertStringNotContainsString('Enter new password for default admin user:', $output);
        $this->assertOutputContains('Administrator user set up. You are now ready to rock BEdita!');
        $this->assertErrorEmpty();

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($newUsername, $user->username);
    }

    /**
     * Test execution when persistence of user credential fails.
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecutePersistenceFailed(): void
    {
        $username = SetupAdminUserCommand::DEFAULT_USERNAME;
        $newUsername = 'second user';
        $newPassword = 'whatever';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        // Invoke task.
        $this->exec(sprintf('%s --admin-username "%s" --admin-password %s', 'setup_admin_user', $newUsername, $newPassword));

        $this->assertExitCode(Command::CODE_ERROR);
        $output = implode(PHP_EOL, $this->_out->messages());
        static::assertStringNotContainsString('has already been configured', $output);
        static::assertStringNotContainsString('Do you want to overwrite current admin user?', $output);
        static::assertStringNotContainsString('Enter new username for default admin user:', $output);
        static::assertStringNotContainsString('Enter new password for default admin user:', $output);
        $this->assertErrorContains('Entity save failure');

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($username, $user->username);
    }
}
