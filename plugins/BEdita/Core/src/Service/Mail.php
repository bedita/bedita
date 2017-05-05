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

namespace BEdita\Core\Service;

use BEdita\Core\Job\JobService;
use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\Mailer\Exception\MissingMailerException;
use Cake\Mailer\MailerAwareTrait;
use Cake\Utility\Hash;

/**
 * Service to send single mail
 *
 * @since 4.0.0
 */
class Mail implements JobService
{
    use MailerAwareTrait;

    /**
     * Email class
     *
     * @var \Cake\Mailer\Email
     */
    protected $Email;

    /**
     * Send a single email directly or using a mailer class.
     *
     * $payload array data specify mail action and input data.
     * Possible keys are:
     *  * 'profile' - Email configuration profile to use (default is 'default'), in profile sender and from fields are
     *  * 'mailer' - Custom mailer class to use, if no namespace is spesified a class with same name is searched
     *      in loaded plugins in `\MyPlugin\Mailer` namespace than in `\BEdita\Core\Mailer`
     *  * 'action' - Mailer action to trigger - mandatory if 'mailer' is specified
     *  * 'params' - Optional additional parameters to pass to mailer
     *  * 'to' - Email recipient, mandatory if no 'mailer' is specified
     *  * 'subject' - Email subject, mandatory if no 'mailer' is specified
     *  * 'message' - Message body, mandatory if no 'mailer' is specified
     *
     * In $options array an alternative 'profile' name may be set.
     *
     * @param array $payload Input data for this email job.
     * @param array $options Options for running this job.
     * @return bool True on success, false on failure
     */
    public function run($payload, $options = [])
    {
        // look for 'profile' in $payload, overridable in $options
        $profile = Hash::get($payload, 'profile', 'default');
        $profile = Hash::get($options, 'profile', $profile);

        $this->Email = new Email($profile);
        if (!empty($payload['mailer'])) {
            if (empty($payload['action'])) {
                throw new \LogicException(__d('bedita', 'Mailer action missing'));
            }
            $mailerOptions = array_intersect_key($payload, array_flip(['params', 'to', 'subject', 'message']));

            return $this->mailerSend($payload['mailer'], $payload['action'], $mailerOptions);
        }

        $mandatory = ['to', 'subject', 'message'];
        $mailOptions = array_intersect_key($payload, array_flip($mandatory));
        if (count($mailOptions) < count($mandatory)) {
            throw new \LogicException(__d('bedita', 'Mandatory parameter missing (to, subject, message)'));
        }
        $this->Email->setTo($mailOptions['to'])
            ->setSubject($mailOptions['subject'])
            ->send($mailOptions['message']);

        return true;
    }

    /**
     * Send mail using mailer.
     * If a plugin syntax is used for $name mailer is loaded directly.
     * Otherwise a matching mailer is searched in plugins and core.
     *
     * @param string $name Mailer name
     * @param string $action Mailer action
     * @param array $options Mailer options
     * @return bool True on success, false on failure
     */
    protected function mailerSend($name, $action, $options)
    {
        // if plugin syntax is used load mailer directly
        if (strpos($name, '.')) {
            $this->getMailer($name, $this->Email)->send($action, [$options]);

            return true;
        }

        $plugins = array_keys((array)Configure::read('Plugins'));
        $plugins[] = 'BEdita/Core';
        $mailer = null;
        foreach ($plugins as $plugin) {
            try {
                $mailer = $this->getMailer("$plugin.$name", $this->Email);
            } catch (MissingMailerException $x) {
            }
            if ($mailer) {
                break;
            }
        }
        if (empty($mailer)) {
            throw new \LogicException(__d('bedita', 'Mailer not found "{0}"', [$name]));
        }

        $mailer->send($action, [$options]);

        return true;
    }
}
