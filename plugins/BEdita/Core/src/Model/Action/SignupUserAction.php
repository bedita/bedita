<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
use BEdita\Core\Utility\OAuth2;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventListenerInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\I18n\Time;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
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
     * 400 Username already registered
     *
     * @var string
     */
    const BE_USER_EXISTS = 'be_user_exists';

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
     * Default configuration.
     *
     * - activation_url => url used for signup activation
     * - roles => the allowed roles on signup. if empty no role can be associated
     * - defaultRoles => default roles associated to user if no one was specified
     * - requireActivation => false if user will be active without confirm
     *
     * @var array
     */
    protected $_defaultConfig = [
        'activation_url' => null,
        'roles' => null,
        'defaultRoles' => null,
        'requireActivation' => true,
    ];

    /**
     * {@inheritdoc}
     */
    protected function initialize(array $config)
    {
        $this->Users = TableRegistry::getTableLocator()->get('Users');
        $this->AsyncJobs = TableRegistry::getTableLocator()->get('AsyncJobs');
        $this->Roles = TableRegistry::getTableLocator()->get('Roles');

        $this->getEventManager()->on($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return \BEdita\Core\Model\Entity\User
     * @throws \Cake\Http\Exception\BadRequestException When validation of URL options fails
     * @throws \Cake\Http\Exception\UnauthorizedException Upon external authorization check failure.
     */
    public function execute(array $data = [])
    {
        $this->setConfig((array)Configure::read('Signup'));

        $data = $this->normalizeInput($data);
        // add activation url from config if not set
        $activationUrl = $this->getConfig('activationUrl');
        if (!empty($activationUrl) && empty($data['data']['activation_url'])) {
            $data['data']['activation_url'] = Router::url($activationUrl);
        }
        $signupRoles = (array)Hash::get($data, 'data.roles', $this->getConfig('defaultRoles'));
        if (!empty($signupRoles)) {
            $data['data']['roles'] = $signupRoles;
        }
        $errors = $this->validate($data['data']);
        if (!empty($errors)) {
            throw new BadRequestException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => $errors,
            ]);
        }

        // operations are not in transaction because AsyncJobs could use a different connection
        $user = $this->createUser($data['data']);
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

            $validator
                ->requirePresence('username')
                ->notEmptyString('username');
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

        if (!empty($this->getConfig('roles'))) {
            $validator->requirePresence('roles');
        }

        $validator->add('roles', 'validateRoles', [
            'rule' => [$this, 'validateRoles'],
        ]);

        return $validator->validate($data);
    }

    /**
     * Validate roles against allowed signup roles configured.
     * In addtion roles can't contain ADMIN_ROLE.
     *
     * @param string|array $roles The roles to check
     * @return true|string
     */
    public function validateRoles($roles)
    {
        $allowedRoles = (array)$this->getConfig('roles');
        if (empty($allowedRoles) && !empty($roles)) {
            return __d('bedita', 'Roles are not allowed on signup');
        }

        $adminRoleName = $this->Roles->get(RolesTable::ADMIN_ROLE)->get('name');

        $roles = (array)$roles;
        $message = '{0} not allowed on signup';
        if (in_array($adminRoleName, $roles)) {
            return __d('bedita', $message, [$adminRoleName]);
        }

        $valid = array_intersect($roles, $allowedRoles);
        if (empty(array_diff($roles, $valid))) {
            return true;
        }

        $rolesNotAllowed = array_diff($roles, $allowedRoles);

        return __d('bedita', $message, [implode(', ', $rolesNotAllowed)]);
    }

    /**
     * Create a new user with status:
     *  - `on` if an external auth provider is used or no activation
     *    is required via `requireActivation` config
     *  - `draft` in other cases.
     *
     * The user is validated using 'signup' validation.
     *
     * @param array $data The data to save
     * @return \BEdita\Core\Model\Entity\User|bool User created or `false` on error
     * @throws \Cake\Http\Exception\UnauthorizedException Upon external authorization check failure.
     */
    protected function createUser(array $data)
    {
        if (!LoggedUser::getUser()) {
            LoggedUser::setUser(['id' => 1]);
        }

        $status = 'draft';
        if ($this->getConfig('requireActivation') === false) {
            $status = 'on';
        }

        if (empty($data['auth_provider'])) {
            return $this->createUserEntity($data, $status, 'signup');
        }

        $authProvider = $this->checkExternalAuth($data);

        $user = $this->createUserEntity($data, 'on', 'signupExternal', true);

        // create `ExternalAuth` entry
        $this->Users->dispatchEvent('Auth.externalAuth', [
            'authProvider' => $authProvider,
            'providerUsername' => $data['provider_username'],
            'userId' => $user->get('id'),
            'params' => empty($data['provider_userdata']) ? null : $data['provider_userdata'],
        ]);

        return $user;
    }

    /**
     * Create User entity.
     *
     * @param array $data The signup data
     * @param string $status User `status`, `on` or `draft`
     * @param string $validate Validation options to use
     * @param bool $verified Add `verified` value to entity
     *
     * @return \BEdita\Core\Model\Entity\User The User entity created
     * @throws \Cake\Http\Exception\BadRequestException When some data is invalid.
     */
    protected function createUserEntity(array $data, $status, $validate, bool $verified = false)
    {
        if ($this->Users->exists(['username' => $data['username']])) {
            $this->dispatchEvent('Auth.signupUserExists', [$data], $this->Users);
            throw new BadRequestException([
                'title' => __d('bedita', 'User "{0}" already registered', $data['username']),
                'code' => self::BE_USER_EXISTS,
            ]);
        }
        $action = new SaveEntityAction(['table' => $this->Users]);

        $data['status'] = $status;
        $entity = $this->Users->newEntity();
        if ($verified === true) {
            $entity->set('verified', Time::now());
        }
        $entityOptions = compact('validate');

        return $action(compact('entity', 'data', 'entityOptions'));
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
     * @return \BEdita\Core\Model\Entity\AuthProvider AuthProvider entity
     * @throws \Cake\Http\Exception\UnauthorizedException Upon external authorization check failure.
     */
    protected function checkExternalAuth(array $data)
    {
        /** @var \BEdita\Core\Model\Entity\AuthProvider $authProvider */
        $authProvider = TableRegistry::getTableLocator()->get('AuthProviders')->find('enabled')
            ->where(['name' => $data['auth_provider']])
            ->first();
        if (empty($authProvider)) {
            throw new UnauthorizedException(__d('bedita', 'External auth provider not found'));
        }
        $options = (array)Hash::get((array)$authProvider->get('params'), 'options');
        $providerResponse = $this->getOAuth2Response(
            $authProvider->get('url'),
            $data['access_token'],
            $options
        );
        if (!$authProvider->checkAuthorization($providerResponse, $data['provider_username'])) {
            throw new UnauthorizedException(__d('bedita', 'External auth failed'));
        }

        return $authProvider;
    }

    /**
     * Get response from an OAuth2 provider
     *
     * @param string $url OAuth2 provider URL
     * @param string $accessToken Access token to use in request
     * @param array $options OAuth2 request options
     * @return array Response from an OAuth2 provider
     * @codeCoverageIgnore
     */
    protected function getOAuth2Response(string $url, string $accessToken, array $options = []): array
    {
        return (new OAuth2())->response($url, $accessToken, $options);
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
        $signupRoles = Hash::get($data, 'roles');
        if (empty($signupRoles)) {
            return;
        }
        $roles = $this->loadRoles($signupRoles);

        /** @var \Cake\ORM\Association\BelongsToMany $association */
        $association = $this->Users->associations()->getByProperty('roles');
        $association->link($entity, $roles);
    }

    /**
     * Load requested roles.
     *
     * @param array $roles Requested role names
     * @return \BEdita\Core\Model\Entity\Role[] requested role entities
     */
    protected function loadRoles(array $roles)
    {
        return $this->Roles->find()
            ->where(['name IN' => $roles])
            ->toList();
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
        if (empty($user->get('email'))) {
            return;
        }
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
