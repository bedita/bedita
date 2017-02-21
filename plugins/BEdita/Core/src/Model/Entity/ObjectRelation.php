<?php
namespace BEdita\Core\Model\Entity;

use Cake\ORM\Entity;

/**
 * ObjectRelation Entity
 *
 * @property int $left_id
 * @property int $relation_id
 * @property int $right_id
 * @property int $priority
 * @property int $inv_priority
 * @property string $params
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 * @property \BEdita\Core\Model\Entity\Relation $relation
 */
class ObjectRelation extends Entity
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
        'left_id' => false,
        'relation_id' => false,
        'right_id' => false
    ];
}
