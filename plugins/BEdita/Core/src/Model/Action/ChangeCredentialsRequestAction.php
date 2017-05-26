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
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Command to request an access credentials change
 * Typically a password change
 *
 * Input data MUST contain:
 *  - 'contact' user email, other contact methods in the future
 *  - 'change_url' base URL to use in email sent to user, actual change URL link will be
 *      {change_url}?uuid={uuid}
 *
 * @since 4.0.0
 */
class ChangeCredentialsRequestAction extends BaseAction
{
    use MailerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $errors = $this->validate($data);
        if ($errors !== true) {
            throw new BadRequestException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => $errors,
            ]);
        }

        $usersTable = TableRegistry::get('Users');
        $usersTable->getConnection()->transactional(function () use ($data, $usersTable) {
            $user = $usersTable->find()
                    ->where(['email' => $data['contact']])
                    ->firstOrFail();
            $job = $this->createJob($user);
            $this->sendMail($user, $job->uuid, $data['change_url']);
        });

        return true;
    }

    /**
     * Validate input data.
     *
     * @param array $data Input
     * @return array|true Array of validation errors, or true if input is valid.
     */
    public function validate(array $data)
    {
        $validator = (new Validator())
            ->email('contact')
            ->notEmpty('contact')
            ->requirePresence('contact')

            ->url('change_url')
            ->notEmpty('change_url')
            ->requirePresence('change_url');

        $errors = $validator->errors($data);
        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    /**
     * Create the credentials change async job
     *
     * @param User $user The user requesting change
     * @return \BEdita\Core\Model\Entity\AsyncJob
     */
    protected function createJob(User $user)
    {
        $aysncJobsTable = TableRegistry::get('AsyncJobs');
        $action = new SaveEntityAction(['table' => $aysncJobsTable]);

        return $action([
            'entity' => $aysncJobsTable->newEntity(),
            'data' => [
                'service' => 'credentials_change',
                'payload' => [
                    'user_id' => $user->id,
                ],
                'scheduled_from' => new Time('1 day'),
                'priority' => 1,
            ]
        ]);
    }

    /**
     * Send change request email to user
     *
     * @param User $user The user
     * @param string $uuid Change uuid
     * @param string $changeUrl Base change URL
     * @return void
     * @throws \Exception When sending email throws an exception different from \Cake\Network\Exception\SocketException
     */
    protected function sendMail(User $user, $uuid, $changeUrl)
    {
        $options = [
            'params' => [
                'user' => $user,
                'changeUrl' => $this->getChangeUrl($uuid, $changeUrl),
            ]
        ];
        $this->getMailer('BEdita/Core.User')->send('changeRequest', [$options]);
    }

    /**
     * Return the credentials change url
     *
     * @param string $uuid Change uuid
     * @param string $changeUrl Base change URL
     * @return string
     */
    protected function getChangeUrl($uuid, $changeUrl)
    {
        $changeUrl .= (strpos($changeUrl, '?') === false) ? '?' : '&';

        return sprintf('%suuid=%s', $changeUrl, $uuid);
    }
}
