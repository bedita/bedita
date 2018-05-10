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

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Folder Entity
 *
 * @property int $parent_id
 * @property \BEdita\Core\Model\Entity\Folder|null $parent
 * @property string $path
 *
 * @since 4.0.0
 */
class Folder extends ObjectEntity
{
    /**
     * {@inheritDoc}
     */
    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);

        $this->setAccess('parents', false);
        $this->setHidden(['parents', 'tree_parent_nodes'], true);
        $this->setVirtual(['path'], true);
        $this->setAccess('path', false);
    }

    /**
     * Getter for `parent` virtual property
     *
     * @return \BEdita\Core\Model\Entity\Folder|null
     */
    protected function _getParent()
    {
        return Hash::get((array)$this->parents, '0');
    }

    /**
     * Setter for `parent` virtual property.
     * Set `parents` property too.
     *
     * @param \BEdita\Core\Model\Entity\Folder|null $folder The folder entity to set as parent
     * @return \BEdita\Core\Model\Entity\Folder|null
     */
    protected function _setParent(Folder $folder = null)
    {
        if ($folder === null) {
            $this->parents = [];

            return null;
        }

        $this->parents = [$folder];

        return $folder;
    }

    /**
     * Getter for `parent_id` virtual property
     *
     * @return int|null
     */
    protected function _getParentId()
    {
        return Hash::get((array)$this->parents, '0.id');
    }

    /**
     * Setter for `parent_id` virtual property.
     *
     * @param int|null $parentId The parent id to set
     * @return int|null
     */
    protected function _setParentId($parentId)
    {
        if ($parentId === null) {
            $this->parent = null;

            return null;
        }

        $table = TableRegistry::get($this->getSource());
        $this->parent = $table
            ->find()
            ->where([
                $table->aliasField('id') => $parentId,
            ])
            ->firstOrFail();

        return $parentId;
    }

    /**
     * Getter for `path` virtual property
     *
     * @return string|null
     * @throws \RuntimeException If Folder is not found on tree.
     */
    protected function _getPath()
    {
        if (!$this->has('id')) {
            return null;
        }

        $trees = TableRegistry::get('Trees');
        try {
            $node = $trees->find()
                ->where(['object_id' => $this->id])
                ->firstOrFail();
        } catch (RecordNotFoundException $previous) {
            throw new \RuntimeException(__d('bedita', 'Folder "{0}" is not on the tree.', $this->id), 0, $previous);
        }

        $path = $trees->find('list', [
                'keyField' => 'id',
                'valueField' => 'object_id',
            ])
            ->where([
                'tree_left <=' => $node->get('tree_left'),
                'tree_right >=' => $node->get('tree_right'),
            ])
            ->order(['tree_left' => 'ASC']);

        return sprintf('/%s', implode('/', $path->toArray()));
    }

    /**
     * Check if `parents` property is set
     *
     * @return bool
     */
    public function isParentSet()
    {
        return array_key_exists('parents', $this->_properties);
    }

    /**
     * {@inheritDoc}
     */
    protected static function listAssociations(Table $Table, array $hidden = [])
    {
        $relationships = parent::listAssociations($Table, $hidden);
        $relationships[] = 'parent';

        return $relationships;
    }
}
