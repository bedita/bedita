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

namespace BEdita\Core\Model\Action;

use BEdita\Core\Model\Entity\User;
use Cake\I18n\Time;
use Cake\Mailer\MailerAwareTrait;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ConflictException;
use Cake\ORM\TableRegistry;

/**
 * Command for activate a user after signup
 *
 * @since 4.0.0
 */
class SignupUserActivationAction extends BaseAction
{
    use MailerAwareTrait;

    /**
     * The UsersTable table
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    protected $Users = null;

    /**
     * The AsyncJobs table
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected $AsyncJobs = null;

    /**
     * {@inheritDoc}
     */
    protected function initialize(array $config)
    {
        $this->Users = TableRegistry::get('Users');
        $this->AsyncJobs = TableRegistry::get('AsyncJobs');
    }

    /**
     * {@inheritDoc}
     *
     * @throws BadRequestException When missing id or async_jobs row is invalid
     * @throws \ConflictException When the user is already active
     */
    public function execute(array $data = [])
    {
        if (empty($data['uuid'])) {
            throw new BadRequestException(__d('bedita', 'Parameter "{0}" missing', ['uuid']));
        }

        $asyncJob = $this->AsyncJobs
            ->find('uncompleted')
            ->where([$this->AsyncJobs->aliasField('uuid') => $data['uuid']])
            ->firstOrFail();

        if (empty($asyncJob->payload['user_id'])) {
            throw new BadRequestException(__d('bedita', 'Invalid async job, missing user_id'));
        }

        $user = $this->Users->get($asyncJob->payload['user_id']);
        if ($user->status == 'on') {
            throw new ConflictException(__d('bedita', 'User already active'));
        }

        // the user is the creator of himself
        $user->created_by = $user->id;
        $user->modified_by = $user->id;
        $user->status = 'on';
        $this->Users->saveOrFail($user);

        $asyncJob->completed = new Time();
        $this->AsyncJobs->save($asyncJob);

        $this->sendMail($user);

        return $user;
    }

    /**
     * Send welcome email to user to inform him of successfully activation
     *
     * @param User $user The user
     * @return void
     */
    protected function sendMail(User $user)
    {
        $options = [
            'params' => [
                'user' => $user,
            ]
        ];
        $this->getMailer('BEdita/Core.User')->send('welcome', [$options]);
    }
}
