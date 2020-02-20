<?php
namespace BEdita\Core\Model\Table;

use BEdita\Core\Model\Validation\Validation;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * ObjectRelations Model
 *
 * @property \Cake\ORM\Association\BelongsTo $LeftObjects
 * @property \Cake\ORM\Association\BelongsTo $Relations
 * @property \Cake\ORM\Association\BelongsTo $RightObjects
 *
 * @method \BEdita\Core\Model\Entity\ObjectRelation get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectRelation newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectRelation[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectRelation|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectRelation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectRelation[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectRelation findOrCreate($search, callable $callback = null, $options = [])
 */
class ObjectRelationsTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('object_relations');
        $this->setDisplayField('left_id');
        $this->setPrimaryKey(['left_id', 'relation_id', 'right_id']);

        $this->belongsTo('LeftObjects', [
            'foreignKey' => 'left_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
        ]);
        $this->belongsTo('Relations', [
            'foreignKey' => 'relation_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Relations'
        ]);
        $this->belongsTo('RightObjects', [
            'foreignKey' => 'right_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
        ]);

        $this->addBehavior('BEdita/Core.Priority', [
            'fields' => [
                'priority' => [
                    'scope' => ['left_id', 'relation_id'],
                ],
                'inv_priority' => [
                    'scope' => ['right_id', 'relation_id'],
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
            ->nonNegativeInteger('priority');

        $validator
            ->nonNegativeInteger('inv_priority');

        $validator
            ->allowEmpty('params', function ($context) {
                return static::jsonSchema(null, $context) === true;
            })
            ->requirePresence('params', function ($context) {
                return $context['newRecord'] && static::jsonSchema(null, $context) !== true;
            })
            ->add('params', 'valid', [
                'rule' => 'jsonSchema',
                'provider' => 'table',
            ]);

        return $validator;
    }

    /**
     * Validate relationship parameters using JSON Schema.
     *
     * @param mixed $value Value being validated.
     * @param array $context Validation context.
     * @return true|string
     */
    public static function jsonSchema($value, $context)
    {
        if (empty($context['providers']['jsonSchema'])) {
            return true;
        }

        $success = Validation::jsonSchema($value, $context['providers']['jsonSchema']);
        if ($success !== true && $value === null && Validation::jsonSchema(new \stdClass(), $context['providers']['jsonSchema']) === true) {
            // For the sake of validation, `null` is equivalent to empty object.
            $success = true;
        }

        return $success;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['left_id'], 'LeftObjects'));
        $rules->add($rules->existsIn(['relation_id'], 'Relations'));
        $rules->add($rules->existsIn(['right_id'], 'RightObjects'));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('params', 'json');

        return $schema;
    }

    /**
     * Set default `priority` and `inv_priority` values if not set.
     *
     * @param \Cake\Event\Event $event Event fired
     * @param \Cake\Datasource\EntityInterface $entity Entity to be saved
     * @return void
     * @throws ImmutableResourceException
     */
    public function beforeSave(Event $event, EntityInterface $entity): void
    {
        if (empty($entity->get('priority'))) {
            $max = $this->maxPriority('priority', $entity);
            $entity->set('priority', $max + 1);
        }
        if (empty($entity->get('inv_priority'))) {
            $max = $this->maxPriority('inv_priority', $entity);
            $entity->set('inv_priority', $max + 1);
        }
    }

    /**
     * Get current max priority on an object relation
     *
     * @param string $priority Priority field name: 'priority' or 'inv_priority'
     * @param EntityInterface $entity Entity being saved
     * @return int
     */
    protected function maxPriority(string $priority, EntityInterface $entity): int
    {
        $ids = ['priority' => 'left_id', 'inv_priority' => 'right_id'];
        $query = $this->find()
            ->where([
                $this->aliasField($ids[$priority]) => (int)$entity->get($ids[$priority]),
                $this->aliasField('relation_id') => (int)$entity->get('relation_id'),
            ]);
        $query->select(['max_value' => $query->func()->max($this->aliasField($priority))]);

        return (int)Hash::get($query->toArray(), '0.max_value');
    }
}
