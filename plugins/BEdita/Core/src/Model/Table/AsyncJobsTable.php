<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Model\Table;

use BEdita\Core\Job\QueueJob;
use BEdita\Core\Model\Entity\AsyncJob;
use BEdita\Core\Model\Validation\Validation;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\I18n\DateTime;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use Cake\Queue\QueueManager;
use Cake\Validation\Validator;

/**
 * AsyncJobs Model
 *
 * @method \BEdita\Core\Model\Entity\AsyncJob get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\AsyncJob newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \BEdita\Core\Model\Entity\AsyncJob newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\AsyncJob saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\AsyncJob>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\AsyncJob> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\AsyncJob>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\AsyncJob> deleteManyOrFail(iterable $entities, array $options = [])
 * @since 4.0.0
 */
class AsyncJobsTable extends Table
{
    /**
     * @inheritDoc
     */
    public static function defaultConnectionName(): string
    {
        if (in_array('async_jobs', ConnectionManager::configured())) {
            return 'async_jobs';
        }

        return parent::defaultConnectionName();
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('async_jobs');

        $this->setPrimaryKey('uuid');
        $this->setDisplayField('payload');
        $this->getSchema()
            ->setColumnType('payload', 'json')
            ->setColumnType('uuid', 'uuid')
            ->setColumnType('results', 'json');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ],
                'AsyncJob.complete' => [
                    'completed' => 'always',
                ],
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
        return $validator
            ->uuid('uuid')
            ->allowEmptyString('uuid', null, 'create')

            ->requirePresence('service', 'create')
            ->notEmptyString('service')

            ->naturalNumber('priority')
            ->allowEmptyString('priority')

            ->allowEmptyString('payload')

            ->add('scheduled_from', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmptyDateTime('scheduled_from')

            ->add('expires', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmptyDateTime('expires')

            ->naturalNumber('max_attempts')
            ->notEmptyString('max_attempts')

            ->add('locked_until', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmptyDateTime('locked_until')

            ->add('completed', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmptyDateTime('completed')

            ->allowEmptyString('results');
    }

    /**
     * Queue async job as new entity is created.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \BEdita\Core\Model\Entity\AsyncJob $entity The entity persisted
     * @return void
     */
    public function afterSave(EventInterface $event, AsyncJob $entity): void
    {
        if (!$entity->isNew() || !QueueManager::getConfig('default')) {
            return;
        }

        $delay = (int)Configure::read('Queue.default.pushDelay', 1);
        if ($entity->scheduled_from && $entity->scheduled_from->isFuture()) {
            $delay = $entity->scheduled_from->diffInSeconds();
        }

        $expires = null;
        if ($entity->expires && $entity->expires->isFuture()) {
            $expires = $entity->expires->diffInSeconds();
        }

        QueueManager::push(
            QueueJob::class,
            ['uuid' => $entity->uuid],
            compact('delay', 'expires'),
        );
    }

    /**
     * Lock an asynchronous job for execution.
     *
     * @param string $uuid UUID of job to be locked.
     * @param mixed $duration Duration. By default, jobs are locked for 5 minutes.
     * @return \BEdita\Core\Model\Entity\AsyncJob
     */
    public function lock(string $uuid, mixed $duration = '+5 minutes'): AsyncJob
    {
        return $this->getConnection()->transactional(function () use ($uuid, $duration) {
            $entity = $this->get($uuid, finder: 'pending');
            $entity->max_attempts -= 1;
            $entity->locked_until = new DateTime($duration);

            $expires = $entity->locked_until->timestamp;
            $this->dispatchEvent('AsyncJob.lock', compact('entity', 'expires'));

            return $this->saveOrFail($entity, ['atomic' => false]);
        });
    }

    /**
     * Unlock an asynchronous job after execution (either successful or failed).
     *
     * @param string $uuid UUID of job to be unlocked.
     * @param mixed $success Job run success. If strictly equal to `false`, job is considered failed.
     * @return void
     */
    public function unlock(string $uuid, mixed $success): void
    {
        $this->getConnection()->transactional(function () use ($uuid, $success): void {
            $entity = $this->get($uuid);
            $entity->locked_until = null;

            $event = 'AsyncJob.fail';
            if ($success !== false) {
                $event = 'AsyncJob.complete';
            }
            $this->dispatchEvent($event, compact('entity', 'success'));

            $this->saveOrFail($entity, ['atomic' => false]);
        });
    }

    /**
     * Finder for pending jobs.
     *
     * This finder returns a query object that filters asynchronous jobs that are
     * still valid (not completed, not yet expired, not locked, and have some attempts left).
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findPending(SelectQuery $query): SelectQuery
    {
        $now = DateTime::now();

        return $query->where(fn(QueryExpression $exp): QueryExpression => $exp->and([
            $exp->or(
                fn(QueryExpression $exp): QueryExpression => $exp
                    ->isNull($this->aliasField('scheduled_from'))
                    ->lte($this->aliasField('scheduled_from'), $now),
            ),
            $exp->or(
                fn(QueryExpression $exp): QueryExpression => $exp
                    ->isNull($this->aliasField('expires'))
                    ->gte($this->aliasField('expires'), $now),
            ),
            $exp->or(
                fn(QueryExpression $exp): QueryExpression => $exp
                    ->isNull($this->aliasField('locked_until'))
                    ->lt($this->aliasField('locked_until'), $now),
            ),
            fn(QueryExpression $exp): QueryExpression => $exp
                ->gt($this->aliasField('max_attempts'), 0)
                ->isNull($this->aliasField('completed')),
        ]));
    }

    /**
     * Finder for failed async jobs.
     *
     * This finder returns a query object that filters asynchronous jobs that are either expired or
     * that have failed too many times.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findFailed(SelectQuery $query): SelectQuery
    {
        $now = DateTime::now();

        return $query->where(fn(QueryExpression $exp): QueryExpression => $exp->and([
            fn(QueryExpression $exp): QueryExpression => $exp->isNull($this->aliasField('completed')),
            $exp->or([
                fn(QueryExpression $exp): QueryExpression => $exp->lt($this->aliasField('expires'), $now),
                $exp->and([
                    fn(QueryExpression $exp): QueryExpression => $exp->eq($this->aliasField('max_attempts'), 0),
                    $exp->or(
                        fn(QueryExpression $exp): QueryExpression => $exp
                            ->isNull($this->aliasField('locked_until'))
                            ->lt($this->aliasField('locked_until'), $now),
                    ),
                ]),
            ]),
        ]));
    }

    /**
     * Finder for completed async jobs.
     *
     * This finder returns a query object that filters asynchronous jobs that have been completed successfully.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findCompleted(SelectQuery $query): SelectQuery
    {
        return $query->where(
            fn(QueryExpression $exp): QueryExpression => $exp->isNotNull($this->aliasField('completed')),
        );
    }

    /**
     * Finder for incomplete async jobs.
     *
     * This finder returns a query object that filters asynchronous jobs that haven't been completed yet.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findIncomplete(SelectQuery $query): SelectQuery
    {
        return $query->where(
            fn(QueryExpression $exp): QueryExpression => $exp->isNull($this->aliasField('completed')),
        );
    }

    /**
     * Find pending asynchronous jobs sorted by descending priority, and optionally filtered by service and priority.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findPriority(SelectQuery $query, ?int $priority = null, ?string $service = null): SelectQuery
    {
        if (!empty($priority)) {
            $query = $query->where(
                fn(QueryExpression $exp): QueryExpression => $exp->gte($this->aliasField('priority'), $priority),
            );
        }
        if (!empty($service)) {
            $query = $query->where(
                fn(QueryExpression $exp): QueryExpression => $exp->eq($this->aliasField('service'), $service),
            );
        }

        return $query
            ->find('pending')
            ->orderByDesc($this->aliasField('priority'));
    }

    /**
     * Update field "results" by entity, success and message
     *
     * @param \BEdita\Core\Model\Entity\AsyncJob $entity The Job entity
     * @param bool $success The success flag
     * @param array $messages The messages
     * @return void
     */
    public function updateResults(AsyncJob $entity, bool $success, array $messages = []): void
    {
        $this->getConnection()->transactional(function () use ($entity, $success, $messages): void {
            $results = (array)$entity->get('results');
            $attempt = count($results) + 1;
            $data = compact('messages');
            $result = compact('data', 'success');
            $result['attempt_number'] = $attempt;
            $results[] = $result;
            $entity->set('results', $results);
            $this->saveOrFail($entity, ['atomic' => false]);
        });
    }
}
