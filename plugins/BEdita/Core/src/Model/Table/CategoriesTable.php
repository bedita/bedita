<?php
declare(strict_types=1);

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
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query\SelectQuery;
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
 * @method \BEdita\Core\Model\Entity\Category get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\Category newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @mixin \BEdita\Core\Model\Behavior\TreeBehavior
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \BEdita\Core\Model\Entity\Category newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Category>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Category> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Category>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Category> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \BEdita\Core\Model\Behavior\SearchableBehavior
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
        $this->getSchema()->setColumnType('labels', 'json');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.Searchable', ['scopes' => (array)$this->getTable()]);
        $this->addBehavior('BEdita/Core.Tree', [
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

            ->allowEmptyArray('labels')

            ->boolean('enabled')
            ->notEmptyString('enabled');
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
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param \ArrayObject $options Options array.
     * @param bool $primary Primary flag.
     * @return void
     */
    public function beforeFind(EventInterface $event, SelectQuery $query, ArrayObject $options, bool $primary): void
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
     * @param \Cake\ORM\Query\SelectQuery $query Query object
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findEnabled(SelectQuery $query): SelectQuery
    {
        return $query->where([
            $this->aliasField('enabled') => true,
        ]);
    }

    /**
     * Find categories by object type name
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string $objectType The object type name.
     * @return \Cake\ORM\Query\SelectQuery
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    public function findType(SelectQuery $query, string $objectType): SelectQuery
    {
        return $query->innerJoinWith('ObjectTypes', function (SelectQuery $query) use ($objectType) {
            return $query->where([$this->ObjectTypes->aliasField('name') => $objectType]);
        });
    }

    /**
     * Find categories IDs by their name.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object.
     * @param array $names List of category names
     * @param int $typeId Object type id
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findIds(SelectQuery $query, array $names, int $typeId): SelectQuery
    {
        return $query
            ->find('enabled')
            ->select([$this->aliasField('id'), $this->aliasField('name')])
            ->where(function (QueryExpression $exp) use ($names, $typeId): QueryExpression {
                return $exp
                    ->eq($this->aliasField('object_type_id'), $typeId)
                    ->in($this->aliasField('name'), $names);
            });
    }

    /**
     * Find category resource by name and object type.
     * `$object_type_name` or `$object` must be provided.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string $name The category name
     * @param string|null $object_type_name The object type name
     * @param string|null $object The object type name (alternative to `object_type_name`)
     * @return \Cake\ORM\Query\SelectQuery
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function findResource(
        SelectQuery $query,
        string $name,
        ?string $object_type_name = null,
        ?string $object = null
    ): SelectQuery {
        $object = $object_type_name ?? $object;
        if (empty($object)) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'object_type_name'));
        }

        return $query->find('type', objectType: $object)
            ->where([$this->aliasField('name') => $name]);
    }

    /**
     * Finder for roots categories.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findRoots(SelectQuery $query): SelectQuery
    {
        return $query->where(
            fn (QueryExpression $exp): QueryExpression => $exp->isNull($this->aliasField('parent_id'))
        );
    }
}
