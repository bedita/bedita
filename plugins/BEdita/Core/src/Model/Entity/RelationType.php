<?php
namespace BEdita\Core\Model\Entity;

use Cake\ORM\Entity;

/**
 * RelationType Entity
 *
 * @property int $relation_id
 * @property int $object_type_id
 * @property string $side
 *
 * @property \BEdita\Core\Model\Entity\Relation $relation
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 */
class RelationType extends Entity
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
        'relation_id' => false,
        'object_type_id' => false,
        'side' => false
    ];
}
