<?php
namespace BEdita\Core\Model\Table;

use BEdita\Core\Exception\ImmutableResourceException;
use BEdita\Core\Model\Entity\Tree;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\RulesChecker;
use Cake\ORM\Rule\IsUnique;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Trees Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Objects
 * @property \Cake\ORM\Association\BelongsTo $ParentObjects
 * @property \Cake\ORM\Association\BelongsTo $RootObjects
 * @property \Cake\ORM\Association\BelongsTo $ParentNode
 * @property \Cake\ORM\Association\HasMany $ChildNodes
 *
 * @method \BEdita\Core\Model\Entity\Tree get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Tree newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Tree patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tree findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \BEdita\Core\Model\Behavior\TreeBehavior
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

        $this->addBehavior('BEdita/Core.Tree', [
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
            ->scalar('position')
            ->notEmpty('position')
            ->notEquals('position', 0, null, function ($context) {
                return is_numeric($context['data']['position']);
            })
            ->integer('position', null, function ($context) {
                return is_numeric($context['data']['position']);
            })
            ->inList('position', ['first', 'last'], null, function ($context) {
                return !is_numeric($context['data']['position']);
            });

        $validator
            ->boolean('menu')
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
                'errorField' => 'parent_id',
                'message' => __d('bedita', 'parent_id must be null or corresponding to a folder'),
            ]
        );

        $rules->add(
            [$this, 'isPositionUnique'],
            'isPositionUnique',
            [
                'errorField' => 'object_id',
                'message' => __d('bedita', 'Folders cannot be made ubiquitous, other objects cannot appear twice in the same folder'),
            ]
        );

        return $rules;
    }

    /**
     * Check that `parent_id` property of the `Tree` entity corresponds to a folder
     *
     * @param \BEdita\Core\Model\Entity\Tree $entity The tree entity to validate
     * @return bool
     */
    public function isParentValid(Tree $entity)
    {
        // if parent_id is null then the object_id must refer to a folder (root)
        if ($entity->parent_id === null) {
            return $this->isFolder($entity->object_id);
        }

        return $this->isFolder($entity->parent_id);
    }

    /**
     * Check that a folder position is unique, and other objects' position is unique among their parent.
     *
     * @param \BEdita\Core\Model\Entity\Tree $entity The tree entity to validate.
     * @return bool
     */
    public function isPositionUnique(Tree $entity)
    {
        $rule = new IsUnique(['parent_id', 'object_id']);
        if ($this->isFolder($entity->object_id)) {
            $rule = new IsUnique(['object_id']);
        }

        return $rule($entity, ['repository' => $this]);
    }

    /**
     * Update `root_id` of children if needed.
     *
     * @param \Cake\Event\Event $event The event
     * @param \BEdita\Core\Model\Entity\Tree $entity The entity persisted
     * @return void
     */
    public function afterSave(Event $event, Tree $entity)
    {
        if ($entity->has('position')) {
            if ($this->moveAt($entity, $entity->get('position')) === false) {
                throw new BadRequestException(__d('bedita', 'Invalid position'));
            }
        }

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

    /**
     * Throw an exception when trying to remove a row that points to a folder, unless cascading.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \BEdita\Core\Model\Entity\Tree $entity Tree entity being deleted.
     * @param \ArrayObject $options Options.
     * @return void
     * @throws \BEdita\Core\Exception\ImmutableResourceException Throws an exception when the delete operation would
     *  leave an orphaned folder.
     */
    public function beforeDelete(Event $event, Tree $entity, \ArrayObject $options)
    {
        if (empty($options['_primary'])) {
            return;
        }

        // Refuse to delete a row that points to a folder.
        if ($this->isFolder($entity->object_id)) {
            throw new ImmutableResourceException(__d('bedita', 'This operation would leave an orphaned folder'));
        }
    }

    /**
     * Check if a given ID is the ID of a Folder.
     *
     * @param int $id ID of object being checked.
     * @return bool
     */
    protected function isFolder($id)
    {
        static $foldersType = null;
        if ($foldersType === null) {
            $foldersType = TableRegistry::get('ObjectTypes')->get('folders')->id;
        }

        return $this->Objects->exists([
            $this->Objects->aliasField('object_type_id') => $foldersType,
            $this->Objects->aliasField('id') => $id,
        ]);
    }
}
