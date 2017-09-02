<?php
namespace BEdita\Core\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use League\JsonGuard\Validator as JsonSchemaValidator;

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
                return static::jsonSchema(null, $context) !== true;
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

        $value = json_decode(json_encode($value));
        $validator = new JsonSchemaValidator($value, $context['providers']['jsonSchema']);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $error = reset($errors);

            return sprintf('%s (in: %s)', $error->getMessage(), $error->getDataPath());
        }

        return true;
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
}
