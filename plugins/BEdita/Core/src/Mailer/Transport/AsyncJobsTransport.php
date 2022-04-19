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

namespace BEdita\Core\Mailer\Transport;

use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Message;
use Cake\Mailer\Transport\DebugTransport;
use Cake\ORM\TableRegistry;

/**
 * Asynchronous mail transport.
 *
 * This transport creates an AsyncJob instead of actually sending the email.
 *
 * @since 4.0.0
 */
class AsyncJobsTransport extends AbstractTransport
{
    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'service' => 'mail',
        'max_attempts' => 3,
    ];

    /**
     * @inheritDoc
     */
    public function send(Message $message): array
    {
        /** @var \BEdita\Core\Model\Table\AsyncJobsTable $table */
        $table = TableRegistry::getTableLocator()->get('AsyncJobs');

        $asyncJob = $table->newEntity([]);
        $asyncJob->service = $this->getConfig('service');
        $asyncJob->max_attempts = $this->getConfig('max_attempts');
        if ($this->getConfig('priority') !== null) {
            $asyncJob->priority = $this->getConfig('priority');
        }

        $payload = $message->jsonSerialize();
        // $payload += [
        //     // '_boundary' => BeditaEmail::getBoundary($email),
        //     '_message' => $message->getBodyString(),
        //     '_htmlMessage' => $message->getBodyHtml(),
        //     '_textMessage' => $message->getBodyText(),
        // ];
        // Remove unnecessary attributes from payload since templates have already been rendered
        // `viewVars` may contain objects that are "heavy" to serialize (like some entities)
        unset($payload['viewVars'], $payload['viewConfig']);
        $asyncJob->payload = $payload;

        $table->saveOrFail($asyncJob);

        return (new DebugTransport())->send($message);
    }
}
