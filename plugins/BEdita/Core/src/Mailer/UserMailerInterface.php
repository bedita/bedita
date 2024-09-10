<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Mailer;

use Cake\Mailer\Mailer;

/**
 * Mailer class to send notifications to users
 *
 * @since 5.28.0
 */
interface UserMailerInterface
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
    public function welcome($options): Mailer;

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
    public function signup(array $options): Mailer;

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
    public function changeRequest(array $options): Mailer;
}
