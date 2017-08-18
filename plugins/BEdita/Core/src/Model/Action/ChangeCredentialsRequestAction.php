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

namespace BEdita\Core\Model\Action;

use BEdita\Core\Model\Entity\AsyncJob;
use BEdita\Core\Model\Entity\User;
use BEdita\Core\Model\Validation\CustomUrlValidationProvider;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\Event;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventListenerInterface;
use Cake\I18n\Time;
use Cake\Mailer\MailerAwareTrait;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Command to request an access credentials change
 * Typically a password change
 *
 * Input data MUST contain:
 *  - 'contact' user email, other contact methods in the future
 *  - 'change_url' base URL to use in email sent to user, actual change URL link will be
 *      {change_url}?uuid={uuid}
 *
 * @since 4.0.0
 */
class ChangeCredentialsRequestAction extends BaseAction implements EventListenerInterface
{

    use EventDispatcherTrait;
    use MailerAwareTrait;

    /**
     * The UsersTable table
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    protected $Users;

    /**
     * The AsyncJobs table
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected $AsyncJobs;

    /**
     * {@inheritdoc}
     */
    protected function initialize(array $config)
    {
        $this->Users = TableRegistry::get('Users');
        $this->AsyncJobs = TableRegistry::get('AsyncJobs');

        $this->eventManager()->on($this);
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $errors = $this->validate($data);
        if ($errors !== true) {
            throw new BadRequestException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => $errors,
            ]);
        }

        // operations are not in transaction: every failure stops following operations
        // and there's nothing to rollback
        $user = $this->getUser($data['contact']);
        $job = $this->createJob($user);
        $changeUrl = $this->getChangeUrl($job->uuid, $data['change_url']);

        $this->dispatchEvent('Auth.credentialsChangeRequest', [$user, $job, $changeUrl], $this->Users);

        return true;
    }

    /**
     * Validate input data.
     *
     * @param array $data Input
     * @return array|true Array of validation errors, or true if input is valid.
     */
    public function validate(array $data)
    {
        $validator = new Validator();
        $validator->setProvider('customUrl', new CustomUrlValidationProvider());

        $validator->email('contact')
            ->notEmpty('contact')
            ->requirePresence('contact')

            ->notEmpty('change_url')
            ->requirePresence('change_url')
            ->add('activation_url', 'customUrl', [
                'rule' => 'isValidUrl',
                'provider' => 'customUrl',
            ]);

        $errors = $validator->errors($data);
        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    /**
     * Get user.
     *
     * @param string $contact Contact method (email).
     * @return \BEdita\Core\Model\Entity\User
     */
    protected function getUser($contact)
    {
        $user = $this->Users->find()
            ->where(function (QueryExpression $exp) use ($contact) {
                return $exp->eq($this->Users->aliasField('email'), $contact);
            })
            ->firstOrFail();

        return $user;
    }

    /**
     * Create the credentials change async job
     *
     * @param User $user The user requesting change
     * @return \BEdita\Core\Model\Entity\AsyncJob
     */
    protected function createJob(User $user)
    {
        $asyncJobsTable = TableRegistry::get('AsyncJobs');
        $action = new SaveEntityAction(['table' => $asyncJobsTable]);

        return $action([
            'entity' => $asyncJobsTable->newEntity(),
            'data' => [
                'service' => 'credentials_change',
                'payload' => [
                    'user_id' => $user->id,
                ],
                'scheduled_from' => new Time('1 day'),
                'priority' => 1,
            ]
        ]);
    }

    /**
     * Send change request email to user
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \BEdita\Core\Model\Entity\User $user The user.
     * @param \BEdita\Core\Model\Entity\AsyncJob $asyncJob Async job.
     * @param string $changeUrl Change URL
     * @return void
     */
    public function sendMail(Event $event, User $user, AsyncJob $asyncJob, $changeUrl)
    {
        $options = [
            'params' => compact('user', 'changeUrl'),
        ];
        $this->getMailer('BEdita/Core.User')->send('changeRequest', [$options]);
    }

    /**
     * Return the credentials change url
     *
     * @param string $uuid Change uuid
     * @param string $changeUrl Base change URL
     * @return string
     */
    protected function getChangeUrl($uuid, $changeUrl)
    {
        $changeUrl .= (strpos($changeUrl, '?') === false) ? '?' : '&';

        return sprintf('%suuid=%s', $changeUrl, $uuid);
    }

    /**
     * {@inheritdoc}
     */
    public function implementedEvents()
    {
        return [
            'Auth.credentialsChangeRequest' => 'sendMail',
        ];
    }
}
