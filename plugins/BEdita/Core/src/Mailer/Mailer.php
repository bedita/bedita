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

use BadMethodCallException;
use Cake\Mailer\Mailer as CakeMailer;

/**
 * Mailer class to send serialized emails.
 *
 * This class extends the CakePHP's core {@see Cake\Mailer\Mailer} class by adding a {@see self::sendRaw()}
 * method to send raw emails, after the object has been unserialized.
 *
 * @since 6.0.0
 */
class Mailer extends CakeMailer
{
    /**
     * Send a raw email.
     *
     * This method assumes that `_message`, `_textMessage` and `_htmlMessage` private attributes
     * have already been set somehow, for instance after calling `createFromArray()`.
     *
     * @return array
     */
    public function sendRaw(): array
    {
        if (empty($this->message->getFrom())) {
            throw new BadMethodCallException('From is not specified.');
        }
        if (empty($this->message->getTo()) && empty($this->message->getCc()) && empty($this->message->getBcc())) {
            throw new BadMethodCallException('You need specify one destination on to, cc or bcc.');
        }

        $contents = $this->getTransport()->send($this->message);
        $this->logDelivery($contents);

        return $contents;
    }
}
