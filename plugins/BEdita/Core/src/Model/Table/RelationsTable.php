<?php
namespace BEdita\Core\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Relations Model
 *
 * @property \Cake\ORM\Association\HasMany $ObjectRelations
 * @property \Cake\ORM\Association\HasMany $RelationTypes
 *
 * @method \BEdita\Core\Model\Entity\Relation get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Relation newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Relation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation findOrCreate($search, callable $callback = null, $options = [])
 */
class RelationsTable extends Table
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

        $this->table('relations');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->hasMany('ObjectRelations', [
            'foreignKey' => 'relation_id',
            'className' => 'BEdita/Core.ObjectRelations'
        ]);
        $this->hasMany('RelationTypes', [
            'foreignKey' => 'relation_id',
            'className' => 'BEdita/Core.RelationTypes'
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('label', 'create')
            ->notEmpty('label');

        $validator
            ->requirePresence('inverse_name', 'create')
            ->notEmpty('inverse_name')
            ->add('inverse_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('inverse_label', 'create')
            ->notEmpty('inverse_label');

        $validator
            ->allowEmpty('description');

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
        $rules->add($rules->isUnique(['name']));
        $rules->add($rules->isUnique(['inverse_name']));

        return $rules;
    }
}
