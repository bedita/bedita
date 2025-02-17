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
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tags Model
 *
 * @property \BEdita\Core\Model\Table\ObjectTagsTable&\Cake\ORM\Association\HasMany $ObjectTags
 * @property \BEdita\Core\Model\Table\ObjectsTable&\Cake\ORM\Association\BelongsToMany $Objects
 * @method \BEdita\Core\Model\Entity\Tag get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\Tag newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Tag>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Tag> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Tag>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Tag> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \BEdita\Core\Model\Behavior\SearchableBehavior
 */
class TagsTable extends Table
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

        $this->setTable('tags');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->getSchema()->setColumnType('labels', 'json');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.Searchable', ['scopes' => (array)$this->getTable()]);

        $this->hasMany('ObjectTags', [
            'foreignKey' => 'tag_id',
            'className' => 'BEdita/Core.ObjectTags',
        ]);
        $this->belongsToMany('Objects', [
            'className' => 'BEdita/Core.Objects',
            'foreignKey' => 'tag_id',
            'targetForeignKey' => 'object_id',
            'through' => 'BEdita/Core.ObjectTags',
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
                if (!$row instanceof EntityInterface) {
                    return $row;
                }

                return $row->setHidden(
                    ['id', 'enabled', 'created', 'modified'],
                    true
                );
            });
        });
    }

    /**
     * Filter only enabled tags.
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
     * Find tag IDs by their name.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object.
     * @param array $names List of tag names
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findIds(SelectQuery $query, array $names): SelectQuery
    {
        return $query
            ->find('enabled')
            ->select([$this->aliasField('id'), $this->aliasField('name')])
            ->where(function (QueryExpression $exp) use ($names): QueryExpression {
                return $exp
                    ->in($this->aliasField('name'), $names);
            });
    }
}
