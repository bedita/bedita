<?php
namespace BEdita\Core\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
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
        ]);
        $this->belongsTo('ParentObjects', [
            'foreignKey' => 'parent_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Folders'
        ]);
        $this->belongsTo('RootObjects', [
            'foreignKey' => 'root_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Folders'
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

        $rules->add(
            [$this, 'isParentValid'],
            'isParentValid',
            [
                'message' => __d('bedita', 'Parent {0} must be a folder or null', $entity->parent_id),
            ]
        );

        return $rules;
    }

    /**
     * Check that `parent_id` property of the entity corresponds to a folder
     *
     * @param EntityInterface $entity The entity to validate
     * @return bool
     */
    public function isParentValid(EntityInterface $entity)
    {
        if ($entity->parent_id === null) {
            return true;
        }

        $table = TableRegistry::get('Objects');
        $isParentFolder = $table->find()
            ->where([
                $table->aliasField('id') => $entity->parent_id,
            ])
            ->innerJoinWith('ObjectTypes', function (Query $query) {
                return $query->where(['ObjectTypes.name' => 'folders']);
            })
            ->count();

        return $isParentFolder === 1;
    }

    /**
     * Update `root_id` of children if needed.
     *
     * @param Event $event The event
     * @param EntityInterface $entity The entity persisted
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity)
    {
        if ($entity->isNew()) {
            return;
        }

        // update root_id
        $this->updateAll(
            ['root_id' => $entity->root_id],
            [
                'tree_left >' => $entity->tree_left,
                'tree_right <' => $entity->tree_right,
                'root_id !=' => $entity->root_id,
            ]
        );
    }
}
