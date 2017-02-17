<?php
namespace BEdita\Core\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ObjectRelations Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Objects
 * @property \Cake\ORM\Association\BelongsTo $Relations
 * @property \Cake\ORM\Association\BelongsTo $Objects
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
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('object_relations');
        $this->displayField('left_id');
        $this->primaryKey(['left_id', 'relation_id', 'right_id']);

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
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
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
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['left_id'], 'Objects'));
        $rules->add($rules->existsIn(['relation_id'], 'Relations'));
        $rules->add($rules->existsIn(['right_id'], 'Objects'));

        return $rules;
    }
}
