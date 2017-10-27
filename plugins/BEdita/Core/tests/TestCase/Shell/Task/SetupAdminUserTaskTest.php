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

use BEdita\Core\Model\Table\UsersTable;
use BEdita\Core\Shell\Task\SetupAdminUserTask;
use BEdita\Core\TestSuite\ShellTestCase;
use Cake\Console\ConsoleInput;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;

/**
 * @covers \BEdita\Core\Shell\Task\SetupAdminUserTask
 */
class SetupAdminUserTaskTest extends ShellTestCase
{

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
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Users = TableRegistry::get('Users');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test execution when default admin user is missing.
     *
     * @return void
     */
    public function testExecuteMissingUser()
    {
        $this->Users->deleteAll(['id' => UsersTable::ADMIN_USER]);

        $this->invoke([SetupAdminUserTask::class]);

        $this->assertAborted();
        $this->assertErrorContains(sprintf('Missing user %d!', UsersTable::ADMIN_USER));
    }

    /**
     * Test execution when default admin users has already been configured and is kept as is.
     *
     * @return void
     */
    public function testExecuteExistingUsersNoOverwrite()
    {
        $username = 'gustavo-supporto';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        $stdin = $this->getMockBuilder(ConsoleInput::class)
            ->getMock();
        $stdin->method('read')
            ->willReturnOnConsecutiveCalls('n');
        $io = new ConsoleIo($this->_out, $this->_err, $stdin);

        // Invoke task.
        $this->invoke([SetupAdminUserTask::class], [], $io);

        $this->assertNotAborted();
        $this->assertOutputContains(sprintf('Administrator user <comment>%s</comment> has already been configured', $username));
        $this->assertOutputContains('Do you want to overwrite current admin user?');
        $this->assertOutputContains('Existing administrator user has been preserved. Don\'t panic!');

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($username, $user->username);
    }

    /**
     * Test execution when default admin users has already been configured and is kept as is with CLI options.
     *
     * @return void
     */
    public function testExecuteExistingUsersNoOverwriteNonInteractive()
    {
        $username = 'gustavo-supporto';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        // Invoke task.
        $this->invoke([SetupAdminUserTask::class, '--no-admin-overwrite']);

        $this->assertNotAborted();
        $output = $this->getOutput();
        $this->assertOutputContains(sprintf('Administrator user <comment>%s</comment> has already been configured', $username));
        static::assertNotContains('Do you want to overwrite current admin user?', $output);
        $this->assertOutputContains('Existing administrator user has been preserved. Don\'t panic!');

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($username, $user->username);
    }

    /**
     * Test execution when default admin users has already been configured and is overwritten.
     *
     * @return void
     */
    public function testExecuteExistingUsersOverwrite()
    {
        $username = 'gustavo-supporto';
        $newUsername = 'ovatsug';
        $newPassword = 'otroppus';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        $stdin = $this->getMockBuilder(ConsoleInput::class)
            ->getMock();
        $stdin->method('read')
            ->willReturnOnConsecutiveCalls('y', $newUsername, $newPassword);
        $io = new ConsoleIo($this->_out, $this->_err, $stdin);

        // Invoke task.
        $this->invoke([SetupAdminUserTask::class], [], $io);

        $this->assertNotAborted();
        $this->assertOutputContains(sprintf('Administrator user <comment>%s</comment> has already been configured', $username));
        $this->assertOutputContains('Do you want to overwrite current admin user?');
        $this->assertOutputContains('Enter new username for default admin user:');
        $this->assertOutputContains('Enter new password for default admin user:');
        $this->assertOutputContains('Administrator user set up. You are now ready to rock BEdita!');

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($newUsername, $user->username);
    }

    /**
     * Test execution when default admin users has already been configured and is overwritten with CLI options.
     *
     * @return void
     */
    public function testExecuteExistingUsersOverwriteNonInteractive()
    {
        $username = 'gustavo-supporto';
        $newUsername = 'ovatsug';
        $newPassword = 'otroppus';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        // Invoke task.
        $this->invoke([SetupAdminUserTask::class, '--admin-overwrite', '--admin-username', $newUsername, '--admin-password', $newPassword]);

        $this->assertNotAborted();
        $output = $this->getOutput();
        $this->assertOutputContains(sprintf('Administrator user <comment>%s</comment> has already been configured', $username));
        static::assertNotContains('Do you want to overwrite current admin user?', $output);
        static::assertNotContains('Enter new username for default admin user:', $output);
        static::assertNotContains('Enter new password for default admin user:', $output);
        $this->assertOutputContains('Administrator user set up. You are now ready to rock BEdita!');

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($newUsername, $user->username);
    }

    /**
     * Test execution when default admin users hasn't been configured yet.
     *
     * @return void
     */
    public function testExecuteDefaultUser()
    {
        $username = SetupAdminUserTask::DEFAULT_USERNAME;
        $newUsername = 'ovatsug';
        $newPassword = 'otroppus';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        // Invoke task.
        $this->invoke([SetupAdminUserTask::class, '--admin-overwrite', '--admin-username', $newUsername, '--admin-password', $newPassword]);

        $this->assertNotAborted();
        $output = $this->getOutput();
        static::assertNotContains('has already been configured', $output);
        static::assertNotContains('Do you want to overwrite current admin user?', $output);
        static::assertNotContains('Enter new username for default admin user:', $output);
        static::assertNotContains('Enter new password for default admin user:', $output);
        $this->assertOutputContains('Administrator user set up. You are now ready to rock BEdita!');

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($newUsername, $user->username);
    }

    /**
     * Test execution when persistence of user credential fails.
     *
     * @return void
     */
    public function testExecutePersistenceFailed()
    {
        $username = SetupAdminUserTask::DEFAULT_USERNAME;
        $newUsername = 'second user';
        $newPassword = 'whatever';

        $this->Users->updateAll(compact('username'), ['id' => UsersTable::ADMIN_USER]);

        // Invoke task.
        $this->invoke([SetupAdminUserTask::class, '--admin-overwrite', '--admin-username', $newUsername, '--admin-password', $newPassword]);

        $this->assertAborted();
        $output = $this->getOutput();
        static::assertNotContains('has already been configured', $output);
        static::assertNotContains('Do you want to overwrite current admin user?', $output);
        static::assertNotContains('Enter new username for default admin user:', $output);
        static::assertNotContains('Enter new password for default admin user:', $output);
        $this->assertErrorContains('Entity save failure.');

        $user = $this->Users->get(UsersTable::ADMIN_USER);
        static::assertSame($username, $user->username);
    }
}
