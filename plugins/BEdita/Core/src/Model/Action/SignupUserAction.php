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

use BEdita\Core\Model\Entity\AsyncJob;
use BEdita\Core\Model\Entity\User;
use BEdita\Core\Utility\LoggedUser;
use Cake\I18n\Time;
use Cake\Mailer\MailerAwareTrait;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Command to signup a user.
 *
 * @since 4.0.0
 */
class SignupUserAction extends BaseAction
{
    use MailerAwareTrait;

    /**
     * The UsersTable table
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    protected $Users = null;

    /**
     * The AsynJobs table
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
     * @return \BEdita\Core\Model\Entity\User
     * @throws \Cake\Network\Exception\BadRequestException When validation of `$data['urlOptions']` fails
     */
    public function execute(array $data = [])
    {
        $data['urlOptions'] = (!empty($data['urlOptions']) && is_array($data['urlOptions'])) ? $data['urlOptions'] : [];
        $errors = $this->validate($data['urlOptions']);
        if (!empty($errors)) {
            throw new BadRequestException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => $errors,
            ]);
        }

        $userId = $this->Users->getConnection()->transactional(function () use ($data) {
            $user = $this->createUser($data['data']);
            $job = $this->createSignupJob($user);
            $this->sendMail($user, $job, $data['urlOptions']);

            return $user->id;
        });

        return (new GetObjectAction(['table' => $this->Users]))->execute(['primaryKey' => $userId]);
    }

    /**
     * Validate data.
     *
     * It needs to validate some data that don't concern the User entity
     * as `activation_url` and `redirect_url`
     *
     * @param array $data The data to validate
     * @return array
     */
    protected function validate(array $data)
    {
        $validator = new Validator();
        $validator->setProvider('signup', $this);

        $validator
            ->requirePresence('activation_url')
            ->add('activation_url', 'customUrl', [
                'rule' => 'isValidUrl',
                'provider' => 'signup',
            ])

            ->add('redirect_url', 'customUrl', [
                'rule' => 'isValidUrl',
                'provider' => 'signup',
            ]);

        return $validator->errors($data);
    }

    /**
     * Checks that a value is a valid URL or custom url as myapp://
     *
     * @param string $value The url to check
     * @param array $context The validation context
     * @return bool
     */
    public function isValidUrl($value, array $context = [])
    {
        // check for a valid scheme (https://, myapp://,...)
        $regex = '/(?<scheme>^[a-z][a-z0-9+\-.]*:\/\/).*/';
        if (!preg_match($regex, $value, $matches)) {
            return false;
        }

        // if scheme is not an URL protocol then it's a custom url (myapp://) => ok
        if (!preg_match('/^(https?|ftps?|sftp|file|news|gopher:\/\/)/', $matches['scheme'])) {
            return true;
        }

        if (!empty($context['providers']['default'])) {
            $provider = $context['providers']['default'];
        } else {
            $provider = (new Validator())->getProvider('default');
        }

        return $provider->url($value, true, $context);
    }

    /**
     * Create a new user with status draft.
     *
     * The user is validated using 'signup' validation.
     *
     * @param array $data The data to save
     * @return \BEdita\Core\Model\Entity\User
     */
    protected function createUser(array $data)
    {
        if (!LoggedUser::getUser()) {
            LoggedUser::setUser(['id' => 1]);
        }

        $data = ['status' => 'draft'] + $data;
        $action = new SaveEntityAction(['table' => $this->Users]);

        return $action([
            'entity' => $this->Users->newEntity(),
            'data' => $data,
            'entityOptions' => [
                'validate' => 'signup',
            ]
        ]);
    }

    /**
     * Create the signup async job
     *
     * @param User $user The user created
     * @return \BEdita\Core\Model\Entity\AsyncJob
     */
    protected function createSignupJob(User $user)
    {
        $action = new SaveEntityAction(['table' => $this->AsyncJobs]);

        return $action([
            'entity' => $this->AsyncJobs->newEntity(),
            'data' => [
                'service' => 'signup',
                'payload' => [
                    'user_id' => $user->id,
                ],
                'scheduled_from' => new Time('1 day'),
                'priority' => 1,
            ]
        ]);
    }

    /**
     * Send confirmation email to user
     *
     * @param User $user The user
     * @param AsyncJob $job The referred async job
     * @param array $urlOptions The options used to build activation url
     * @return void
     * @throws \Exception When sending email throws an exception different from \Cake\Network\Exception\SocketException
     */
    protected function sendMail(User $user, AsyncJob $job, array $urlOptions = [])
    {
        $options = [
            'params' => [
                'userId' => $user->id,
                'activationUrl' => $this->getActivationUrl($job, $urlOptions),
            ]
        ];
        $this->getMailer('BEdita/Core.User')->send('signup', [$options]);
    }

    /**
     * Return the signup activation url
     *
     * @param AsyncJob $job The async job entity
     * @param array $urlOptions The options used to build activation url
     * @return string
     */
    protected function getActivationUrl(AsyncJob $job, array $urlOptions)
    {
        $baseUrl = $urlOptions['activation_url'];
        $redirectUrl = empty($urlOptions['redirect_url']) ? '' : '&redirect_url=' . rawurlencode($urlOptions['redirect_url']);
        $baseUrl .= (strpos($baseUrl, '?') === false) ? '?' : '&';

        return sprintf('%sid=%s%s', $baseUrl, $job->uuid, $redirectUrl);
    }
}
