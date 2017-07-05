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

namespace BEdita\Core\Utility;

use Cake\Console\Exception\StopException;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Utility operations on objects mainly for seeding and shell commands.
 * These operations are only available on CLI with 'debug' activated.
 * *DON'T* use these methods in an API response: various important checks on permissions,
 * users and applications are missing.
 *
 * @internal
 */
class ObjectsHandler
{

    /**
     * Enable or disable operations - only
     * Only on CLI with 'debug' class methods will be enabled.
     *
     * @return void
     * @throws \Cake\Console\Exception\StopException
     */
    protected static function checkEnvironment()
    {
        $isCli = PHP_SAPI === 'cli';
        $debug = Configure::read('debug');
        if (!($isCli && $debug)) {
            $detail = 'Operation avilable only in CLI environment in "debug" mode';
            Log::write('error', $detail);
            throw new StopException(['title' => 'Not available',
                'detail' => $detail]);
        }
    }

    /**
     * Save an object of type $type from $data array
     * Input data array is of the form
     * ['field1' => 'value1', 'field2' => 'value2']
     * User data must contain at least user 'id'.
     * If user data is missing current user is used if present
     * or sytem user (with 'id' = 1)
     *
     * If $data['id'] is set, corresponding object is updated.
     * On missing $data['id'] a new object is created.
     *
     * @param string|int $type Object type name or id
     * @param array $data Input data array
     * @param array $user User performing action data
     * @throws \Cake\Console\Exception\StopException
     * @return \Cake\Datasource\EntityInterface|bool Entity saved or false on error
     */
    public static function save($type, $data, $user = [])
    {
        static::checkEnvironment();
        $currentUser = LoggedUser::getUser();
        if (empty($user)) {
            $user = empty($currentUser) ? ['id' => 1] : $currentUser;
        }
        LoggedUser::setUser($user);

        $objectType = TableRegistry::get('ObjectTypes')->get($type);
        $table = TableRegistry::get($objectType->model);
        if (!empty($data['id'])) {
            $entity = $table->get($data['id']);
        } else {
            $entity = $table->newEntity();
        }
        $entity = $table->patchEntity($entity, $data);
        $entity->set('type', $objectType->name);
        $saveResult = $table->save($entity);
        if (!$saveResult) {
            Log::write('error', 'Object creation failed  - ' . $type . ' - ' . json_encode($entity->errors()));
            throw new StopException(['title' => 'Invalid data', 'detail' => [$entity->errors()]]);
        }

        // restore current user
        LoggedUser::setUser($currentUser);

        return $saveResult;
    }

    /**
     * COMPLETELY and IRREVOCABLY remove an object from the database.
     *
     * @param int $id Object to remove id
     * @throws \Cake\Console\Exception\StopException
     * @return bool success or failure
     */
    public static function remove($id)
    {
        static::checkEnvironment();
        $objectsTable = TableRegistry::get('Objects');
        $entity = $objectsTable->get($id);

        return $objectsTable->delete($entity);
    }
}
