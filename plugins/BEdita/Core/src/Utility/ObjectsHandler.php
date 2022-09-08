<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

/**
 * Utility operations on objects mainly for seeding and shell commands.
 * These operations are only available on CLI environment.
 * *DON'T* use these methods in an API response: various important checks on permissions,
 * users and applications are missing.
 *
 * @internal
 */
class ObjectsHandler
{
    /**
     * Check environment: operations allowed only in CLI.
     * Otherwise a StopException is thrown.
     *
     * @return void
     * @throws \Cake\Console\Exception\StopException
     */
    protected static function checkEnvironment(): void
    {
        if (!static::isCli()) {
            throw new StopException('Operation avilable only in CLI environment');
        }
    }

    /**
     * Check if we are in CLI environment.
     *
     * @return bool
     */
    protected static function isCli(): bool
    {
        return PHP_SAPI === 'cli';
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
     * @param string $type Object type name
     * @param array $data Input data array
     * @param array $user User performing action data
     * @return \Cake\Datasource\EntityInterface Entity saved
     */
    public static function save(string $type, array $data, array $user = []): EntityInterface
    {
        static::checkEnvironment();
        $currentUser = LoggedUser::getUser();
        if (empty($user)) {
            $user = empty($currentUser) ? ['id' => 1] : $currentUser;
        }
        LoggedUser::setUser($user);

        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get($type);
        $table = TableRegistry::getTableLocator()->get($type);
        if (!empty($data['id'])) {
            $entity = $table->get($data['id']);
        } else {
            $entity = $table->newEmptyEntity();
        }
        $options = ['accessibleFields' => ['locked' => true]];
        $entity = $table->patchEntity($entity, $data, $options);
        $entity->set('type', $objectType->name);
        $saveResult = $table->saveOrFail($entity);

        // restore current user
        LoggedUser::setUser($currentUser);

        return $saveResult;
    }

    /**
     * COMPLETELY and IRREVOCABLY remove an object from the database.
     *
     * @param int|string $id Object to remove ID or uname
     * @return bool success
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     */
    public static function remove($id): bool
    {
        static::checkEnvironment();
        $objectsTable = TableRegistry::getTableLocator()->get('Objects');
        $entity = $objectsTable->find('unameId', [$id])->firstOrFail();

        return $objectsTable->deleteOrFail($entity);
    }
}
