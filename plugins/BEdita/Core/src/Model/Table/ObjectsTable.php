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
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
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
     * Special sort fields: virtual column names used for custom sort strategies
     * Only related to `DateRanges` for now
     *
     * @var array
     */
    const DATERANGES_SORT_FIELDS = [
        'date_ranges_min_start_date',
        'date_ranges_max_start_date',
        'date_ranges_min_end_date',
        'date_ranges_max_end_date',
    ];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('objects');
        $this->setEntityClass(ObjectEntity::class);
        $this->setPrimaryKey('id');
        $this->setDisplayField('title');

        $this->addBehavior('BEdita/Core.ObjectModel');
        $this->addBehavior('BEdita/Core.Categories');

        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.ObjectTypes'
        ]);
        $this->hasMany('DateRanges', [
            'foreignKey' => 'object_id',
            'className' => 'BEdita/Core.DateRanges',
            'sort' => ['start_date' => 'ASC'],
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
            'finder' => 'available',
            'cascadeCallbacks' => true,
        ]);
        $this->belongsToMany('Categories', [
            'className' => 'BEdita/Core.Categories',
            'through' => 'BEdita/Core.ObjectCategories',
            'foreignKey' => 'object_id',
            'targetForeignKey' => 'category_id',
            'sort' => ['name' => 'ASC'],
            'finder' => 'enabled',
            'cascadeCallbacks' => true,
        ]);
        $this->belongsToMany('Tags', [
            'className' => 'BEdita/Core.Tags',
            'through' => 'BEdita/Core.ObjectTags',
            'foreignKey' => 'object_id',
            'targetForeignKey' => 'category_id',
            'sort' => ['name' => 'ASC'],
            'finder' => 'enabled',
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
    public function buildRules(RulesChecker $rules): RulesChecker
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
        $this->checkStatus($entity);
        $this->checkLangTag($entity);
        $this->checkLocked($entity);

        return true;
    }

    /**
     * Check `lang` tag using `I18n` configuration.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return void
     * @throws \Cake\Http\Exception\BadRequestException If a wrong lang tag is specified
     */
    protected function checkLangTag(EntityInterface $entity)
    {
        if ($entity->isDirty('lang') && empty($entity->get('lang')) && Configure::check('I18n.default')) {
            $entity->set('lang', Configure::read('I18n.default'));
        }
    }

    /**
     * Check `locked` attribute.
     * If `locked` is true `status`, `uname` and `deleted` cannot be changed.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return void
     * @throws \Cake\Http\Exception\ForbiddenException
     */
    protected function checkLocked(EntityInterface $entity): void
    {
        if (empty($entity->get('locked')) || $entity->isDirty('locked')) {
            return;
        }
        if ($entity->isDirty('status') || $entity->isDirty('uname') || $entity->isDirty('deleted')) {
            throw new ForbiddenException(__('Operation not allowed on "locked" objects'));
        }
    }

    /**
     * Check that `status` is consistent with `Status.level` configuration.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return void
     * @throws \Cake\Http\Exception\BadRequestException
     */
    protected function checkStatus(EntityInterface $entity): void
    {
        if ($entity->isNew() || !Configure::check('Status.level') || !$entity->isDirty('status')) {
            return;
        }
        $level = Configure::read('Status.level');
        $status = $entity->get('status');
        if (($level === 'on' && $status !== 'on') || ($level === 'draft' && $status === 'off')) {
            throw new BadRequestException(__d(
                'bedita',
                'Status "{0}" is not consistent with configured Status.level "{1}"',
                $status,
                $level
            ));
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
        $join = $this->dateRangesSubQueryJoin($query, $options);
        if (!empty($join)) {
            return $join;
        }

        return $query->distinct([$this->aliasField($this->getPrimaryKey())])
                ->innerJoinWith('DateRanges', function (Query $query) use ($options) {
                    return $query->find('dateRanges', $options);
                });
    }

    /**
     * Create a date ranges subquery join if a special sort field is set.
     *
     * @param Query $query Query object instance.
     * @param array $options Array of acceptable date range conditions.
     * @return Query|null
     */
    protected function dateRangesSubQueryJoin(Query $query, array $options): ?Query
    {
        $minMaxField = key(
            array_intersect_key(
                $options,
                array_flip(self::DATERANGES_SORT_FIELDS)
            )
        );
        if (empty($minMaxField)) {
            return null;
        }
        unset($options[$minMaxField]);
        $finder = 'dateRanges';
        if (empty($options)) {
            $finder = 'all';
        }
        $subQuery = $this->DateRanges->find($finder, $options)
            ->select([
                'date_ranges_object_id' => 'object_id',
                'date_ranges_min_start_date' => $query->func()->min('start_date'),
                'date_ranges_max_start_date' => $query->func()->max('start_date'),
                'date_ranges_min_end_date' => $query->func()->min('end_date'),
                'date_ranges_max_end_date' => $query->func()->max('end_date'),
            ])
            ->group('object_id');

        return $query->distinct([
                $this->aliasField($this->getPrimaryKey()),
                $minMaxField,
            ])
            ->innerJoin(
                ['DateBoundaries' => $subQuery],
                ['DateBoundaries.date_ranges_object_id = ' . $this->aliasField($this->getPrimaryKey())]
            );
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
    protected function findStatusLevel(Query $query, array $options)
    {
        if (empty($options[0])) {
            throw new BadFilterException(__d('bedita', 'Invalid options for finder "{0}"', 'status'));
        }

        $level = $options[0];
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

    /**
     * Retrieve object translation for a language.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Lang options.
     * @return \Cake\ORM\Query
     */
    protected function findTranslations(Query $query, array $options)
    {
        return $query->contain('Translations', function (Query $query) use ($options) {
            return $query->where(['Translations.lang' => $options['lang']]);
        });
    }

    /**
     * Finder for available objects based on these rules:
     *  - `status`, `publish_start` and `publish_end` should be acceptable via `findPublishable`
     *  - `deleted` should be 0
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findAvailable(Query $query): Query
    {
        return $query->find('publishable')
            ->where([$this->aliasField('deleted') => 0]);
    }

    /**
     * Finder for publishable objects based on these rules:
     *  - `status` should be acceptable checking 'Status.level' configuration
     *  - `publish_start` and `publish_end` should be acceptable, checking 'Publish.checkDate' configuration
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findPublishable(Query $query): Query
    {
        if (Configure::check('Status.level')) {
            $query = $query->find('statusLevel', [Configure::read('Status.level')]);
        }
        if ((bool)Configure::read('Publish.checkDate', false)) {
            $query = $query->find('publishDateAllowed');
        }

        return $query;
    }

    /**
     * Finder to check if `publish_start` and `publish_end` dates allow object publishing.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findPublishDateAllowed(Query $query): Query
    {
        $now = $query->func()->now();

        return $query->where(function (QueryExpression $exp) use ($now) {
            return $exp->and([
                $exp->or(function (QueryExpression $exp) use ($now) {
                    $field = $this->aliasField('publish_start');

                    return $exp
                        ->isNull($field)
                        ->lte($field, $now);
                }),
                $exp->or(function (QueryExpression $exp) use ($now) {
                    $field = $this->aliasField('publish_end');

                    return $exp
                        ->isNull($field)
                        ->gte($field, $now);
                }),
            ]);
        });
    }

    /**
     * Finder to get an object by ID or 'uname'
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array with ID or uname as first element.
     * @return \Cake\ORM\Query
     */
    protected function findUnameId(Query $query, array $options)
    {
        $id = (string)Hash::get($options, '0');
        if (is_numeric($id)) {
            return $query->where([$this->aliasField('id') => (int)$id]);
        }

        return $query->where([$this->aliasField('uname') => $id]);
    }

    /**
     * Finder for categories by name.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Category names.
     * @return \Cake\ORM\Query
     */
    protected function findCategories(Query $query, array $options)
    {
        return $this->categoriesQuery('Categories', $query, $options);
    }

    /**
     * Finder for tags by name.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Tag names.
     * @return \Cake\ORM\Query
     */
    protected function findTags(Query $query, array $options)
    {
        return $this->categoriesQuery('Tags', $query, $options);
    }

    /**
     * Finder for tags and categories by name.
     * $options array MUST contain a list of category/tag names or a single element with a comma separated list.
     *
     * @param string $assoc Association name, 'Tags' or 'Categories'
     * @param Query $query Query object instance.
     * @param array $options Tag or category names.
     * @return Query
     */
    protected function categoriesQuery(string $assoc, Query $query, array $options)
    {
        /**
         * If a single element is passed with comma separated values
         * a new array is created fromm it.
         */
        if (count($options) === 1) {
            $options = array_filter(explode(',', reset($options)));
        }

        return $query->distinct()->innerJoinWith($assoc, function (Query $query) use ($assoc, $options) {
            return $query->where([sprintf('%s.name IN', $assoc) => $options]);
        });
    }
}
