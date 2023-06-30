<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Validation\Validation;
use BEdita\Core\ORM\Rule\IsUniqueAmongst;
use BEdita\Core\Search\SimpleSearchTrait;
use Cake\Cache\Cache;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;

/**
 * Relations Model
 *
 * @property \Cake\ORM\Association\HasMany $ObjectRelations
 * @property \Cake\ORM\Association\BelongsToMany $LeftObjectTypes
 * @property \Cake\ORM\Association\BelongsToMany $RightObjectTypes
 * @method \BEdita\Core\Model\Entity\Relation newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Relation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation findOrCreate($search, callable $callback = null, $options = [])
 */
class RelationsTable extends Table
{
    use SimpleSearchTrait;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('relations');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('ObjectRelations');

        $through = TableRegistry::getTableLocator()->get('LeftRelationTypes', ['className' => 'RelationTypes']);
        $this->belongsToMany('LeftObjectTypes', [
            'className' => 'ObjectTypes',
            'through' => $through,
            'foreignKey' => 'relation_id',
            'targetForeignKey' => 'object_type_id',
            'conditions' => [
                $through->aliasField('side') => 'left',
            ],
        ]);
        $through = TableRegistry::getTableLocator()->get('RightRelationTypes', ['className' => 'RelationTypes']);
        $this->belongsToMany('RightObjectTypes', [
            'className' => 'ObjectTypes',
            'through' => $through,
            'foreignKey' => 'relation_id',
            'targetForeignKey' => 'object_type_id',
            'conditions' => [
                $through->aliasField('side') => 'right',
            ],
        ]);
        $this->addBehavior('BEdita/Core.Searchable');
        $this->addBehavior('BEdita/Core.ResourceName');

        $this->setupSimpleSearch([
            'fields' => [
                'name',
                'inverse_name',
                'description',
                'label',
                'inverse_label',
            ],
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
            ->setProvider('bedita', Validation::class)

            ->integer('id')
            ->allowEmptyString('id', null, 'create')

            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->regex('name', Validation::RESOURCE_NAME_REGEX)
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])

            ->allowEmptyString('label', null, 'create')
            ->notEmptyString('label', null, 'update')

            ->requirePresence('inverse_name', 'create')
            ->notEmptyString('inverse_name')
            ->regex('inverse_name', Validation::RESOURCE_NAME_REGEX)
            ->add('inverse_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])

            ->allowEmptyString('inverse_label', null, 'create')
            ->notEmptyString('inverse_label', null, 'update')

            ->allowEmptyString('description')

            ->allowEmptyArray('params')
            ->add('params', 'valid', [
                'rule' => ['jsonSchema', 'http://json-schema.org/draft-06/schema#'],
                'provider' => 'bedita',
            ]);

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules
            ->add(new IsUniqueAmongst(['name' => ['name', 'inverse_name']]), '_isUniqueAmongst', [
                'errorField' => 'name',
                'message' => __d('cake', 'This value is already in use'),
            ])
            ->add(new IsUniqueAmongst(['inverse_name' => ['name', 'inverse_name']]), '_isUniqueAmongst', [
                'errorField' => 'inverse_name',
                'message' => __d('cake', 'This value is already in use'),
            ]);

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        $schema->setColumnType('params', 'jsonobject');

        return $schema;
    }

    /**
     * {@inheritDoc}
     *
     * @return \BEdita\Core\Model\Entity\Relation
     */
    public function get($primaryKey, array $options = []): EntityInterface
    {
        if (!is_numeric($primaryKey)) {
            $relation = $this->find('byName', ['name' => $primaryKey])
                ->select('id')
                ->firstOrFail();

            $primaryKey = $relation->id;
        }

        return parent::get($primaryKey, $options);
    }

    /**
     * Find a relation by its name or inverse name.
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Additional options. The `name` key is required.
     * @return \Cake\ORM\Query
     */
    protected function findByName(Query $query, array $options = [])
    {
        if (empty($options['name'])) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'name'));
        }
        $name = Inflector::underscore($options['name']);

        return $query->where(function (QueryExpression $exp) use ($name) {
            return $exp->or(function (QueryExpression $exp) use ($name) {
                return $exp
                    ->eq($this->aliasField('name'), $name)
                    ->eq($this->aliasField('inverse_name'), $name);
            });
        });
    }

    /**
     * Populate default `label` and `inverse_label` properties if not set.
     *
     * {@inheritDoc}
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity): void
    {
        if (!$entity->isNew()) {
            return;
        }
        if (empty($entity->get('label'))) {
            $entity->set('label', Inflector::humanize((string)$entity->get('name')));
        }
        if (empty($entity->get('inverse_label'))) {
            $entity->set('inverse_label', Inflector::humanize((string)$entity->get('inverse_name')));
        }
    }

    /**
     * Invalidate object types cache after updating a relation.
     *
     * @return void
     */
    public function afterSave()
    {
        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
    }

    /**
     * Invalidate object types cache after deleting a relation.
     *
     * @return void
     */
    public function afterDelete()
    {
        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
    }
}
