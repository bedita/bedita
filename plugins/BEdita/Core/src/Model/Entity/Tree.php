<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Entity;

use BEdita\Core\Model\Entity\Folder;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Tree Entity
 *
 * @property int $id
 * @property int $object_id
 * @property int|null $parent_id
 * @property int $root_id
 * @property int|null $parent_node_id
 * @property int $tree_left
 * @property int $tree_right
 * @property int $depth_level
 * @property bool $menu
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 * @property \BEdita\Core\Model\Entity\ObjectEntity $parent_object
 * @property \BEdita\Core\Model\Entity\ObjectEntity $root_object
 * @property \BEdita\Core\Model\Entity\Tree $parent_node
 * @property \BEdita\Core\Model\Entity\Tree[] $child_nodes
 *
 * @since 4.0.0
 */
class Tree extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => false,
        'object_id' => true,
        'parent_id' => true,
        'menu' => true,
    ];

    /**
     * Setter for `parent_id`.
     * It also set `root_id` and `parent_node_id`.
     *
     * When `$parent_id` is null it means that the node is a root (first level on tree) so
     * - the `root_id` is set equal to `object_id`
     * - the parent_node_id is set to `null`
     *
     * When `$parent_id` is not null the parent node is retrieved and
     * - the `root_id` is set equal to parent node `root_id`
     * - the `parent_node_id` is set to the parent node `id`
     *
     * @param int|null $parentId The value to set
     * @return int|null
     */
    protected function _setParentId($parentId)
    {
        if (array_key_exists('parent_id', $this->_properties) && $this->_properties['parent_id'] === $parentId) {
            return $parentId;
        }

        if ($parentId === null) {
            $this->root_id = $this->object_id;
            $this->parent_node_id = null;

            return $parentId;
        }

        // set root_id and parent_node_id
        $table = TableRegistry::get($this->getSource());
        $parentNode = $table
            ->find()
            ->where(['object_id' => $parentId])
            ->firstOrFail();

        $this->root_id = $parentNode->root_id;
        $this->parent_node_id = $parentNode->id;

        return $parentId;
    }

    /**
     * Setter for `parent_object` property.
     *
     * Set the related `parent_id` if changed.
     * In this way `parent_node_id` will be marked as dirty (see `self::_setParentId()`)
     * ensuring the recover of the tree nested set model.
     *
     * @param \BEdita\Core\Model\Entity\Folder|null $folder The folder entity to set as parent
     * @return \BEdita\Core\Model\Entity\Folder|null
     */
    protected function _setParentObject(Folder $folder = null)
    {
        $parentId = ($folder === null) ? null : $folder->id;

        if ($this->parent_id !== $parentId) {
            $this->parent_id = $parentId;
        }

        return $folder;
    }
}
