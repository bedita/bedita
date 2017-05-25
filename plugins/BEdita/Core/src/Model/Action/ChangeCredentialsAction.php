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

use Cake\I18n\Time;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Command to change user access credentials (password)
 *
 * @since 4.0.0
 */
class ChangeCredentialsAction extends BaseAction
{

    /**
     * The UsersTable table
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    protected $Users = null;

    /**
     * The AsynJobs table
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected $AsyncJobs = null;

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    protected function initialize(array $config)
    {
        $this->Users = TableRegistry::get('Users');
        $this->AsyncJobs = TableRegistry::get('AsyncJobs');
    }

    /**
     * Validate input data.
     *
     * @param array $data Input
     * @return array|true Array of validation errors, or true if input is valid.
     */
    public function validate(array $data)
    {
        $validator = (new Validator())
            ->notEmpty('token')
            ->requirePresence('token')

            ->notEmpty('password')
            ->requirePresence('password');

        $errors = $validator->errors($data);
        if (empty($errors)) {
            return true;
        }

        return $errors;
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

        $asyncJob = $this->AsyncJobs->find()
                ->where([
                    'uuid' => $data['token'],
                    'completed IS NULL',
                ])
                ->firstOrFail();

        if (empty($asyncJob->payload['user_id'])) {
            throw new \LogicException(__d('bedita', 'Parameter "{0}" missing', ['payload.user_id']));
        }

        $user = $this->Users->get($asyncJob->payload['user_id']);

        $user->password_hash = $data['password'];
        $this->Users->saveOrFail($user);

        $asyncJob->completed = new Time();
        $this->AsyncJobs->save($asyncJob);

        return $user;
    }
}
