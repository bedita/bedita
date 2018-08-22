<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Validation\ObjectsValidator;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;

/**
 * Objects Model
 *
 * @property \BEdita\Core\Model\Table\ObjectTypesTable|\Cake\ORM\Association\BelongsTo $ObjectTypes
 * @property \BEdita\Core\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $CreatedByUsers
 * @property \BEdita\Core\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $ModifiedByUsers
 * @property \BEdita\Core\Model\Table\DateRangesTable|\Cake\ORM\Association\HasMany $DateRanges
 * @property \BEdita\Core\Model\Table\FoldersTable|\Cake\ORM\Association\BelongsToMany $Parents
 * @property \BEdita\Core\Model\Table\TreesTable|\Cake\ORM\Association\HasMany $TreeNodes
 * @property \BEdita\Core\Model\Table\TranslationsTable|\Cake\ORM\Association\HasMany $Translations
 *
 * @method \BEdita\Core\Model\Entity\ObjectEntity get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \BEdita\Core\Model\Behavior\UserModifiedBehavior
 * @mixin \BEdita\Core\Model\Behavior\ObjectTypeBehavior
 * @mixin \BEdita\Core\Model\Behavior\RelationsBehavior
 *
 * @since 4.0.0
 */
class ObjectsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    protected $_validatorClass = ObjectsValidator::class;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('objects');
        $this->setEntityClass(ObjectEntity::class);
        $this->setPrimaryKey('id');
        $this->setDisplayField('title');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.DataCleanup');
        $this->addBehavior('BEdita/Core.UserModified');
        $this->addBehavior('BEdita/Core.Relations');
        $this->addBehavior('BEdita/Core.CustomProperties');
        $this->addBehavior('BEdita/Core.UniqueName');
        $this->addBehavior('BEdita/Core.Searchable', [
            'fields' => [
                'title' => 10,
                'description' => 7,
                'body' => 5,
            ],
        ]);

        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.ObjectTypes'
        ]);
        $this->hasMany('DateRanges', [
            'foreignKey' => 'object_id',
            'className' => 'BEdita/Core.DateRanges',
            'saveStrategy' => 'replace',
        ]);
        $this->belongsTo('CreatedByUsers', [
            'foreignKey' => 'created_by',
            'className' => 'BEdita/Core.Users'
        ]);
        $this->belongsTo('ModifiedByUsers', [
            'foreignKey' => 'modified_by',
            'className' => 'BEdita/Core.Users'
        ]);
        $this->belongsToMany('Parents', [
            'className' => 'BEdita/Core.Folders',
            'through' => 'BEdita/Core.Trees',
            'foreignKey' => 'object_id',
            'targetForeignKey' => 'parent_id',
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('TreeNodes', [
            'className' => 'Trees',
            'foreignKey' => 'object_id',
        ]);
        $this->hasMany('Translations', [
            'className' => 'Translations',
            'foreignKey' => 'object_id',
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['uname']));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));
        $rules->add($rules->existsIn(['created_by'], 'CreatedByUsers'));
        $rules->add($rules->existsIn(['modified_by'], 'ModifiedByUsers'));

        return $rules;
    }

    /**
     * Perform checks on abstract and not enabled types.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return bool
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        $objectType = $this->ObjectTypes->get($entity->get('type'));
        if ($objectType->get('is_abstract') || !$objectType->get('enabled')) {
            // Cannot save objects of an abstract type.
            return false;
        }
        $this->checkLangTag($entity);

        return true;
    }

    /**
     * Check `lang` tag using `I18n` configuration.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return void
     * @throws \Cake\Network\Exception\BadRequestException If a wrong lang tag is specified
     */
    protected function checkLangTag(EntityInterface $entity)
    {
        if ($entity->isDirty('lang') && empty($entity->get('lang')) && Configure::check('I18n.default')) {
            $entity->set('lang', Configure::read('I18n.default'));
        }
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('custom_props', 'json');
        $schema->setColumnType('extra', 'json');

        return $schema;
    }

    /**
     * Find by object type.
     *
     * You can pass a list of allowed object types to this finder:
     *
     * ```
     * $table->find('type', [1, 'document', 'profiles', 1004]);
     * ```
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable object types.
     * @return \Cake\ORM\Query
     */
    protected function findType(Query $query, array $options)
    {
        $field = $this->aliasField($this->ObjectTypes->getForeignKey());

        return $query->where(function (QueryExpression $exp) use ($field, $options) {
            $in = [];
            $notIn = [];
            foreach ($options as $key => $value) {
                if (!is_int($key) && !in_array($key, ['eq', '=', 'neq', 'ne', '!=', '<>'], true)) {
                    continue;
                }
                $value = $this->ObjectTypes->get($value);

                $objectTypeIds[] = $value->id;
                if ($value->get('is_abstract')) {
                    $objectTypeIds = array_merge(
                        $objectTypeIds,
                        $this->ObjectTypes
                            ->find('children', ['for' => $value->id])
                            ->find('list', ['valueField' => $this->ObjectTypes->getPrimaryKey()])
                            ->toList()
                    );
                }

                if (in_array($key, ['neq', 'ne', '!=', '<>'], true)) {
                    $notIn = $objectTypeIds;
                } else {
                    $in = array_merge($in, $objectTypeIds);
                }
            }

            if (!empty($in)) {
                $exp = $exp->in($field, $in);
            }
            if (!empty($notIn)) {
                $exp = $exp->notIn($field, $notIn);
            }

            return $exp;
        });
    }

    /**
     * Find by date range using `DateRanges` table findDate filter
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable date range conditions.
     * @return \Cake\ORM\Query
     */
    protected function findDateRanges(Query $query, array $options)
    {
        return $query
            ->distinct([$this->aliasField($this->getPrimaryKey())])
            ->innerJoinWith('DateRanges', function (Query $query) use ($options) {
                return $query->find('dateRanges', $options);
            });
    }

    /**
     * Finder for my objects (i.e.: user created by logged-in user)
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findMine(Query $query)
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->eq($this->aliasField($this->CreatedByUsers->getForeignKey()), LoggedUser::id());
        });
    }

    /**
     * Try to get the object `id` from `uname`.
     *
     * If `$uname` is numeric it returns immediately.
     * else try to find it from `uname` field.
     *
     * @param int|string $uname Unique identifier for the object.
     * @return int
     */
    public function getId($uname)
    {
        if (is_numeric($uname)) {
            return (int)$uname;
        }

        $result = $this->find()
            ->select($this->aliasField('id'))
            ->where([$this->aliasField('uname') => $uname])
            ->enableHydration(false)
            ->firstOrFail();

        return $result['id'];
    }

    /**
     * Finder for objects having a certain `ancestor` on the tree.
     *
     * @param Query $query  Query object instance.
     * @param array $options Id or unique name of ancestor
     * @return \Cake\ORM\Query
     */
    protected function findAncestor(Query $query, array $options)
    {
        $parentId = $this->getId((string)Hash::get($options, '0'));
        $parentNode = $this->TreeNodes->find()
            ->where([
                $this->TreeNodes->aliasField('object_id') => $parentId,
            ])
            ->firstOrFail();

        return $query
            ->innerJoinWith('TreeNodes', function (Query $query) use ($parentNode) {
                return $query->where(function (QueryExpression $exp) use ($parentNode) {
                    return $exp
                        ->gt($this->TreeNodes->aliasField('tree_left'), $parentNode->get('tree_left'))
                        ->lt($this->TreeNodes->aliasField('tree_right'), $parentNode->get('tree_right'));
                });
            })
            ->order($this->TreeNodes->aliasField('tree_left'));
    }

    /**
     * Finder for objects having a certain `parent` on the tree.
     *
     * @param Query $query Query object instance.
     * @param array $options Id or unique name of ancestor
     * @return \Cake\ORM\Query
     */
    protected function findParent(Query $query, array $options)
    {
        $parentId = $this->getId((string)Hash::get($options, '0'));

        return $query
            ->innerJoinWith('TreeNodes', function (Query $query) use ($parentId) {
                return $query->where([
                    $this->TreeNodes->aliasField('parent_id') => $parentId,
                ]);
            })
            ->order($this->TreeNodes->aliasField('tree_left'));
    }

    /**
     * Finder for objects based on status level.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Object status level.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException Throws an exception if an invalid set of options is passed to
     *      the finder.
     */
    protected function findStatus(Query $query, array $options)
    {
        if (count($options) !== 1 || array_keys($options) !== [0]) {
            throw new BadFilterException(__d('bedita', 'Invalid options for finder "{0}"', 'status'));
        }

        $level = reset($options);
        switch ($level) {
            case 'on':
                return $query->where([
                    $this->aliasField('status') => 'on',
                ]);

            case 'draft':
                return $query->where(function (QueryExpression $exp) {
                    return $exp->in($this->aliasField('status'), ['on', 'draft']);
                });

            case 'off':
            case 'all':
                return $query;

            default:
                throw new BadFilterException(__d('bedita', 'Invalid options for finder "{0}"', 'status'));
        }
    }
}
