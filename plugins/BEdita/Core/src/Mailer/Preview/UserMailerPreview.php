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

use DebugKit\Mailer\MailPreview;

/**
 * Mail preview for UserMailer
 *
 * @since 4.0.0
 * @codeCoverageIgnore
 */
class UserMailerPreview extends MailPreview
{
    /**
     * Preview of Welcome message.
     *
     * @return \BEdita\Core\Mailer\UserMailer
     */
    public function welcome()
    {
        $user = $this->getUser();

        $options = [
            'params' => [
                'user' => $user
            ]
        ];

        return $this->getMailer('BEdita/Core.User')
            ->welcome($options);
    }

    /**
     * Preview of Signup message.
     *
     * @return \BEdita\Core\Mailer\UserMailer
     */
    public function signup()
    {
        $user = $this->getUser();

        $options = [
            'params' => [
                'user' => $user,
                'activationUrl' => 'https://example.com'
            ]
        ];

        return $this->getMailer('BEdita/Core.User')
            ->signup($options);
    }

    /**
     * Get last user adding email if empty
     *
     * @return \BEdita\Core\Model\Entity\User
     */
    protected function getUser()
    {
        $this->loadModel('Users');
        $user = $this->Users
            ->find()
            ->order(['id' => 'DESC'])
            ->first();

        if (!$user->email) {
            $user->email = 'gustavo@bedita.com';
        }

        return $user;
    }
}
