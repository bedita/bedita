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

use BEdita\Core\Configure\Engine\DatabaseConfig;
use BEdita\Core\State\CurrentApplication;
use Cake\Cache\Cache;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Config Model - used to handle configuration data in DB
 *
 * @property \BEdita\Core\Model\Table\ApplicationsTable&\Cake\ORM\Association\BelongsTo $Applications
 *
 * @method \BEdita\Core\Model\Entity\Config get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Config newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Config saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Config patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 *
 * @since 4.0.0
 */
class ConfigTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('config');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ]
            ],
        ]);

        $this->belongsTo('Applications');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['application_id'], 'Applications'));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->alphaNumeric('name')

            ->requirePresence('context', 'create')
            ->notEmpty('context')

            ->requirePresence('content', 'create')
            ->notEmpty('content');

        return $validator;
    }

    /**
     * Invalidate database config cache after saving a config entity.
     *
     * @return void
     */
    public function afterSave()
    {
        Cache::clear(false, DatabaseConfig::CACHE_CONFIG);
    }

    /**
     * Invalidate database config cache after deleting a config entity.
     *
     * @return void
     */
    public function afterDelete()
    {
        Cache::clear(false, DatabaseConfig::CACHE_CONFIG);
    }

    /**
     * Finder for my config.
     * Common configuration (where `application_id` is NULL)
     * and configuration of the current application is returned.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findMine(Query $query)
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->or_(function (QueryExpression $exp) {
                $id = CurrentApplication::getApplicationId();
                if ($id !== null) {
                    $exp->eq($this->aliasField('application_id'), $id);
                }

                return $exp->isNull($this->aliasField('application_id'));
            });
        });
    }

    /**
     * Finder for configuration by name and optional application name or id.
     * Options array MUST have `name` and optionally `application` (application name) or `application_id`
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options array.
     * @return \Cake\ORM\Query
     */
    protected function findName(Query $query, array $options): Query
    {
        if (empty($options['name'])) {
            throw new BadRequestException(__d('bedita', 'Missing mandatory option "name"'));
        }
        $query = $query->where([$this->aliasField('name') => $options['name']]);
        if (empty($options['application']) && empty($options['application_id'])) {
            return $query;
        }

        return $query->innerJoinWith('Applications', function (Query $query) use ($options) {
            if (!empty($options['application'])) {
                $conditions = [$this->Applications->aliasField('name') => $options['application']];
            } else {
                $conditions = [$this->Applications->aliasField('id') => $options['application_id']];
            }

            return $query->where($conditions);
        });
    }

    /**
     * Alias for `name` finder.
     * Used to load entity in `BEdita\Core\Utility\Resources`
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options array.
     * @return \Cake\ORM\Query
     * @codeCoverageIgnore
     */
    protected function findResource(Query $query, array $options): Query
    {
        return $query->find('name', $options);
    }
}
