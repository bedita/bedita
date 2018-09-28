<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Validation\ObjectTypesValidator;
use BEdita\Core\ORM\Rule\IsUniqueAmongst;
use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Database\Expression\Comparison;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ForbiddenException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * ObjectTypes Model
 *
 * @property \Cake\ORM\Association\HasMany $Objects
 * @property \Cake\ORM\Association\HasMany $Properties
 * @property \Cake\ORM\Association\BelongsToMany $LeftRelations
 * @property \Cake\ORM\Association\BelongsToMany $RightRelations
 *
 * @method \BEdita\Core\Model\Entity\ObjectType newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectType[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectType|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectType[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectType findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 *
 * @since 4.0.0
 */
class ObjectTypesTable extends Table
{
    /**
     * Cache config name for object types.
     *
     * @var string
     */
    const CACHE_CONFIG = '_bedita_object_types_';

    /**
     * Default parent id 1 for `objects`.
     *
     * @var int
     */
    const DEFAULT_PARENT_ID = 1;

    /**
     * Default `plugin` if not specified.
     *
     * @var string
     */
    const DEFAULT_PLUGIN = 'BEdita/Core';

    /**
     * Default `model` if not specified.
     *
     * @var string
     */
    const DEFAULT_MODEL = 'Objects';

    /**
     * {@inheritDoc}
     */
    protected $_validatorClass = ObjectTypesValidator::class;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('object_types');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        $this->hasMany('Objects', [
            'foreignKey' => 'object_type_id',
            'className' => 'Objects',
        ]);

        $this->hasMany('Properties', [
            'foreignKey' => 'property_type_id',
            'className' => 'Properties',
            'dependent' => true,
        ]);

        $through = TableRegistry::get('LeftRelationTypes', ['className' => 'RelationTypes']);
        $this->belongsToMany('LeftRelations', [
            'className' => 'Relations',
            'through' => $through->getRegistryAlias(),
            'foreignKey' => 'object_type_id',
            'targetForeignKey' => 'relation_id',
            'conditions' => [
                $through->aliasField('side') => 'left',
            ],
        ]);
        $through = TableRegistry::get('RightRelationTypes', ['className' => 'RelationTypes']);
        $this->belongsToMany('RightRelations', [
            'className' => 'Relations',
            'through' => $through->getRegistryAlias(),
            'foreignKey' => 'object_type_id',
            'targetForeignKey' => 'relation_id',
            'conditions' => [
                $through->aliasField('side') => 'right',
            ],
        ]);

        $this->belongsTo('Parent', [
            'foreign_key' => 'parent_id',
            'className' => 'ObjectTypes',
        ]);
        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree', [
            'left' => 'tree_left',
            'right' => 'tree_right',
        ]);
        $this->addBehavior('BEdita/Core.Searchable', [
            'fields' => [
                'name' => 10,
                'singular' => 10,
                'description' => 5,
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules
            ->add(new IsUniqueAmongst(['name' => ['name', 'singular']]), '_isUniqueAmongst', [
                'errorField' => 'name',
                'message' => __d('cake', 'This value is already in use'),
            ])
            ->add(new IsUniqueAmongst(['singular' => ['name', 'singular']]), '_isUniqueAmongst', [
                'errorField' => 'singular',
                'message' => __d('cake', 'This value is already in use'),
            ]);

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('associations', 'json');
        $schema->setColumnType('hidden', 'json');

        return $schema;
    }

    /**
     * {@inheritDoc}
     *
     * @return \BEdita\Core\Model\Entity\ObjectType
     */
    public function get($primaryKey, $options = [])
    {
        if (is_string($primaryKey) && !is_numeric($primaryKey)) {
            $allTypes = array_flip(
                $this->find('list')
                    ->cache('map', self::CACHE_CONFIG)
                    ->toArray()
            );
            $allTypes += array_flip(
                $this->find('list', ['valueField' => 'singular'])
                    ->cache('map_singular', self::CACHE_CONFIG)
                    ->toArray()
            );

            $primaryKey = Inflector::underscore($primaryKey);
            if (!isset($allTypes[$primaryKey])) {
                throw new RecordNotFoundException(sprintf(
                    'Record not found in table "%s"',
                    $this->getTable()
                ));
            }

            $primaryKey = $allTypes[$primaryKey];
        }

        if (empty($options)) {
            $options = [
                'key' => sprintf('id_%d_rel', $primaryKey),
                'cache' => self::CACHE_CONFIG,
                'contain' => ['LeftRelations.RightObjectTypes', 'RightRelations.LeftObjectTypes'],
            ];
        }

        return parent::get($primaryKey, $options);
    }

    /**
     * Set default `parent_id`, `plugin` and `model` on creation if missing.
     * Prevent delete if:
     *  - type is abstract and a subtype exists
     *  - is a `core_type`, you may set `enabled` false in this case
     *
     * Controls are performed here insted of `beforeSave()` or `beforeDelete()`
     * in order to be executed before corresponding methods in `TreeBehavior`.
     *
     * @param \Cake\Event\Event $event The event dispatched
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @return void
     * @throws \Cake\Network\Exception\ForbiddenException if operation on entity is not allowed
     */
    public function beforeRules(Event $event, EntityInterface $entity)
    {
        if ($entity->isNew()) {
            if (empty($entity->get('parent_id'))) {
                $entity->set('parent_id', self::DEFAULT_PARENT_ID);
            }
            if (empty($entity->get('table'))) {
                $entity->set('table', self::DEFAULT_PLUGIN . '.' . self::DEFAULT_MODEL);
            }
        }
        if ($event->getData('operation') === 'delete') {
            if ($entity->get('is_abstract') && $this->childCount($entity) > 0) {
                throw new ForbiddenException(__d('bedita', 'Abstract type with existing subtypes'));
            }
            if ($entity->get('core_type')) {
                throw new ForbiddenException(__d('bedita', 'Core types are not removable'));
            }
        }
        if ($entity->isDirty('parent_id') && $this->objectsExist($entity->get('id'))) {
            throw new ForbiddenException(__d('bedita', 'Parent type change forbidden: objects of this type exist'));
        }
    }

    /**
     * Invalidate cache after saving an object type.
     * Recover Nested Set Model tree structure (tree_left, tree_right)
     *
     * @return void
     */
    public function afterSave()
    {
        Cache::clear(false, self::CACHE_CONFIG);
    }

    /**
     * Forbidden operations:
     *  - `is_abstract` set to `true` if at least an object of this type exists
     *  - `is_abstract` set to `false` if a subtype exist.
     *  - `enabled` is set to false and objects of this type or subtypes exist
     *  - `table` is not a valid table model class
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity The entity that is going to be saved
     * @return void
     * @throws \Cake\Network\Exception\ForbiddenException|\Cake\Network\Exception\BadRequestException if entity is not saveable
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        if ($entity->isDirty('is_abstract')) {
            if ($entity->get('is_abstract') && $this->objectsExist($entity->get('id'))) {
                throw new ForbiddenException(__d('bedita', 'Setting as abstract forbidden: objects of this type exist'));
            } elseif (!$entity->get('is_abstract') && $this->childCount($entity) > 0) {
                throw new ForbiddenException(__d('bedita', 'Setting as not abstract forbidden: subtypes exist'));
            }
        }
        if ($entity->isDirty('enabled') && !$entity->get('enabled')) {
            if ($this->objectsExist($entity->get('id'))) {
                throw new ForbiddenException(__d('bedita', 'Type disable forbidden: objects of this type exist'));
            } elseif ($this->childCount($entity) > 0) {
                throw new ForbiddenException(__d('bedita', 'Type disable forbidden: subtypes exist'));
            }
        }
        if ($entity->isDirty('table') && !App::className($entity->get('table'), 'Model/Table', 'Table')) {
            throw new BadRequestException(__d('bedita', '"{0}" is not a valid model table name', [$entity->get('table')]));
        }
    }

    /**
     * Check if objects of a certain type id exist
     *
     * @param int $typeId Object type id
     * @return bool True if at least an object exists, false otherwise
     */
    protected function objectsExist($typeId)
    {
        return TableRegistry::get('Objects')->exists(['object_type_id' => $typeId]);
    }

    /**
     * Don't allow delete actions if at least an object of this type exists.
     *
     * @param \Cake\Event\Event $event The beforeDelete event that was fired
     * @param \Cake\Datasource\EntityInterface $entity The entity that is going to be deleted
     * @return void
     * @throws \Cake\Network\Exception\ForbiddenException if entity is not deletable
     */
    public function beforeDelete(Event $event, EntityInterface $entity)
    {
        if ($this->objectsExist($entity->get('id'))) {
            throw new ForbiddenException(__d('bedita', 'Objects of this type exist'));
        }
    }

    /**
     * Invalidate cache after deleting an object type.
     *
     * @return void
     */
    public function afterDelete()
    {
        Cache::clear(false, self::CACHE_CONFIG);
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(Query $query, array $options)
    {
        return $query->contain(['LeftRelations', 'RightRelations']);
    }

    /**
     * Find object types having a parent by `name` or `id`
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Additional options. The first element containing `id` or `name` is required.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException When missing required parameters.
     */
    public function findParent(Query $query, array $options)
    {
        if (empty($options[0])) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'parent'));
        }
        $parentId = $options[0];
        if (!is_numeric($parentId)) {
            $parentId = $this->get($parentId)->id;
        }

        return $query->where([$this->aliasField('parent_id') => $parentId]);
    }

    /**
     * Find allowed object types by relation name and side.
     *
     * This finder returns a list of object types that are allowed for the
     * relation specified by the required option `name`. You can specify the
     * side of the relation you want to retrieve allowed object types for by
     * passing an additional option `side` (default: `'right'`).
     *
     * If the specified relation name is actually the name of an inverse relation,
     * this finder automatically takes care of "swapping" sides, always returning
     * correct results.
     *
     * ### Example
     *
     * ```php
     * // Find object types allowed on the "right" side:
     * TableRegistry::get('ObjectTypes')
     *     ->find('byRelation', ['name' => 'my_relation']);
     *
     * // Find a list of object type names allowed on the "left" side of the inverse relation:
     * TableRegistry::get('ObjectTypes')
     *     ->find('byRelation', ['name' => 'my_inverse_relation', 'side' => 'left'])
     *     ->find('list')
     *     ->toArray();
     *
     * // Include also descendants of the allowed object types (e.g.: return **Images** whereas **Media** are allowed):
     * TableRegistry::get('ObjectTypes')
     *     ->find('byRelation', ['name' => 'my_relation', 'descendants' => true]);
     * ```
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Additional options. The `name` key is required, while `side` is optional
     *      and assumed to be `'right'` by default.
     * @return \Cake\ORM\Query
     * @throws \LogicException When missing required parameters.
     */
    protected function findByRelation(Query $query, array $options = [])
    {
        if (empty($options['name'])) {
            throw new \LogicException(__d('bedita', 'Missing required parameter "{0}"', 'name'));
        }
        $name = Inflector::underscore($options['name']);

        $leftField = 'inverse_name';
        $rightField = 'name';
        if (!empty($options['side']) && $options['side'] !== 'right') {
            $leftField = 'name';
            $rightField = 'inverse_name';
        }

        // Build sub-queries to find object-types that lay on the left and right side of searched relationship, respectively.
        $leftSubQuery = $this->find()
            ->innerJoinWith('LeftRelations', function (Query $query) use ($name, $leftField) {
                return $query->where(function (QueryExpression $exp) use ($name, $leftField) {
                    return $exp->eq($this->LeftRelations->aliasField($leftField), $name);
                });
            });
        $rightSubQuery = $this->find()
            ->innerJoinWith('RightRelations', function (Query $query) use ($name, $rightField) {
                return $query->where(function (QueryExpression $exp) use ($name, $rightField) {
                    return $exp->eq($this->RightRelations->aliasField($rightField), $name);
                });
            });

        // Conditions builder that filters only object types returned by one of the two sub-queries.
        // This could be achieved more efficiently using two left joins, but if we need to find also
        // descendants it's simpler done this way.
        $conditionsBuilder = function (QueryExpression $exp) use ($leftSubQuery, $rightSubQuery) {
            return $exp->or_(function (QueryExpression $exp) use ($leftSubQuery, $rightSubQuery) {
                return $exp
                    ->in($this->aliasField('id'), $leftSubQuery->select(['id']))
                    ->in($this->aliasField('id'), $rightSubQuery->select(['id']));
            });
        };

        if (!empty($options['descendants'])) { // We don't need only explicitly linked object types, but also their descendants!
            // Obtain Nested-Set-Model left and right counters for the explicitly-linked object types, that are obtained
            // using the `$conditionsBuilder` built before.
            $nsmCounters = $this->find()
                ->select(['tree_left', 'tree_right'])
                ->where($conditionsBuilder)
                ->enableHydration(false)
                ->all();

            // Replace `$conditionsBuilder` with a more complex one that returns not only the matching object types,
            // but also their descendants.
            $conditionsBuilder = function (QueryExpression $exp) use ($nsmCounters) {
                if ($nsmCounters->count() === 0) {
                    // No nodes found: relationship apparently does not exist, or has no linked types.
                    // Add contradiction to force empty results.
                    return $exp->add(new Comparison(1, 1, 'integer', '<>'));
                }

                // Find descendants for all found nodes using NSM rules.
                // If the nodes found are [l = 3, r = 8] and [l = 9, r = 10], the conditions will be built as follows:
                // ... WHERE (tree_left >= 3 AND tree_right <= 8) OR (tree_left >= 9 AND tree_right <= 10)
                return $exp->or_(
                    $nsmCounters
                        ->map(function (array $row) use ($exp) {
                            return $exp->and_(function (QueryExpression $exp) use ($row) {
                                return $exp
                                    ->gte($this->aliasField('tree_left'), $row['tree_left'])
                                    ->lte($this->aliasField('tree_right'), $row['tree_right']);
                            });
                        })
                        ->toArray()
                );
            };
        }

        // Everything is said and done by now. Fingers crossed!
        return $query->where($conditionsBuilder);
    }

    /**
     * Finder to get object type starting from object id or uname.
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Additional options. The `id` key is required.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException When missing required parameters.
     */
    protected function findObjectId(Query $query, array $options = [])
    {
        if (empty($options['id'])) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'id'));
        }

        return $query->innerJoinWith('Objects', function (Query $query) use ($options) {
            return $query->where(function (QueryExpression $exp) use ($options) {
                return $exp->or_([
                    $this->Objects->aliasField('id') => $options['id'],
                    $this->Objects->aliasField('uname') => $options['id'],
                ]);
            });
        });
    }
}
