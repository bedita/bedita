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
use BEdita\Core\Model\Table\RolesTable;
use BEdita\Core\Model\Validation\Validation;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventListenerInterface;
use Cake\Http\Client;
use Cake\I18n\Time;
use Cake\Mailer\MailerAwareTrait;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Command to signup a user.
 *
 * @since 4.0.0
 */
class SignupUserAction extends BaseAction implements EventListenerInterface
{

    use EventDispatcherTrait;
    use MailerAwareTrait;

    /**
     * The UsersTable table
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    protected $Users;

    /**
     * The AsyncJobs table
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected $AsyncJobs;

    /**
     * The RolesTable table
     *
     * @var \BEdita\Core\Model\Table\RolesTable
     */
    protected $Roles;

    /**
     * {@inheritdoc}
     */
    protected function initialize(array $config)
    {
        $this->Users = TableRegistry::get('Users');
        $this->AsyncJobs = TableRegistry::get('AsyncJobs');
        $this->Roles = TableRegistry::get('Roles');

        $this->getEventManager()->on($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return \BEdita\Core\Model\Entity\User
     * @throws \Cake\Network\Exception\BadRequestException When validation of URL options fails
     * @throws \Cake\Network\Exception\UnauthorizedException Upon external authorization check failure.
     */
    public function execute(array $data = [])
    {
        $data = $this->normalizeInput($data);
        $errors = $this->validate($data['data']);
        if (!empty($errors)) {
            throw new BadRequestException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => $errors,
            ]);
        }

        // operations are not in transaction because AsyncJobs could use a different connection
        $user = $this->createUser($data['data']);
        if ($user === false) {
            throw new UnauthorizedException(__d('bedita', 'External auth failed'));
        }
        try {
            // add roles to user, with validity check
            $this->addRoles($user, $data['data']);

            if (empty($data['data']['auth_provider'])) {
                $job = $this->createSignupJob($user);
                $activationUrl = $this->getActivationUrl($job, $data['data']);

                $this->dispatchEvent('Auth.signup', [$user, $job, $activationUrl], $this->Users);
            } else {
                $this->dispatchEvent('Auth.signupActivation', [$user], $this->Users);
            }
        } catch (\Exception $e) {
            // if async job or send mail fail remove user created and re-throw the exception
            $this->Users->delete($user);

            throw $e;
        }

        return (new GetObjectAction(['table' => $this->Users]))->execute(['primaryKey' => $user->id, 'contain' => 'Roles']);
    }

    /**
     * Normalize input data to plain JSON if in JSON API format
     *
     * @param array $data Input data
     * @return array Normalized array
     */
    protected function normalizeInput(array $data)
    {
        if (!empty($data['data']['data']['attributes'])) {
            $meta = !empty($data['data']['data']['meta']) ? $data['data']['data']['meta'] : [];
            $data['data'] = array_merge($data['data']['data']['attributes'], $meta);
        }

        return $data;
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
        $validator->setProvider('bedita', Validation::class);

        if (empty($data['auth_provider'])) {
            $validator->requirePresence('activation_url');
        } else {
            $validator
                ->requirePresence('provider_username')
                ->requirePresence('access_token');
        }

        $validator
            ->add('activation_url', 'customUrl', [
                'rule' => 'url',
                'provider' => 'bedita',
            ])

            ->add('redirect_url', 'customUrl', [
                'rule' => 'url',
                'provider' => 'bedita',
            ]);

        return $validator->errors($data);
    }

    /**
     * Create a new user with status:
     *  - `on` if an external auth provider is used or no activation
     *    is required via `Signup.requireActivation` config
     *  - `draft` in other cases.
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

        $status = 'draft';
        if (!empty($data['auth_provider']) || Configure::read('Signup.requireActivation') === false) {
            $status = 'on';
        }
        unset($data['status']);

        $authProvider = $this->checkExternalAuth($data);
        if (!$authProvider) {
            return false;
        }

        $validate = empty($data['auth_provider']) ? 'signup' : 'signupExternal';
        $action = new SaveEntityAction(['table' => $this->Users]);
        $user = $action([
            'entity' => $this->Users->newEntity()->set('status', $status),
            'data' => $data,
            'entityOptions' => [
                'validate' => $validate,
            ],
        ]);

        if (empty($data['auth_provider'])) {
            return $user;
        }

        $params = empty($data['provider_userdata']) ? null : $data['provider_userdata'];
        $providerUsername = $data['provider_username'];
        $userId = $user->get('id');
        $this->Users->dispatchEvent('Auth.externalAuth', compact('authProvider', 'providerUsername', 'userId', 'params'));

        return $user;
    }

    /**
     * Check external auth data validity
     *
     * To perform external auth check these fields are mandatory:
     *  - "auth_provider": provider name like "google", "facebook"... must be in `auth_providers`
     *  - "provider_username": id or username of the provider
     *  - "access_token": token returned by provider to use in check
     *
     * @param array $data The signup data
     * @return \BEdita\Core\Model\Entity\AuthProviderUser|bool AuthProvider entity or true on success, false on failure
     */
    protected function checkExternalAuth(array $data)
    {
        if (empty($data['auth_provider'])) {
            return true;
        }
        /** @var \BEdita\Core\Model\Entity\AuthProvider $authProvider */
        $authProvider = TableRegistry::get('AuthProviders')->find('enabled', ['name' => $data['auth_provider']])->first();
        if (empty($authProvider)) {
            return false;
        }
        $providerResponse = $this->getOAuth2Response($authProvider->get('url'), $data['access_token']);
        if (!$authProvider->checkAuthorization($providerResponse, $data['provider_username'])) {
            return false;
        }

        return $authProvider;
    }

    /**
     * Get response from an OAuth2 provider
     *
     * @param string $url OAuth2 provider URL
     * @param string $accessToken Access token to use in request
     * @return array Response from an OAuth2 provider
     * @codeCoverageIgnore
     */
    protected function getOAuth2Response($url, $accessToken)
    {
        /** @var \Cake\Http\Client\Response $response */
        $response = (new Client())->get($url, [], ['headers' => ['Authorization' => 'Bearer ' . $accessToken]]);

        return $response->json;
    }

    /**
     * Add roles to user if requested, with validity check
     *
     * @param \BEdita\Core\Model\Entity\User $entity The user created
     * @param array $data The signup data
     * @return void
     */
    protected function addRoles(User $entity, array $data)
    {
        if (empty($data['roles'])) {
            return;
        }
        $roles = $this->loadRoles($data['roles']);
        $association = $this->Users->associations()->getByProperty('roles');
        $association->link($entity, $roles);
    }

    /**
     * Load requested roles entities with validation
     *
     * @param array $roles Requested role names
     * @return \BEdita\Core\Model\Entity\Role[] requested role entities
     * @throws \Cake\Network\Exception\BadRequestException When role validation fails
     */
    protected function loadRoles(array $roles)
    {
        $entities = [];
        $allowed = (array)Configure::read('Signup.roles');
        foreach ($roles as $name) {
            $role = $this->Roles->find()->where(compact('name'))->first();
            if (RolesTable::ADMIN_ROLE === $role->get('id') || !in_array($name, $allowed)) {
                throw new BadRequestException(__d('bedita', 'Role "{0}" not allowed on signup', [$name]));
            }
            $entities[] = $role;
        }

        return $entities;
    }

    /**
     * Create the signup async job
     *
     * @param \BEdita\Core\Model\Entity\User $user The user created
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
            ],
        ]);
    }

    /**
     * Send confirmation email to user
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \BEdita\Core\Model\Entity\User $user The user
     * @param \BEdita\Core\Model\Entity\AsyncJob $job The referred async job
     * @param string $activationUrl URL to be used for activation.
     * @return void
     */
    public function sendMail(Event $event, User $user, AsyncJob $job, $activationUrl)
    {
        $options = [
            'params' => compact('activationUrl', 'user'),
        ];
        $this->getMailer('BEdita/Core.User')->send('signup', [$options]);
    }

    /**
     * Send welcome email to user to inform of successfully activation
     * External auth users are already activated
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \BEdita\Core\Model\Entity\User $user The user
     * @return void
     */
    public function sendActivationMail(Event $event, User $user)
    {
        $options = [
            'params' => compact('user'),
        ];
        $this->getMailer('BEdita/Core.User')->send('welcome', [$options]);
    }

    /**
     * Return the signup activation url
     *
     * @param \BEdita\Core\Model\Entity\AsyncJob $job The async job entity
     * @param array $urlOptions The options used to build activation url
     * @return string
     */
    protected function getActivationUrl(AsyncJob $job, array $urlOptions)
    {
        $baseUrl = $urlOptions['activation_url'];
        $redirectUrl = empty($urlOptions['redirect_url']) ? '' : '&redirect_url=' . rawurlencode($urlOptions['redirect_url']);
        $baseUrl .= (strpos($baseUrl, '?') === false) ? '?' : '&';

        return sprintf('%suuid=%s%s', $baseUrl, $job->uuid, $redirectUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function implementedEvents()
    {
        return [
            'Auth.signup' => 'sendMail',
            'Auth.signupActivation' => 'sendActivationMail',
        ];
    }
}
