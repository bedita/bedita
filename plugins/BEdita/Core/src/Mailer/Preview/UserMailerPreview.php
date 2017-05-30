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

namespace BEdita\Core\Mailer\Preview;

use Cake\Utility\Text;
use DebugKit\Mailer\MailPreview;

/**
 * Mail preview for UserMailer
 *
 * @property \BEdita\Core\Model\Table\UsersTable $Users
 *
 * @since 4.0.0
 * @codeCoverageIgnore
 */
class UserMailerPreview extends MailPreview
{
    /**
     * Preview of Welcome message.
     *
     * @return \Cake\Mailer\Email
     */
    public function welcome()
    {
        $user = $this->getUser();
        $options = [
            'params' => compact('user'),
        ];

        /* @var \BEdita\Core\Mailer\UserMailer $mailer */
        $mailer = $this->getMailer('BEdita/Core.User');

        return $mailer->welcome($options);
    }

    /**
     * Preview of Signup message.
     *
     * @return \Cake\Mailer\Email
     */
    public function signup()
    {
        $options = [
            'params' => [
                'user' => $this->getUser(),
                'changeUrl' => 'https://example.org/activate?code=' . Text::uuid(),
            ],
        ];

        /* @var \BEdita\Core\Mailer\UserMailer $mailer */
        $mailer = $this->getMailer('BEdita/Core.User');

        return $mailer->signup($options);
    }

    /**
     * Preview of Password recovery message.
     *
     * @return \Cake\Mailer\Email
     */
    public function changeRequest()
    {
        $options = [
            'params' => [
                'user' => $this->getUser(),
                'changeUrl' => 'https://example.org/recover?code=' . Text::uuid(),
            ],
        ];

        /* @var \BEdita\Core\Mailer\UserMailer $mailer */
        $mailer = $this->getMailer('BEdita/Core.User');

        return $mailer->changeRequest($options);
    }

    /**
     * Create mock user.
     *
     * @return \BEdita\Core\Model\Entity\User
     */
    protected function getUser()
    {
        $this->loadModel('Users');

        $user = $this->Users->newEntity([
            'name' => 'Gustavo',
            'surname' => 'Supporto',
            'title' => 'Gustavo Supporto',
            'email' => 'gustavo.supporto@example.org',
            'username' => 'gustavo_supporto',
        ]);
        $user->type = 'users';

        return $user;
    }
}
