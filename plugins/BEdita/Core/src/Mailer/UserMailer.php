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

use Cake\Mailer\Mailer;

/**
 * Mailer class to send notifications to users
 * [Temporary dummy implementation]
 *
 * @since 4.0.0
 */
class UserMailer extends Mailer
{
    /**
     * Welcome message
     *
     * @param array $options Email options: 'to' (recipient)
     * @return void
     * @codeCoverageIgnore
     */
    public function welcome($options)
    {
        $this
            ->setTemplate('BEdita/Core.welcome')
            ->setLayout('BEdita/Core.default')
            ->setTo($options['to'])
            ->setSubject('Welcome!');
    }
}
