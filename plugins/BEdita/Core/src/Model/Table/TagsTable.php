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
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tags Model
 *
 * @property \BEdita\Core\Model\Table\ObjectTagsTable&\Cake\ORM\Association\HasMany $ObjectTags
 * @property \BEdita\Core\Model\Table\ObjectsTable&\Cake\ORM\Association\BelongsToMany $Objects
 * @method \BEdita\Core\Model\Entity\Tag get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Tag newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Tag saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Tag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
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

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.Searchable');

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
     * Hide read-only fields when fetched as an association.
     *
     * @param \Cake\Event\EventInterface $event Fired event.
     * @param \Cake\ORM\Query $query Query object instance.
     * @param \ArrayObject $options Options array.
     * @param bool $primary Primary flag.
     * @return void
     */
    public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, bool $primary): void
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
     * Find tag IDs by their name.
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Array containing key `names` as a list of strings.
     * @return \Cake\ORM\Query
     */
    protected function findIds(Query $query, array $options): Query
    {
        if (empty($options['names']) || !is_array($options['names'])) {
            throw new BadFilterException(__d('bedita', 'Missing or wrong required parameter "{0}"', 'names'));
        }

        return $query
            ->find('enabled')
            ->select([$this->aliasField('id'), $this->aliasField('name')])
            ->where(function (QueryExpression $exp) use ($options): QueryExpression {
                return $exp
                    ->in($this->aliasField('name'), $options['names']);
            });
    }
}
