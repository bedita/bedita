<?php
declare(strict_types=1);

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

namespace BEdita\Core\Mailer;

use BEdita\Core\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;

/**
 * Mailer class to send notifications to users
 *
 * @since 4.0.0
 */
class UserMailer extends Mailer implements UserMailerInterface
{
    /**
     * Welcome message.
     *
     * It requires `$options['params']` with:
     * - `user` the user Entity to send welcome email
     *
     * @param array $options Email options
     * @return \Cake\Mailer\Mailer
     * @throws \LogicException When missing some required parameter
     */
    public function welcome($options): Mailer
    {
        if (empty($options['params']['user'])) {
            throw new \LogicException(__d('bedita', 'Parameter "{0}" missing', ['params.user']));
        }

        $user = $options['params']['user'];
        $this->checkUser($user);
        $projectName = $this->getProjectName();
        $subject = __d('bedita', 'Welcome to {0}', [$projectName]);

        $this->setViewVars([
            'user' => $user,
            'projectName' => $projectName,
        ]);

        $this->viewBuilder()
            ->setTemplate('BEdita/Core.welcome')
            ->setLayout('BEdita/Core.default');

        return $this
            ->setEmailFormat('both')
            ->setTo($user->email)
            ->setSubject($subject);
    }

    /**
     * Signup message.
     *
     * It requires `$options['params']` with:
     * - `user` the User Entity to send signup email
     * - `activationUrl` the activation url to follow
     *
     * @param array $options Email options
     * @return \Cake\Mailer\Mailer
     * @throws \LogicException When missing some required parameter
     */
    public function signup(array $options): Mailer
    {
        if (empty($options['params']['activationUrl'])) {
            throw new \LogicException(__d('bedita', 'Parameter "{0}" missing', ['params.activationUrl']));
        }

        if (empty($options['params']['user'])) {
            throw new \LogicException(__d('bedita', 'Parameter "{0}" missing', ['params.user']));
        }

        $user = $options['params']['user'];
        $this->checkUser($user);

        $projectName = $this->getProjectName();
        $subject = __d('bedita', '{0} registration', [$projectName]);

        $this->setViewVars([
            'user' => $user,
            'activationUrl' => $options['params']['activationUrl'],
            'projectName' => $projectName,
        ]);

        $this->viewBuilder()
            ->setTemplate('BEdita/Core.signup')
            ->setLayout('BEdita/Core.default');

        return $this->setEmailFormat('both')
            ->setTo($user->email)
            ->setSubject($subject);
    }

    /**
     * Credentials change message.
     *
     * It requires `$options['params']` with:
     * - `user` the user id to send signup email
     * - `changeUrl` the change url to follow
     *
     * @param array $options Email options
     * @return \Cake\Mailer\Mailer
     * @throws \LogicException When missing some required parameter
     */
    public function changeRequest(array $options): Mailer
    {
        if (empty($options['params']['changeUrl'])) {
            throw new \LogicException(__d('bedita', 'Parameter "{0}" missing', ['params.changeUrl']));
        }

        if (empty($options['params']['user'])) {
            throw new \LogicException(__d('bedita', 'Parameter "{0}" missing', ['params.user']));
        }

        $user = $options['params']['user'];
        $this->checkUser($user);

        $projectName = $this->getProjectName();
        $subject = __d('bedita', '{0} change request', [$projectName]);

        $this->setViewVars([
            'user' => $user,
            'changeUrl' => $options['params']['changeUrl'],
            'projectName' => $projectName,
        ]);

        $this->viewBuilder()
            ->setTemplate('BEdita/Core.change_request')
            ->setLayout('BEdita/Core.default');

        return $this->setEmailFormat('both')
            ->setTo($user->email)
            ->setSubject($subject);
    }

    /**
     * Check the user is valid.
     *
     * @param \BEdita\Core\Model\Entity\User $user The user entity
     * @return void
     * @throws \LogicException When user is not valid
     */
    protected function checkUser($user)
    {
        if (!($user instanceof User)) {
            throw new \LogicException(__d('bedita', 'Invalid user, it must be an User Entity'));
        }

        if (empty($user->email)) {
            throw new \LogicException(__d('bedita', 'User email missing'));
        }
    }

    /**
     * Get the project name.
     * It tries to get the application name (default 'BEdita')
     *
     * @return string
     */
    protected function getProjectName()
    {
        $name = Configure::read('Project.name');

        return $name ?? 'BEdita';
    }
}
