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
 * @property array $params
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 * @property \BEdita\Core\Model\Entity\Relation $relation
 */
class ObjectRelation extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'left_id' => false,
        'relation_id' => false,
        'right_id' => false
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'left_id',
        'right_id',
        'relation_id',
    ];
}
