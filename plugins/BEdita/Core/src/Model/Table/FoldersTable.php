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

namespace BEdita\Core\Model\Table;

use BEdita\Core\Model\Entity\Folder;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Rule\ValidCount;
use Cake\ORM\TableRegistry;

/**
 * Folders Model
 *
 * @property \Cake\ORM\Association\HasOne $Trees
 * @property \Cake\ORM\Association\BelongsToMany $Children
 *
 * @method \BEdita\Core\Model\Entity\Folder get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Folder newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Folder[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Folder|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Folder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Folder[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Folder findOrCreate($search, callable $callback = null, $options = [])
 *
 * @since 4.0.0
 *
 */
class FoldersTable extends ObjectsTable
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setEntityClass(Folder::class);

        $this->belongsToMany('Children', [
            'className' => 'Objects',
            'through' => 'Trees',
            'foreignKey' => 'parent_id',
            'targetForeignKey' => 'object_id',
            'sort' => [
                'Trees.tree_left' => 'asc',
            ],
            'cascadeCallbacks' => true,
        ]);

        $this->hasMany('TreeParentNodes', [
            'className' => 'Trees',
            'foreignKey' => 'parent_id',
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add(
            [$this, 'hasAtMostOneParent'],
            'hasAtMostOneParent',
            [
                'errorField' => 'parents',
                'message' => __d('bedita', 'Folder can have at most one existing parent.'),
            ]
        );

        $rules->add(
            [$this, 'isFolderRestorable'],
            'isFolderRestorable',
            [
                'errorField' => 'deleted',
                'message' => __d('bedita', 'Folder can be restored only if its ancestors are not deleted.'),
            ]
        );

        return $rules;
    }

    /**
     * Custom rule for checking that entity has at most one parent.
     * The check is done on `parents` property
     *
     * @param Folder $entity The folder entity to check
     * @return bool
     */
    public function hasAtMostOneParent(Folder $entity)
    {
        if (empty($entity->parents)) {
            return true;
        }

        $rule = new ValidCount('parents');

        return $rule($entity, ['operator' => '==', 'count' => 1]) && !empty($entity->parent->id);
    }

    /**
     * Custom rule to check if the folder entity is restorable
     * i.e. if its parents have not been deleted.
     *
     * If entity is new or `deleted` is not dirty (no change) or it is equal to true (delete action) then return true.
     *
     * @param Folder $entity The entity to check
     * @return bool
     */
    public function isFolderRestorable(Folder $entity)
    {
        if ($entity->isNew() || !$entity->isDirty('deleted') || $entity->deleted === true) {
            return true;
        }

        $node = $this->TreeNodes
            ->find()
            ->where([$this->TreeNodes->aliasField('object_id') => $entity->id])
            ->firstOrFail();

        $deletedParents = $this->find()
            ->innerJoinWith('TreeNodes', function (Query $query) use ($node) {
                return $query->where(function (QueryExpression $exp) use ($node) {
                    return $exp
                        ->lt($this->TreeNodes->aliasField('tree_left'), $node->get('tree_left'))
                        ->gt($this->TreeNodes->aliasField('tree_right'), $node->get('tree_right'));
                });
            })
            ->where([$this->aliasField('deleted') => true])
            ->count();

        return $deletedParents === 0;
    }

    /**
     * Set `parents` as not dirty to prevent automatic save that could breaks the tree.
     * The tree is saved later in `afterSave()`
     *
     * @param Event $event The event
     * @param EntityInterface $entity The entity to save
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        $entity->dirty('parents', false);
    }

    /**
     * Update the tree setting the right parent.
     *
     * @param Event $event The event
     * @param EntityInterface $entity The folder entity persisted
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity)
    {
        $this->updateChildrenDeletedField($entity);

        // no update on the tree
        if (!$entity->isNew() && !$entity->isParentSet()) {
            return;
        }

        $trees = TableRegistry::get('Trees');

        if ($entity->isNew()) {
            $node = $trees->newEntity([
                'object_id' => $entity->id,
                'parent_id' => $entity->parent_id,
            ]);
            $trees->saveOrFail($node);

            return;
        }

        $node = $trees->find()
            ->where(['object_id' => $entity->id])
            ->firstOrFail();

        // parent unchanged
        if ($entity->parent_id === $node->parent_id) {
            return;
        }

        $node->parent_id = $entity->parent_id;
        $trees->saveOrFail($node);
    }

    /**
     * Prepare all descendants of type "folders" in `$options` to delete later in `static::afterDelete()`.
     *
     * ### Options
     *
     * `_isDescendant` default empty. When not empty means that the deletion was cascading from a parent so no other action needed.
     *
     * @param \Cake\Event\Event $event The event
     * @param \Cake\Datasource\EntityInterface $entity The folder entity to delete
     * @param \ArrayObject $options Delete options
     * @return void
     */
    public function beforeDelete(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        if (!empty($options['_isDescendant'])) {
            return;
        }

        $options['descendants'] = $this
            ->find('ancestor', [$entity->get('id')])
            ->where([
                $this->aliasField('object_type_id') => $this->objectType()->id,
            ])
            ->toArray();
    }

    /**
     * Delete all descendants of type "folders" if exist.
     *
     * @param \Cake\Event\Event $event The event
     * @param \Cake\Datasource\EntityInterface $entity The folder entity to delete
     * @param \ArrayObject $options Delete options
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        if (empty($options['descendants'])) {
            return;
        }

        foreach ((array)$options['descendants'] as $subfolder) {
            $this->deleteOrFail($subfolder, ['_isDescendant' => true]);
        }
    }

    /**
     * Finder for root folders.
     *
     * @param Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findRoots(Query $query)
    {
        return $query
            ->innerJoinWith('TreeNodes', function (Query $query) {
                return $query->where(function (QueryExpression $exp) {
                    return $exp->isNull($this->TreeNodes->aliasField('parent_id'));
                });
            })
            ->order('TreeNodes.tree_left');
    }

    /**
     * Update the `deleted` field of children folders to parent value.
     * The update is executed only if parent folder `deleted` is dirty.
     *
     * @param Folder $folder The parent folder.
     * @return int
     */
    protected function updateChildrenDeletedField(Folder $folder)
    {
        if (!$folder->isDirty('deleted')) {
            return;
        }

        // use Trees table to build subquery and not `static::findAncestor()` custom finder because
        // the update fails on MySql when "attempts to select from and modify the same table within a single statement."
        // @see https://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html#error_er_update_table_used
        $parentNode = $this->TreeNodes
            ->find()
            ->where([$this->TreeNodes->aliasField('object_id') => $folder->id])
            ->firstOrFail();

        $descendantsToUpdate = $this->TreeNodes
            ->find()
            ->select(['object_id'])
            ->where(function (QueryExpression $exp) use ($parentNode) {
                return $exp
                    ->gt($this->TreeNodes->aliasField('tree_left'), $parentNode->get('tree_left'))
                    ->lt($this->TreeNodes->aliasField('tree_right'), $parentNode->get('tree_right'));
            });

        // Update deleted field of descendants
        return $this->updateAll(
            [
                'deleted' => $folder->deleted,
                'modified' => $this->timestamp(null, true),
                'modified_by' => $this->userId(),
            ],
            [
                'id IN' => $descendantsToUpdate,
                'object_type_id' => $this->objectType()->id,
                'deleted IS NOT' => $folder->deleted,
            ]
        );
    }
}
