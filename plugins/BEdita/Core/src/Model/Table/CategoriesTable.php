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

use ArrayObject;
use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Validation\Validation;
use BEdita\Core\Search\SimpleSearchTrait;
use Cake\Collection\CollectionInterface;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * Categories Model
 *
 * @property \BEdita\Core\Model\Table\ObjectTypesTable&\Cake\ORM\Association\BelongsTo $ObjectTypes
 * @property \BEdita\Core\Model\Table\CategoriesTable&\Cake\ORM\Association\BelongsTo $ParentCategories
 * @property \BEdita\Core\Model\Table\CategoriesTable&\Cake\ORM\Association\HasMany $ChildCategories
 * @property \BEdita\Core\Model\Table\ObjectCategoriesTable&\Cake\ORM\Association\HasMany $ObjectCategories
 * @property \BEdita\Core\Model\Table\ObjectsTable&\Cake\ORM\Association\BelongsToMany $Objects
 * @method \BEdita\Core\Model\Entity\Category get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Category newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Category saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Category patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CategoriesTable extends Table
{
    use SimpleSearchTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('categories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.Searchable');
        $this->addBehavior('Tree', [
            'left' => 'tree_left',
            'right' => 'tree_right',
        ]);

        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'className' => 'BEdita/Core.ObjectTypes',
        ]);
        $this->belongsTo('ParentCategories', [
            'className' => 'BEdita/Core.Categories',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('ChildCategories', [
            'className' => 'BEdita/Core.Categories',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('ObjectCategories', [
            'foreignKey' => 'category_id',
            'className' => 'BEdita/Core.ObjectCategories',
        ]);
        $this->belongsToMany('Objects', [
            'className' => 'BEdita/Core.Objects',
            'foreignKey' => 'category_id',
            'targetForeignKey' => 'object_id',
            'through' => 'BEdita/Core.ObjectCategories',
        ]);

        $this->setupSimpleSearch(['fields' => ['labels', 'name']]);
    }

    /**
     * Common validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create')

            ->scalar('name')
            ->maxLength('name', 50)
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->regex('name', Validation::CATEGORY_NAME_REGEX)

            ->scalar('labels')
            ->allowEmptyString('labels')

            ->boolean('enabled')
            ->notEmptyString('enabled');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        $schema->setColumnType('labels', 'json');

        return $schema;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        return $rules
            ->add(
                $rules->existsIn(['object_type_id'], 'ObjectTypes'),
                null,
                ['errorField' => 'object_type_id']
            )
            ->add(
                $rules->existsIn(['parent_id'], 'ParentCategories', ['allowNullableNulls' => true]),
                null,
                ['errorField' => 'parent_id']
            );
    }

    /**
     * Hide read-only fields when fetched as an association.
     *
     * @param \Cake\Event\EventInterface $event Fired event.
     * @param \Cake\ORM\Query $query Query object instance.
     * @param \ArrayObject $options Options array.
     * @param bool $primary Primary flag.
     * @return void
     */
    public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, bool $primary)
    {
        if ($primary) {
            return;
        }

        $query->formatResults(function (CollectionInterface $results): CollectionInterface {
            return $results->map(function ($row) {
                if (!empty($row['_joinData'])) {
                    $row['params'] = $row['_joinData']['params'] ?? null;
                }
                if (!$row instanceof EntityInterface) {
                    return $row;
                }

                return $row->setHidden(
                    [
                        'id', 'enabled', 'created', 'modified',
                        'object_type_id', 'object_type_name',
                        'parent', 'object',
                        'parent_id', 'tree_left', 'tree_right',
                    ],
                    true
                );
            });
        });
    }

    /**
     * Filter only enabled categories.
     *
     * @param \Cake\ORM\Query $query Query object
     * @return \Cake\ORM\Query
     */
    protected function findEnabled(Query $query): Query
    {
        return $query->where([
            $this->aliasField('enabled') => true,
        ]);
    }

    /**
     * Find categories by object type name
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options array.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function findType(Query $query, array $options): Query
    {
        if (empty($options[0])) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'type'));
        }

        return $query->innerJoinWith('ObjectTypes', function (Query $query) use ($options) {
            return $query->where([$this->ObjectTypes->aliasField('name') => $options[0]]);
        });
    }

    /**
     * Find categories IDs by their name.
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Array containing key `names` as a list of strings, and `typeId` as an integer.
     * @return \Cake\ORM\Query
     */
    protected function findIds(Query $query, array $options)
    {
        if (empty($options['names']) || !is_array($options['names'])) {
            throw new BadFilterException(__d('bedita', 'Missing or wrong required parameter "{0}"', 'names'));
        }
        if (empty($options['typeId'])) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'typeId'));
        }

        return $query
            ->find('enabled')
            ->select([$this->aliasField('id'), $this->aliasField('name')])
            ->where(function (QueryExpression $exp) use ($options): QueryExpression {
                return $exp
                    ->eq($this->aliasField('object_type_id'), $options['typeId'])
                    ->in($this->aliasField('name'), $options['names']);
            });
    }

    /**
     * Find category resource by name and object type.
     * Options array argument MUST contain 'name' and 'object_type_name' keys.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options array.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function findResource(Query $query, array $options): Query
    {
        if (empty($options['name'])) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'name'));
        }
        $object = Hash::get($options, 'object_type_name', Hash::get($options, 'object'));
        if (empty($object)) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'object_type_name'));
        }

        return $query->find('type', [$object])
            ->where([$this->aliasField('name') => $options['name']]);
    }
}
