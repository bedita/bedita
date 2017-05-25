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

namespace BEdita\Core\Mailer;

use BEdita\Core\Model\Action\GetObjectAction;
use BEdita\Core\State\CurrentApplication;
use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;

/**
 * Mailer class to send notifications to users
 *
 * @since 4.0.0
 */
class UserMailer extends Mailer
{
    /**
     * Welcome message
     *
     * @param array $options Email options: 'to' (recipient)
     * @return \Cake\Mailer\Email
     * @codeCoverageIgnore
     */
    public function welcome($options)
    {
        return $this
            ->setTemplate('BEdita/Core.welcome')
            ->setLayout('BEdita/Core.default')
            ->setTo($options['to'])
            ->setSubject('Welcome!');
    }

    /**
     * Signup message.
     *
     * It requires `$options['params']` with:
     * - `userId` the user id to send signup email
     * - `activationUrl` the activation url to follow
     *
     * @param array $options Email options
     * @return \Cake\Mailer\Email
     * @throws \LogicException When missing some required parameter
     */
    public function signup(array $options)
    {
        if (empty($options['params']['userId'])) {
            throw new \LogicException(__d('bedita', 'Parameter "{0}" missing', ['params.userId']));
        }

        if (empty($options['params']['activationUrl'])) {
            throw new \LogicException(__d('bedita', 'Parameter "{0}" missing', ['params.activationUrl']));
        }

        $users = TableRegistry::get('Users');
        $action = new GetObjectAction(['table' => $users]);
        $user = $action(['primaryKey' => $options['params']['userId']]);

        if (empty($user->email)) {
            throw new \LogicException(__d('bedita', 'User email missing'));
        }

        $currentApplication = CurrentApplication::getApplication();
        $appName = ($currentApplication !== null) ? $currentApplication->name : 'BEdita';
        $subject = __d('bedita', '{0} registration', [$appName]);

        $this->set([
            'user' => $user,
            'activationUrl' => $options['params']['activationUrl'],
            'appName' => $appName
        ]);

        return $this->setTemplate('BEdita/Core.signup')
            ->setLayout('BEdita/Core.default')
            ->setEmailFormat('both')
            ->setTo($user->email)
            ->setSubject($subject);
    }
}
