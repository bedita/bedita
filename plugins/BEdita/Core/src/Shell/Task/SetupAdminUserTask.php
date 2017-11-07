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

namespace BEdita\Core\Shell\Task;

use BEdita\Core\Model\Table\UsersTable;
use Cake\Console\Shell;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Task to setup admin user.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\UsersTable $Users
 */
class SetupAdminUserTask extends Shell
{

    /**
     * Default username of first user.
     *
     * @var string
     */
    const DEFAULT_USERNAME = 'bedita';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
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

        return $parser;
    }

    /**
     * Configure connection.
     *
     * @return void
     */
    public function main()
    {
        $this->loadModel('Users');

        try {
            $this->verbose('=====> Retrieving information for default administrator user... ', 0);
            $user = $this->Users->get(UsersTable::ADMIN_USER);
            $this->verbose('<info>DONE</info>');
        } catch (RecordNotFoundException $e) {
            $this->verbose('<error>FAIL</error>');
            $this->abort(sprintf('Missing user %d!', UsersTable::ADMIN_USER));

            return; // Not needed, but helps analyzers know that the flow is interrupted.
        }

        if ($user->username !== static::DEFAULT_USERNAME) {
            $this->out(sprintf('=====> Administrator user <comment>%s</comment> has already been configured.', $user->username));

            if ($this->param('no-admin-overwrite')) {
                $this->params['admin-overwrite'] = false;
            } elseif (!$this->param('admin-overwrite')) {
                $this->params['admin-overwrite'] = ($this->in('Do you want to overwrite current admin user?', ['y', 'n'], 'n') === 'y');
            }
            if (!$this->param('admin-overwrite')) {
                $this->out('=====> <success>Existing administrator user has been preserved. Don\'t panic!</success>');

                return;
            }
        }

        if (!$this->param('admin-username')) {
            $this->params['admin-username'] = $this->in('Enter new username for default admin user:');
        }
        if (!$this->param('admin-password')) {
            $this->quiet('=====> <warning>Typing will NOT be hidden!</warning> Please do not enter really sensitive data here.');
            $this->params['admin-password'] = $this->in('Enter new password for default admin user:');
        }

        $user->username = $this->param('admin-username');
        $user->password = $this->param('admin-password');
        $user->blocked = false;

        try {
            $this->verbose('=====> Saving user credentials... ', 0);
            $this->Users->saveOrFail($user, ['associated' => false]);
            $this->verbose('<info>DONE</info>');
        } catch (PersistenceFailedException $e) {
            $this->verbose('<error>FAIL</error>');
            $this->abort($e->getMessage());
        }

        $this->out('=====> <success>Administrator user set up. You are now ready to rock BEdita!</success>');
    }
}
