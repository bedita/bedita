<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Command;

use BEdita\Core\Model\Table\UsersTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * SetupAdminUser command.
 */
class SetupAdminUserCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * Default username of first user.
     *
     * @var string
     */
    public const DEFAULT_USERNAME = 'bedita';

    /**
     * Console arguments
     *
     * @var \Cake\Console\Arguments
     */
    protected $args;

    /**
     * Console IO
     *
     * @var \Cake\Console\ConsoleIo
     */
    protected $io;

    /**
     * Users table
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    protected $table;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->setName('cake setup_admin_user');
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription([
                'Setup admin user.',
            ])
            ->addOption('admin-overwrite', [
                'help' => 'Overwrite current default admin user, if it has already been configured. Useful for unattended runs.',
                'boolean' => true,
            ])
            ->addOption('no-admin-overwrite', [
                'help' => 'Do NOT overwrite current default admin user, if it has already been configured. Useful for unattended runs.',
                'boolean' => true,
            ])
            ->addOption('admin-username', [
                'help' => 'New username for default admin user. Useful for unattended runs.',
                'required' => false,
            ])
            ->addOption('admin-password', [
                'help' => 'New password for default admin user. Useful for unattended runs.',
                'required' => false,
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Setup admin user';
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $this->args = $args;
        $this->io = $io;
        $this->table = $this->fetchTable('Users');

        try {
            $this->io->verbose('=====> Retrieving information for default administrator user... ', 0);
            $user = $this->table->get(UsersTable::ADMIN_USER);
            $this->io->verbose('<info>DONE</info>');
        } catch (RecordNotFoundException $e) {
            $this->io->verbose('<error>FAIL</error>');
            $this->io->abort(sprintf('Missing user %d!', UsersTable::ADMIN_USER));
        }

        if ($user->username !== static::DEFAULT_USERNAME) {
            $this->io->out(sprintf('=====> Administrator user <comment>%s</comment> has already been configured.', $user->username));

            $adminOverwrite = null;
            if ($this->args->getOption('no-admin-overwrite')) {
                $adminOverwrite = false;
            } elseif (!$this->args->getOption('admin-overwrite')) {
                $adminOverwrite = ($this->io->askChoice('Do you want to overwrite current admin user?', ['y', 'n'], 'n') === 'y');
            } else {
                $adminOverwrite = $this->args->getOption('admin-overwrite');
            }
            if (!$adminOverwrite) {
                $this->io->out('=====> <success>Existing administrator user has been preserved. Don\'t panic!</success>');

                return static::CODE_SUCCESS;
            }
        }

        $adminUsername = null;
        if (!$this->args->getOption('admin-username')) {
            $adminUsername = $this->io->ask('Enter new username for default admin user:');
        } else {
            $adminUsername = $this->args->getOption('admin-username');
        }
        $adminPassword = null;
        if (!$this->args->getOption('admin-password')) {
            $this->io->quiet('=====> <warning>Typing will NOT be hidden!</warning> Please do not enter really sensitive data here.');
            $adminPassword = $this->io->ask('Enter new password for default admin user:');
        } else {
            $adminPassword = $this->args->getOption('admin-password');
        }

        $user->username = $adminUsername;
        $user->password = $adminPassword;
        $user->blocked = false;

        try {
            $this->io->verbose('=====> Saving user credentials... ', 0);
            $this->table->saveOrFail($user, ['associated' => false]);
            $this->io->verbose('<info>DONE</info>');
        } catch (PersistenceFailedException $e) {
            $this->io->verbose('<error>FAIL</error>');
            $this->io->abort($e->getMessage());

            return static::CODE_ERROR;
        }

        $this->io->out('=====> <success>Administrator user set up. You are now ready to rock BEdita!</success>');

        return static::CODE_SUCCESS;
    }
}
