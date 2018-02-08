<?php
namespace BEdita\Core\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Trees Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Objects
 * @property \Cake\ORM\Association\BelongsTo $ParentTrees
 * @property \Cake\ORM\Association\BelongsTo $RootObjects
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
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('trees');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // associations with objects
        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
        ]);
        $this->belongsTo('ParentObjects', [
            'foreignKey' => 'parent_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
        ]);
        $this->belongsTo('RootObjects', [
            'foreignKey' => 'root_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
        ]);

        // associations with trees
        $this->belongsTo('ParentNode', [
            'className' => 'BEdita/Core.Trees',
            'foreignKey' => 'parent_node_id'
        ]);
        $this->hasMany('ChildNodes', [
            'className' => 'BEdita/Core.Trees',
            'foreignKey' => 'parent_node_id'
        ]);

        $this->addBehavior('Tree', [
            'left' => 'tree_left',
            'right' => 'tree_right',
            'parent' => 'parent_node_id',
            'level' => 'depth_level',
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('object_id')
            ->requirePresence('object_id', 'create');

        $validator->boolean('menu')
            ->notEmpty('menu');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['object_id'], 'Objects'));
        $rules->add($rules->existsIn(['root_id'], 'RootObjects'));
        $rules->add($rules->existsIn(
            ['parent_id'],
            'ParentObjects',
            ['allowNullableNulls' => true]
        ));
        $rules->add($rules->existsIn(
            ['parent_node_id'],
            'ParentNode',
            ['allowNullableNulls' => true]
        ));

        // $rules->add(function ($entity, $options) {

        // }, 'isFolderNotUbiquitous');

        return $rules;
    }
}
