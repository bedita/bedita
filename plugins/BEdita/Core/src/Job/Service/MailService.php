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

namespace BEdita\Core\Job\Service;

use BEdita\Core\Job\JobService;
use BEdita\Core\Mailer\Email;
use Cake\Utility\Hash;

/**
 * Service to send single mail
 *
 * @since 4.0.0
 */
class MailService implements JobService
{

    /**
     * Send a single email.
     *
     * Payload **MUST** contain a set of options compatible with {@see \Cake\Mailer\Email::createFromArray()}.
     *
     * Options can contain the following keys:
     *  - `transport`: mail transport to use (default: `'default'`).
     *
     * @param array $payload Input data for this email job.
     * @param array $options Options for running this job.
     * @return array Result from {@see \Cake\Mailer\Email::send()}.
     */
    public function run(array $payload, array $options = [])
    {
        $transport = Hash::get($options, 'transport', 'default');

        $email = (new Email())
            ->createFromArray($payload)
            ->setTransport($transport);

        return $email->sendRaw();
    }
}
