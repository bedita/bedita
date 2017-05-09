<?php
namespace BEdita\Core\Model\Table;

use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AsyncJobs Model
 *
 * @method \BEdita\Core\Model\Entity\AsyncJob get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AsyncJob findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 *
 * @since 4.0.0
 */
class AsyncJobsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public static function defaultConnectionName()
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
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('async_jobs');

        $this->setPrimaryKey('uuid');
        $this->setDisplayField('payload');

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
    public function validationDefault(Validator $validator)
    {
        $validator
            ->uuid('uuid')
            ->allowEmpty('uuid', 'create');

        $validator
            ->requirePresence('service', 'create')
            ->notEmpty('service');

        $validator
            ->naturalNumber('priority')
            ->allowEmpty('priority');

        $validator
            ->allowEmpty('payload');

        $validator
            ->dateTime('scheduled_from')
            ->allowEmpty('scheduled_from');

        $validator
            ->dateTime('expires')
            ->allowEmpty('expires');

        $validator
            ->naturalNumber('max_attempts')
            ->notEmpty('max_attempts');

        $validator
            ->dateTime('locked_until')
            ->allowEmpty('locked_until');

        $validator
            ->dateTime('completed')
            ->allowEmpty('completed');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->columnType('payload', 'json');

        return parent::_initializeSchema($schema);
    }

    /**
     * Lock an asynchronous job for execution.
     *
     * @param string $uuid UUID of job to be locked.
     * @param mixed $duration Duration. By default, jobs are locked for 5 minutes.
     * @return \BEdita\Core\Model\Entity\AsyncJob
     */
    public function lock($uuid, $duration = '+5 minutes')
    {
        return $this->getConnection()->transactional(function () use ($uuid, $duration) {
            $entity = $this->get($uuid, ['finder' => 'pending']);
            $entity->max_attempts -= 1;
            $entity->locked_until = new Time($duration);

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
    public function unlock($uuid, $success)
    {
        $this->getConnection()->transactional(function () use ($uuid, $success) {
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
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findPending(Query $query)
    {
        $now = $query->func()->now();

        return $query
            ->where(function (QueryExpression $exp) use ($now) {
                return $exp->and_([
                    $exp->or_(function (QueryExpression $exp) use ($now) {
                        $field = $this->aliasField('scheduled_from');

                        return $exp
                            ->isNull($field)
                            ->lte($field, $now);
                    }),
                    $exp->or_(function (QueryExpression $exp) use ($now) {
                        $field = $this->aliasField('expires');

                        return $exp
                            ->isNull($field)
                            ->gte($field, $now);
                    }),
                    $exp->or_(function (QueryExpression $exp) use ($now) {
                        $field = $this->aliasField('locked_until');

                        return $exp
                            ->isNull($field)
                            ->lt($field, $now);
                    }),
                    function (QueryExpression $exp) {
                        return $exp
                            ->gt($this->aliasField('max_attempts'), 0)
                            ->isNull($this->aliasField('completed'));
                    },
                ]);
            });
    }

    /**
     * Finder for failed async jobs.
     *
     * This finder returns a query object that filters asynchronous jobs that are either expired or
     * that have failed too many times.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findFailed(Query $query)
    {
        $now = $query->func()->now();

        return $query->where(function (QueryExpression $exp) use ($now) {
            return $exp->and_([
                function (QueryExpression $exp) {
                    return $exp->isNull($this->aliasField('completed'));
                },
                $exp->or_([
                    function (QueryExpression $exp) use ($now) {
                        return $exp->lt($this->aliasField('expires'), $now);
                    },
                    $exp->and_([
                        function (QueryExpression $exp) {
                            return $exp->eq($this->aliasField('max_attempts'), 0);
                        },
                        $exp->or_(function (QueryExpression $exp) use ($now) {
                            $field = $this->aliasField('locked_until');

                            return $exp
                                ->isNull($field)
                                ->lt($field, $now);
                        }),
                    ]),
                ]),
            ]);
        });
    }

    /**
     * Finder for completed async jobs.
     *
     * This finder returns a query object that filters asynchronous jobs that have been completed successfully.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findCompleted(Query $query)
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->isNotNull($this->aliasField('completed'));
        });
    }
}
