<?php
namespace BEdita\Core\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tree Entity
 *
 * @property int $id
 * @property int $object_id
 * @property int $parent_id
 * @property int $root_id
 * @property int $parent_node_id
 * @property int $tree_left
 * @property int $tree_right
 * @property int $depth_level
 * @property int $menu
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 * @property \BEdita\Core\Model\Entity\ObjectEntity $parent_object
 * @property \BEdita\Core\Model\Entity\ObjectEntity $root_object
 * @property \BEdita\Core\Model\Entity\Tree $parent_node
 * @property \BEdita\Core\Model\Entity\Tree[] $child_nodes
 */
class Tree extends Entity
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
