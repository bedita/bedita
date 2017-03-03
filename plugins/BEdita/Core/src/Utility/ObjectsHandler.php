<?php
namespace BEdita\Core\Utility;

use Cake\Console\Exception\StopException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Migrations\AbstractSeed;

/**
 * Utility operations on objects mainly for seeding and shell commands.
 * DON'T use these methods in an API response: various importan checks like permissions, current user and application...
 */
class ObjectsHandler
{
    /**
     * Create a new object of type $type from $data array
     * Input data array is of the form
     * ['field1' => 'value1', 'field2' => 'value2']
     * User data must contain at least user 'id'.
     * If user data is missing current user is used if present
     * or sytem user (with 'id' = 1)
     *
     * @param string|int $type Object type name or id
     * @param array $data Input data array
     * @param array $user User performing action data
     * @return \Cake\Datasource\EntityInterface|bool Entity created or false on error
     */
    public static function create($type, $data, $user = [])
    {
        $currentUser = LoggedUser::getUser();
        if (empty($user)) {
            $user = empty($currentUser) ? ['id' => 1] : $currentUser;
        }
        LoggedUser::setUser($user);

        $objectType = TableRegistry::get('ObjectTypes')->get($type);
        $table = TableRegistry::get($objectType->model);
        $entity = $table->newEntity($data);
        $entity->type = $objectType->name;
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
     * @return bool success or failure
     */
    public static function remove($id)
    {
        $objectsTable = TableRegistry::get('Objects');
        $entity = $objectsTable->get($id);

        return $objectsTable->delete($entity);
    }
}
