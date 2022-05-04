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

use Cake\Mailer\Email as CakeEmail;

/**
 * Email class to send serialized emails.
 *
 * This class extends the CakePHP's core {@see Cake\Mailer\Email} class by adding a {@see self::sendRaw()}
 * method to send raw emails, after the object has been unserialized.
 *
 * @since 4.0.0
 */
class Email extends CakeEmail
{
    /**
     * Send a raw email.
     *
     * This method assumes that `_message`, `_textMessage` and `_htmlMessage` private attributes
     * have already been set somehow, for instance after calling `createFromArray()`.
     *
     * @return array
     */
    public function sendRaw()
    {
        if (empty($this->message->getFrom())) {
            throw new \BadMethodCallException('From is not specified.');
        }
        if (empty($this->message->getTo()) && empty($this->message->getCc()) && empty($this->message->getBcc())) {
            throw new \BadMethodCallException('You need specify one destination on to, cc or bcc.');
        }

        $transport = $this->getTransport();
        if (!$transport) {
            $msg = 'Cannot send email, transport was not defined. Did you call transport() or define ' .
                'a transport in the set profile?';
            throw new \BadMethodCallException($msg);
        }
        $contents = $transport->send($this->message);
        $this->_logDelivery($contents);

        return $contents;
    }

    /**
     * Get boundary used by a mail.
     *
     * @param \Cake\Mailer\Email $email Email instance.
     * @return string|null
     */
    // public static function getBoundary(CakeEmail $email)
    // {
    //     return $email->getMessage()->getBou;
    // }
}
