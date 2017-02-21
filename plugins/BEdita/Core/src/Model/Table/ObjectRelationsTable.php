<?php
namespace BEdita\Core\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
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
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('priority')
            ->requirePresence('priority', 'create')
            ->notEmpty('priority');

        $validator
            ->integer('inv_priority')
            ->requirePresence('inv_priority', 'create')
            ->notEmpty('inv_priority');

        $validator
            ->allowEmpty('params');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['left_id'], 'Objects'));
        $rules->add($rules->existsIn(['relation_id'], 'Relations'));
        $rules->add($rules->existsIn(['right_id'], 'Objects'));

        return $rules;
    }
}
