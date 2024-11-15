<?php
declare(strict_types=1);

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

use BEdita\Core\State\CurrentApplication;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * Config Model - used to handle configuration data in DB
 *
 * @property \Cake\ORM\Table&\Cake\ORM\Association\BelongsTo $Applications
 * @method \BEdita\Core\Model\Entity\Config get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\Config newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \Cake\ORM\Query\SelectQuery queryCache(\Cake\ORM\Query\SelectQuery $query, string $key)
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \BEdita\Core\Model\Entity\Config newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\Config[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Config>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Config> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Config>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Config[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Config> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \BEdita\Core\Model\Behavior\QueryCacheBehavior
 * @since 4.0.0
 */
class ConfigTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('config');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ],
            ],
        ]);
        $this->addBehavior('BEdita/Core.QueryCache');

        $this->belongsTo('Applications');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['application_id'], 'Applications'));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->alphaNumeric('name')

            ->requirePresence('context', 'create')
            ->notEmptyString('context')

            ->requirePresence('content', 'create')
            ->notEmptyString('content');

        return $validator;
    }

    /**
     * Finder for my config.
     * Common configuration (where `application_id` is NULL)
     * and configuration of the current application is returned.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findMine(SelectQuery $query): SelectQuery
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->or(function (QueryExpression $exp) {
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
     * Options array MUST be:
     *  - an associative array with `name` and optionally `application` (application name) or `application_id`
     *  - a non empty indexed array, the first element is then used as `name`
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array $options Options array.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findName(SelectQuery $query, array $options): SelectQuery
    {
        if (empty($options[0]) && empty($options['name'])) {
            throw new BadRequestException(__d('bedita', 'Missing mandatory option "name"'));
        }
        $name = (string)Hash::get($options, 'name', Hash::get($options, '0'));
        $query = $query->where([$this->aliasField('name') => $name]);
        if (empty($options['application']) && empty($options['application_id'])) {
            return $query;
        }

        return $query->innerJoinWith('Applications', function (SelectQuery $query) use ($options) {
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
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array $options Options array.
     * @return \Cake\ORM\Query\SelectQuery
     * @codeCoverageIgnore
     */
    protected function findResource(SelectQuery $query, array $options): SelectQuery
    {
        return $query->find('name', $options);
    }

    /**
     * Fetch configuration from database using cache.
     *
     * @param int|null $applicationId Application ID.
     * @param string|null $context Config context.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function fetchConfig(?int $applicationId, ?string $context): SelectQuery
    {
        $query = $this->find()
            ->select(['name', 'content'])
            ->disableHydration()
            ->where(function (QueryExpression $exp) use ($applicationId, $context): QueryExpression {
                if (!empty($context)) {
                    $exp = $exp->eq($this->aliasField('context'), $context);
                }
                if ($applicationId !== null) {
                    return $exp->eq($this->aliasField('application_id'), $applicationId);
                }

                return $exp->isNull($this->aliasField('application_id'));
            });

        return $this->queryCache(
            $query,
            sprintf('config_%s_%s', $applicationId ?: 'any', $context ?: 'any')
        );
    }
}
