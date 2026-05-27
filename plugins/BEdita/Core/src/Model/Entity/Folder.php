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

use BEdita\Core\Model\Behavior\AdjacencyListBehavior;
use BEdita\Core\Model\Table\RolesTable;
use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Folder Entity
 *
 * @property int $parent_id
 * @property string $path
 * @property array $slug_path
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
        $this->setVirtual(['path', 'slug_path'], true);
        $this->setAccess(['path', 'slug_path'], false);
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
        $Trees = TableRegistry::getTableLocator()->get('Trees');
        /** @var \BEdita\Core\Model\Entity\Tree|null $node */
        $node = $Trees->find()->where(['object_id' => $this->id])->first();
        if ($node === null) {
            return [];
        }

        $query = $Trees->find('ancestors', ['for' => $node->id]);
        $level = $Trees->getAssociation('Descendants')->junction()->aliasField(AdjacencyListBehavior::CTE_FIELD_LEVEL);
        $permission = $query
            ->disableHydration()
            ->innerJoinWith('Objects.Permissions.Roles')
            ->select([
                'level' => $level,
                'name' => 'Roles.name',
            ])
            ->orderAsc($level)
            ->toArray();

        if (empty($permission)) {
            return [];
        }

        $roles = current(Hash::combine($permission, '{n}.name', '{n}.name', '{n}.level'));

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
        /** @var \BEdita\Core\Model\Entity\Tree|null $node */
        $node = $Trees->find()->where(['object_id' => $this->id])->first();
        if ($node === null) {
            return false;
        }

        $descendantPermitted = $Trees->find('descendants', ['for' => $node->id])
            ->disableHydration()
            ->innerJoinWith(
                'Objects.Permissions',
                fn (Query $q): Query => $q->where(['Permissions.role_id IN' => $roleIds]),
            )
            ->select(['existing' => 1])
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
                ->all()
                ->toArray();
        } catch (RecordNotFoundException $previous) {
            throw new \RuntimeException(__d('bedita', 'Folder "{0}" is not on the tree.', $this->id), 0, $previous);
        }

        return sprintf('/%s', implode('/', $path));
    }

    /**
     * Getter for `slugPath` virtual property.
     *
     * @return array|null
     * @throws \RuntimeException If folder is not found on tree.
     */
    protected function _getSlugPath()
    {
        if (!$this->has('id')) {
            return null;
        }

        try {
            $Trees = TableRegistry::getTableLocator()->get('Trees');

            return $Trees->find('pathNodes', [$this->id])
                ->select([
                    'id' => $Trees->aliasField('object_id'),
                    $Trees->aliasField('menu'),
                    $Trees->aliasField('params'),
                    $Trees->aliasField('slug'),
                ], true)
                ->disableHydration()
                ->toArray();
        } catch (RecordNotFoundException $previous) {
            throw new \RuntimeException(__d('bedita', 'Folder "{0}" is not on the tree.', $this->id), 0, $previous);
        }
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
