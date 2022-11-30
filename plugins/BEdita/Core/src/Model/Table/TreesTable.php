<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Model\Table;

use BEdita\Core\Exception\LockedResourceException;
use BEdita\Core\Model\Entity\Tree;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Rule\IsUnique;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Trees Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Objects
 * @property \Cake\ORM\Association\BelongsTo $ParentObjects
 * @property \Cake\ORM\Association\BelongsTo $RootObjects
 * @property \Cake\ORM\Association\BelongsTo $ParentNode
 * @property \Cake\ORM\Association\HasMany $ChildNodes
 * @method \BEdita\Core\Model\Entity\Tree get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Tree newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Tree patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \BEdita\Core\Model\Behavior\TreeBehavior
 */
class TreesTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('trees');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // associations with objects
        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ParentObjects', [
            'foreignKey' => 'parent_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Folders',
        ]);
        $this->belongsTo('RootObjects', [
            'foreignKey' => 'root_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Folders',
        ]);

        // associations with trees
        $this->belongsTo('ParentNode', [
            'className' => 'BEdita/Core.Trees',
            'foreignKey' => 'parent_node_id',
        ]);
        $this->hasMany('ChildNodes', [
            'className' => 'BEdita/Core.Trees',
            'foreignKey' => 'parent_node_id',
        ]);

        $this->addBehavior('BEdita/Core.Tree', [
            'left' => 'tree_left',
            'right' => 'tree_right',
            'parent' => 'parent_node_id',
            'level' => 'depth_level',
            'recoverOrder' => ['tree_left' => 'ASC', 'tree_right' => 'ASC', 'object_id' => 'ASC'],
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('position')
            ->notEmptyString('position')
            ->notEquals('position', 0, null, function ($context) {
                return is_numeric($context['data']['position']);
            })
            ->integer('position', null, function ($context) {
                return is_numeric($context['data']['position']);
            })
            ->inList('position', ['first', 'last'], null, function ($context) {
                return !is_numeric($context['data']['position']);
            });

        $validator
            ->boolean('menu')
            ->notEmptyString('menu');

        $validator
            ->boolean('canonical');

        $validator
            ->allowEmptyString('children_order')
            ->inList('children_order', [null, 'title', '-title', 'modified', '-modified']);

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['object_id'], 'Objects'));
        $rules->add($rules->existsIn(['root_id'], 'RootObjects'));
        $rules->add($rules->existsIn(
            ['parent_id'],
            'ParentObjects',
            ['allowNullableNulls' => true]
        ));
        $rules->add($rules->existsIn(
            ['parent_node_id'],
            'ParentNode',
            ['allowNullableNulls' => true]
        ));

        $rules->add(
            [$this, 'isParentValid'],
            'isParentValid',
            [
                'errorField' => 'parent_id',
                'message' => __d('bedita', 'parent_id must be null or corresponding to a folder'),
            ]
        );

        $rules->add(
            [$this, 'isPositionUnique'],
            'isPositionUnique',
            [
                'errorField' => 'object_id',
                'message' => __d('bedita', 'Folders cannot be made ubiquitous, other objects cannot appear twice in the same folder'),
            ]
        );

        $rules->add(
            [$this, 'isValidChildrenOrder'],
            'isValidChildrenOrder',
            [
                'errorField' => 'children_order',
                'message' => __d('bedita', 'The children_order is not valid. Valid values: null, title, -title, modified, -modified'),
            ]
        );

        return $rules;
    }

    /**
     * Check that `parent_id` property of the `Tree` entity corresponds to a folder
     *
     * @param \BEdita\Core\Model\Entity\Tree $entity The tree entity to validate
     * @return bool
     */
    public function isParentValid(Tree $entity)
    {
        // if parent_id is null then the object_id must refer to a folder (root)
        if ($entity->parent_id === null) {
            return $this->isFolder($entity->object_id);
        }

        return $this->isFolder($entity->parent_id);
    }

    /**
     * Check that a folder position is unique, and other objects' position is unique among their parent.
     *
     * @param \BEdita\Core\Model\Entity\Tree $entity The tree entity to validate.
     * @return bool
     */
    public function isPositionUnique(Tree $entity)
    {
        $rule = new IsUnique(['parent_id', 'object_id']);
        if ($this->isFolder($entity->object_id)) {
            $rule = new IsUnique(['object_id']);
        }

        return $rule($entity, ['repository' => $this]);
    }

    /**
     * Check that children_order is valid.
     * Allowed values: null, 'title', '-title', 'modified', '-modified'.
     *
     * @param \BEdita\Core\Model\Entity\Tree $entity The tree entity to validate.
     * @return bool
     */
    public function isValidChildrenOrder(Tree $entity)
    {
        if ($entity->children_order === null) {
            return true;
        }

        return in_array($entity->children_order, ['title', '-title', 'modified', '-modified']);
    }

    /**
     * Update `root_id` of children if needed.
     *
     * @param \Cake\Event\Event $event The event
     * @param \BEdita\Core\Model\Entity\Tree $entity The entity persisted
     * @return void
     */
    public function afterSave(Event $event, Tree $entity)
    {
        if ($entity->has('position')) {
            if ($this->moveAt($entity, $entity->get('position')) === false) {
                throw new BadRequestException(__d('bedita', 'Invalid position'));
            }
        }

        // if canonical set to `true` => set to `false` other `canonical` occurrences
        if ($entity->isDirty('canonical') && $entity->get('canonical')) {
            $this->updateAll(
                ['canonical' => false],
                [
                    'object_id' => $entity->object_id,
                    'id !=' => $entity->id,
                ]
            );
        }

        if ($entity->isNew()) {
            return;
        }

        // update root_id
        $this->updateAll(
            ['root_id' => $entity->root_id],
            [
                'tree_left >' => $entity->tree_left,
                'tree_right <' => $entity->tree_right,
                'root_id !=' => $entity->root_id,
            ]
        );
    }

    /**
     * Throw an exception when trying to remove a row that points to a folder, unless cascading.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \BEdita\Core\Model\Entity\Tree $entity Tree entity being deleted.
     * @param \ArrayObject $options Options.
     * @return void
     * @throws \BEdita\Core\Exception\LockedResourceException Throws an exception when the delete operation would
     *  leave an orphaned folder.
     */
    public function beforeDelete(Event $event, Tree $entity, \ArrayObject $options)
    {
        if (empty($options['_primary'])) {
            return;
        }

        // Refuse to delete a row that points to a folder.
        if ($this->isFolder($entity->object_id)) {
            throw new LockedResourceException(__d('bedita', 'This operation would leave an orphaned folder'));
        }
    }

    /**
     * Check if a given ID is the ID of a Folder.
     *
     * @param int $id ID of object being checked.
     * @return bool
     */
    protected function isFolder($id)
    {
        static $foldersType = null;
        if ($foldersType === null) {
            $foldersType = TableRegistry::getTableLocator()->get('ObjectTypes')->get('folders')->id;
        }

        return $this->Objects->exists([
            $this->Objects->aliasField('object_type_id') => $foldersType,
            $this->Objects->aliasField('id') => $id,
        ]);
    }

    /**
     * Find path nodes from object id.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array with object id as first element.
     * @return \Cake\ORM\Query
     */
    protected function findPathNodes(Query $query, array $options)
    {
        if (empty($options)) {
            throw new BadRequestException(__d('bedita', 'Missing required parameter "{0}"', 'object id'));
        }

        $node = $this->find()
            ->select([
                $this->aliasField('tree_left'),
                $this->aliasField('tree_right'),
            ])
            ->where(['object_id' => $options[0]])
            ->firstOrFail();

        $query = $query->where(function (QueryExpression $exp) use ($node) {
            return $exp
                ->lte($this->aliasField('tree_left'), $node->get('tree_left'))
                ->gte($this->aliasField('tree_right'), $node->get('tree_right'));
        });

        return $query->order([$this->aliasField('tree_left') => 'ASC']);
    }

    /**
     * Get sort by object ID.
     * Default 'Trees.tree_left' => 'asc'
     *
     * @param int $objectId The tree object ID
     * @return array
     */
    public function getSort(int $objectId): array
    {
        /** @var \BEdita\Core\Model\Entity\Tree $entity */
        $entity = $this->find()
            ->select([$this->aliasField('children_order')])
            ->where([$this->aliasField('object_id') => $objectId])
            ->first();
        if (empty($entity) || empty($entity->children_order)) {
            return ['Trees.tree_left' => 'asc'];
        }
        $sign = substr($entity->children_order, 0, 1);
        $direction = $sign === '-' ? 'desc' : 'asc';
        $field = $sign === '-' ? substr($entity->children_order, 1) : $entity->children_order;
        $key = sprintf('Children.%s', $field);

        return [$key => $direction];
    }
}
