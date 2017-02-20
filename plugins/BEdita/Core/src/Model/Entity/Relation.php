<?php
namespace BEdita\Core\Model\Entity;

use Cake\ORM\Entity;

/**
 * Relation Entity
 *
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string $inverse_name
 * @property string $inverse_label
 * @property string $description
 * @property string $params
 *
 * @property \BEdita\Core\Model\Entity\ObjectRelation[] $object_relations
 * @property \BEdita\Core\Model\Entity\RelationType[] $relation_types
 */
class Relation extends Entity
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
