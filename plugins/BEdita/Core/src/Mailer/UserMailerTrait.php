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

use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\Mailer\MailerAwareTrait;
use LogicException;

/**
 * Mailer class to send notifications to users
 *
 * @since 5.28.0
 */
trait UserMailerTrait
{
    use MailerAwareTrait;

    /**
     * Returns a mailer instance.
     *
     * @param string $name Mailer's name.
     * @param array<string, mixed>|string|null $config Array of configs, or profile name string.
     * @return \Cake\Mailer\Mailer
     * @throws \LogicException if mailer class doesn't implement interface.
     */
    protected function getUserMailer(): Mailer
    {
        $mailerClass = Configure::read('Mailer.User', 'BEdita/Core.User');
        $mailer = $this->getMailer($mailerClass);
        if (!$mailer instanceof UserMailerInterface) {
            throw new LogicException(sprintf('Mailer class "%s" must implement UserMailerInterface', $mailerClass));
        }

        return $mailer;
    }
}
