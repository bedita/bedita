<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Validation\ObjectsValidator;
use BEdita\Core\ORM\QueryFilterTrait;
use BEdita\Core\Utility\LoggedUser;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Objects Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ObjectTypes
 * @property \Cake\ORM\Association\BelongsTo $CreatedByUser
 * @property \Cake\ORM\Association\BelongsTo $ModifiedByUser
 * @property \Cake\ORM\Association\HasMany $DateRanges
 *
 * @method \BEdita\Core\Model\Entity\ObjectEntity get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \BEdita\Core\Model\Behavior\UserModifiedBehavior
 * @mixin \BEdita\Core\Model\Behavior\RelationsBehavior
 *
 * @since 4.0.0
 */
class ObjectsTable extends Table
{

    use QueryFilterTrait;

    /**
     * {@inheritDoc}
     */
    protected $_validatorClass = ObjectsValidator::class;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('objects');
        $this->setEntityClass(ObjectEntity::class);
        $this->setPrimaryKey('id');
        $this->setDisplayField('title');

        $this->addBehavior('Timestamp');

        $this->addBehavior('BEdita/Core.DataCleanup');

        $this->addBehavior('BEdita/Core.UserModified');

        $this->addBehavior('BEdita/Core.Relations');

        $this->addBehavior('BEdita/Core.CustomPropertis');

        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.ObjectTypes'
        ]);

        $this->hasMany('DateRanges', [
            'foreignKey' => 'object_id',
            'className' => 'BEdita/Core.DateRanges',
            'saveStrategy' => 'replace',
        ]);

        $this->belongsTo('CreatedByUser', [
            'foreignKey' => 'created_by',
            'className' => 'BEdita/Core.Users'
        ]);

        $this->belongsTo('ModifiedByUser', [
            'foreignKey' => 'modified_by',
            'className' => 'BEdita/Core.Users'
        ]);

        $this->addBehavior('BEdita/Core.UniqueName');

        $this->addBehavior('BEdita/Core.Searchable', [
            'fields' => [
                'title' => 10,
                'description' => 7,
                'body' => 5,
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
        $rules->add($rules->isUnique(['uname']));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));
        $rules->add($rules->existsIn(['created_by'], 'CreatedByUser'));
        $rules->add($rules->existsIn(['modified_by'], 'ModifiedByUser'));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->columnType('custom_props', 'json');
        $schema->columnType('extra', 'json');

        return $schema;
    }

    /**
     * Find by object type.
     *
     * You can pass a list of allowed object types to this finder:
     *
     * ```
     * $table->find('type', [1, 'document', 'profiles', 1004]);
     * ```
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable object types.
     * @return \Cake\ORM\Query
     */
    protected function findType(Query $query, array $options)
    {
        foreach ($options as &$type) {
            $type = $this->ObjectTypes->get($type)->id;
        }
        unset($type);
        $field = $this->aliasField($this->ObjectTypes->getForeignKey());

        return $this->fieldsFilter($query, [$field => $options]);
    }

    /**
     * Find by date range using `DateRanges` table findDate filter
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable date range conditions.
     * @return \Cake\ORM\Query
     */
    protected function findDateRanges(Query $query, array $options)
    {
        return $query
            ->distinct([$this->aliasField($this->getPrimaryKey())])
            ->innerJoinWith('DateRanges', function (Query $query) use ($options) {
                return $query->find('dateRanges', $options);
            });
    }

    /**
     * Finder for my objects (i.e.: user created by logged-in user)
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findMine(Query $query)
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->eq($this->aliasField($this->CreatedByUser->getForeignKey()), LoggedUser::id());
        });
    }
}
