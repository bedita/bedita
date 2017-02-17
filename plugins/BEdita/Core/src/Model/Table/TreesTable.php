<?php
namespace BEdita\Core\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Trees Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Objects
 * @property \Cake\ORM\Association\BelongsTo $ParentTrees
 * @property \Cake\ORM\Association\BelongsTo $Objects
 * @property \Cake\ORM\Association\HasMany $ChildTrees
 *
 * @method \BEdita\Core\Model\Entity\Tree get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Tree newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Tree patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree findOrCreate($search, callable $callback = null, $options = [])
 */
class TreesTable extends Table
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

        $this->table('trees');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
        ]);
        $this->belongsTo('ParentTrees', [
            'className' => 'BEdita/Core.Trees',
            'foreignKey' => 'parent_id'
        ]);
        $this->belongsTo('Objects', [
            'foreignKey' => 'root_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
        ]);
        $this->hasMany('ChildTrees', [
            'className' => 'BEdita/Core.Trees',
            'foreignKey' => 'parent_id'
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
            ->integer('tree_left')
            ->requirePresence('tree_left', 'create')
            ->notEmpty('tree_left');

        $validator
            ->integer('tree_right')
            ->requirePresence('tree_right', 'create')
            ->notEmpty('tree_right');

        $validator
            ->integer('depth_level')
            ->requirePresence('depth_level', 'create')
            ->notEmpty('depth_level');

        $validator
            ->integer('menu')
            ->requirePresence('menu', 'create')
            ->notEmpty('menu');

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
        $rules->add($rules->existsIn(['object_id'], 'Objects'));
        $rules->add($rules->existsIn(['parent_id'], 'ParentTrees'));
        $rules->add($rules->existsIn(['root_id'], 'Objects'));

        return $rules;
    }
}
