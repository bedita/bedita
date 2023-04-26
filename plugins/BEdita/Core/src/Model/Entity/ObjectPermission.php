<?php
namespace BEdita\Core\Model\Entity;

use BEdita\Core\Utility\JsonApiSerializable;
use Cake\ORM\Entity;

/**
 * ObjectPermission Entity
 *
 * @property int $id
 * @property int $object_id
 * @property int $role_id
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 * @property \BEdita\Core\Model\Entity\Role $role
 * @property \BEdita\Core\Model\Entity\User $created_by_user
 */
class ObjectPermission extends Entity implements JsonApiSerializable
{
    use JsonApiTrait;

    /**
     * @inheritDoc
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'created' => false,
        'created_by' => false,
    ];

    /**
     * @inheritDoc
     */
    protected $_hidden = [
        'created_by_user',
        'object',
        'role',
    ];
}
