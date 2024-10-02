<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Table\RolesTable;
use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Folder Entity
 *
 * @property int $parent_id
 * @property string $path
 *
 * @property \BEdita\Core\Model\Entity\Folder|null $parent
 * @property \BEdita\Core\Model\Entity\ObjectEntity[] $children
 * @since 4.0.0
 */
class Folder extends ObjectEntity
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
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
     * {@inheritDoc}
     *
     * If $roles is an empty array try to look for inherited permissions.
     */
    protected function _getPerms(): ?array
    {
        $roles = parent::_getPerms();
        if (is_array($roles) && empty($roles)) {
            $roles = $this->getInheritedRolesPermissions();
        }
        if (is_array($roles) && !empty($roles)) {
            $roles = Hash::insert($roles, 'descendant_perms_granted', $this->descendantHavePermissions());
        }

        return $roles;
    }

    /**
     * Get inherited roles permissions.
     *
     * @return array
     */
    protected function getInheritedRolesPermissions(): array
    {
        if (empty($this->id)) {
            return [];
        }
        $Trees = TableRegistry::getTableLocator()->get('Trees');
        /** @var \BEdita\Core\Model\Entity\Tree $node */
        $node = $Trees->find()->where(['object_id' => $this->id])->first();

        $permission = $this->getTable()->Permissions
            ->find()
            ->disableHydration()
            ->select(['tree_left' => 'Trees.tree_left', 'name' => 'Roles.name'])
            ->contain('Roles')
            ->innerJoin(
                ['Trees' => 'trees'],
                [
                    'Trees.object_id = Permissions.object_id',
                    'Trees.tree_left <' => $node->tree_left,
                    'Trees.tree_right >' => $node->tree_right,
                ],
                [
                    'Trees.object_id' => 'integer',
                    'Trees.tree_left' => 'integer',
                    'Trees.tree_right' => 'integer',
                ]
            )
            ->order(['Trees.tree_left' => 'DESC'])
            ->toArray();

        if (empty($permission)) {
            return [];
        }

        $roles = current(Hash::combine($permission, '{n}.name', '{n}.name', '{n}.tree_left'));

        return ['roles' => array_values($roles), 'inherited' => true];
    }

    /**
     * Check if access to any descendant is permitted to the current user.
     *
     * @return bool
     */
    protected function descendantHavePermissions(): bool
    {
        $user = LoggedUser::getUser();
        if (empty($user)) {
            return false;
        }
        $roleIds = Hash::extract($user, 'roles.{n}.id');
        if (in_array(RolesTable::ADMIN_ROLE, $roleIds)) {
            return true;
        }

        $Trees = TableRegistry::getTableLocator()->get('Trees');
        $descendantPermitted = $Trees->selectQuery()
            ->disableHydration()
            ->select(['existing' => 1])
            ->from(['t1' => 'trees'], true)
            ->innerJoin(
                ['t2' => 'trees'],
                [
                    't2.tree_left > t1.tree_left',
                    't2.tree_right < t1.tree_right',
                ],
            )
            ->innerJoin(
                ['op' => 'object_permissions'],
                [
                    'op.object_id = t2.object_id',
                    'op.role_id IN' => Hash::extract($user, 'roles.{n}.id'),
                ],
            )
            ->where(['t1.object_id' => $this->id], ['t1.object_id' => 'integer'])
            ->limit(1)
            ->first();

        return !empty($descendantPermitted);
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
    protected function _setParent(?Folder $folder = null)
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
    protected function _getParentId(): ?int
    {
        if (empty($this->parents)) {
            return null;
        }

        return (int)Hash::get((array)$this->parents, '0.id');
    }

    /**
     * Setter for `parent_id` virtual property.
     *
     * @param int|string|null $parentId The parent id to set. Can be a numeric string
     * @return int|null
     */
    protected function _setParentId($parentId): ?int
    {
        if ($parentId === null) {
            $this->parent = null;

            return null;
        }

        $table = TableRegistry::getTableLocator()->get($this->getSource());
        $this->parent = $table
            ->find()
            ->where([
                $table->aliasField('id') => $parentId,
            ])
            ->firstOrFail();

        return $this->parent->id;
    }

    /**
     * Getter for `parent_uname` virtual property
     *
     * @return string|null
     */
    protected function _getParentUname(): ?string
    {
        if (empty($this->parents)) {
            return null;
        }

        return (string)Hash::get((array)$this->parents, '0.uname');
    }

    /**
     * Setter for `parent_uname` virtual property.
     *
     * @param string|null $parentUname The parent uname to set
     * @return string|null
     */
    protected function _setParentUname(?string $parentUname): ?string
    {
        if ($parentUname === null) {
            $this->parent = null;

            return null;
        }

        $table = TableRegistry::getTableLocator()->get($this->getSource());
        $this->parent = $table
            ->find()
            ->where([
                $table->aliasField('uname') => $parentUname,
            ])
            ->firstOrFail();

        return $parentUname;
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

        try {
            $path = TableRegistry::getTableLocator()->get('Trees')
                ->find('pathNodes', [$this->id])
                ->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'object_id',
                ])
                ->toArray();
        } catch (RecordNotFoundException $previous) {
            throw new \RuntimeException(__d('bedita', 'Folder "{0}" is not on the tree.', $this->id), 0, $previous);
        }

        return sprintf('/%s', implode('/', $path));
    }

    /**
     * Check if `parents` property is set
     *
     * @return bool
     */
    public function isParentSet()
    {
        return array_key_exists('parents', $this->_fields);
    }

    /**
     * @inheritDoc
     */
    protected static function listAssociations(Table $Table, array $hidden = [])
    {
        $relationships = parent::listAssociations($Table, $hidden);
        $relationships[] = 'parent';

        return $relationships;
    }
}
