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
     * Finder for root folders.
     *
     * @param Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findRoots(Query $query)
    {
        $subquery = TableRegistry::get('Trees')->find('list')->where(['parent_id IS NULL']);

        $query->join([
            'table' => 'trees',
            'alias' => 'Trees',
            'type' => 'INNER',
            'conditions' => 'Trees.object_id = ' . $this->aliasField('id'),
        ])
        ->where(function (QueryExpression $exp) use ($subquery) {
            return $exp->in('Trees.id', $subquery);
        });

        return $query;
    }
}
