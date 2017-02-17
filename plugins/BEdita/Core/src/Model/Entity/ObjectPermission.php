<?php
namespace BEdita\Core\Model\Entity;

use Cake\ORM\Entity;

/**
 * ObjectPermission Entity
 *
 * @property int $id
 * @property int $object_id
 * @property int $role_id
 * @property string $params
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 * @property \BEdita\Core\Model\Entity\Role $role
 */
class ObjectPermission extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
