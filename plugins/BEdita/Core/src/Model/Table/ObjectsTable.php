<?php
declare(strict_types=1);

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

use BEdita\Core\Exception\LockedResourceException;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Enum\DateRangesSortField;
use BEdita\Core\Model\Enum\ObjectEntityStatus;
use BEdita\Core\Model\Validation\ObjectsValidator;
use BEdita\Core\Search\SimpleSearchTrait;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Type\EnumType;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\I18n\DateTime;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

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
 * @property \BEdita\Core\Model\Table\ObjectPermissionsTable|\Cake\ORM\Association\HasMany $Permissions
 * @property \BEdita\Core\Model\Table\CaptionsTable|\Cake\ORM\Association\HasMany $Captions
 * @method \BEdita\Core\Model\Entity\ObjectEntity get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\ObjectEntity newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectEntity findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \BEdita\Core\Model\Behavior\UserModifiedBehavior
 * @mixin \BEdita\Core\Model\Behavior\ObjectTypeBehavior
 * @mixin \BEdita\Core\Model\Behavior\RelationsBehavior
 * @mixin \BEdita\Core\Model\Behavior\ResourceNameBehavior
 * @mixin \BEdita\Core\Model\Behavior\StatusBehavior
 * @since 4.0.0
 */
class ObjectsTable extends Table
{
    use SimpleSearchTrait;

    /**
     * @inheritDoc
     */
    protected string $_validatorClass = ObjectsValidator::class;

    /**
     * Special sort fields: virtual column names used for custom sort strategies
     * Only related to `DateRanges` for now
     *
     * @var array
     */
    public const DATERANGES_SORT_FIELDS = [
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
        $this->getSchema()
            ->setColumnType('custom_props', 'json')
            ->setColumnType('extra', 'json')
            ->setColumnType('status', EnumType::from(ObjectEntityStatus::class));

        $this->addBehavior('BEdita/Core.ObjectModel');
        $this->addBehavior('BEdita/Core.Categories');
        $this->addBehavior('BEdita/Core.ResourceName', [
            'field' => 'uname',
        ]);

        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.ObjectTypes',
        ]);
        $this->hasMany('DateRanges', [
            'foreignKey' => 'object_id',
            'className' => 'BEdita/Core.DateRanges',
            'sort' => ['start_date' => 'ASC'],
            'saveStrategy' => 'replace',
        ]);
        $this->belongsTo('CreatedByUsers', [
            'foreignKey' => 'created_by',
            'className' => 'BEdita/Core.Users',
        ]);
        $this->belongsTo('ModifiedByUsers', [
            'foreignKey' => 'modified_by',
            'className' => 'BEdita/Core.Users',
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
            'targetForeignKey' => 'tag_id',
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
            'finder' => 'available',
        ]);
        $this->hasMany('Permissions', [
            'className' => 'ObjectPermissions',
            'foreignKey' => 'object_id',
        ]);
        $this->hasMany('Captions', [
            'foreignKey' => 'object_id',
            'className' => 'BEdita/Core.Captions',
            'saveStrategy' => 'replace',
            'sort' => ['id' => 'ASC'],
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
     * @param \Cake\Event\EventInterface $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity): void
    {
        $objectType = $this->ObjectTypes->get($entity->get('type'));
        if ($objectType->get('is_abstract') || !$objectType->get('enabled')) {
            // Cannot save objects of an abstract type.
            $event->setResult(false);

            return;
        }

        /** @var \BEdita\Core\Model\Behavior\StatusBehavior $statusBehavior */
        $statusBehavior = $this->getBehavior('Status');
        $statusBehavior->checkStatus($entity);
        $this->checkLangTag($entity);
        $this->checkLocked($entity);

        $event->setResult(true);
    }

    /**
     * Check `lang` tag using `I18n` configuration.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return void
     */
    protected function checkLangTag(EntityInterface $entity): void
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
     * @throws \BEdita\Core\Exception\LockedResourceException
     */
    protected function checkLocked(EntityInterface $entity): void
    {
        if (empty($entity->get('locked')) || $entity->isDirty('locked')) {
            return;
        }
        if ($entity->isDirty('status') || $entity->isDirty('uname') || $entity->isDirty('deleted')) {
            throw new LockedResourceException(__('Operation not allowed on "locked" objects'));
        }
    }

    /**
     * Find by object type.
     *
     * You can pass a list of allowed object types to this finder:
     *
     * ```
     * $table->find('type', value: [1, 'document', 'profiles', 1004]);
     * ```
     *
     * If `value` named parameter is present it will be used otherwise you can specify the paramters to use.
     * The following are equivalent:
     *
     * ```
     * // example of equivalent signatures
     * $table->find('type', value: 1);
     * $table->find('type', value: ['eq' => 1]);
     * $table->find('type', eq: 1);
     *
     * // other example of equivalent signatures
     * $table->find('type', value: ['ne' => ['documents', 'profiles']]);
     * $table->find('type', ne: ['documents', 'profiles']);
     * ```
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array|string|int $args Named arguments. If `value` is present it will be used.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findType(SelectQuery $query, array|string|int ...$args): SelectQuery
    {
        $filter = $args['value'] ?? $args;
        $field = $this->aliasField($this->ObjectTypes->getForeignKey());

        return $query->where(function (QueryExpression $exp) use ($field, $filter) {
            $in = [];
            $notIn = [];
            foreach ((array)$filter as $key => $value) {
                if (!is_int($key) && !in_array($key, ['eq', '=', 'neq', 'ne', '!=', '<>'], true)) {
                    continue;
                }
                $value = $this->ObjectTypes->get($value);

                $objectTypeIds[] = $value->id;
                if ($value->get('is_abstract')) {
                    $objectTypeIds = array_merge(
                        $objectTypeIds,
                        $this->ObjectTypes
                            ->find('children', for: $value->id)
                            ->find('list', valueField: $this->ObjectTypes->getPrimaryKey())
                            ->all()
                            ->toList(),
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
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array|string|null $start_date start date condition.
     * @param array|string|null $end_date end date condition.
     * @param string|null $from_date from date condition.
     * @param string|null $to_date to date condition.
     * @param \BEdita\Core\Model\Enum\DateRangesSortField|null $sortableField Special sortable field.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findDateRanges(
        SelectQuery $query,
        array|string|null $start_date = null,
        array|string|null $end_date = null,
        ?string $from_date = null,
        ?string $to_date = null,
        ?DateRangesSortField $sortableField = null,
    ): SelectQuery {
        $options = array_filter(compact(
            'start_date',
            'end_date',
            'from_date',
            'to_date',
        ));
        if ($sortableField !== null) {
            return $this->dateRangesSubQueryJoin($query, $sortableField, $options);
        }

        return $query->distinct([$this->aliasField($this->getPrimaryKey())])
            ->innerJoinWith('DateRanges', function (SelectQuery $query) use ($options) {
                return $query->find('dateRanges', ...$options);
            });
    }

    /**
     * Create a date ranges subquery join using special sortable field.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param \BEdita\Core\Model\Enum\DateRangesSortField $minMaxField Special sortable field.
     * @param array $options Array of date range conditions.
     * @return \Cake\ORM\Query\SelectQuery|null
     */
    protected function dateRangesSubQueryJoin(
        SelectQuery $query,
        DateRangesSortField $minMaxField,
        array $options,
    ): ?SelectQuery {
        $finder = 'dateRanges';
        if (empty($options)) {
            $finder = 'all';
        }
        $subQuery = $this->DateRanges->find($finder, ...$options)
            ->select([
                'date_ranges_object_id' => 'object_id',
                DateRangesSortField::MIN_START_DATE->value => $query->func()->min('start_date'),
                DateRangesSortField::MAX_START_DATE->value => $query->func()->max('start_date'),
                DateRangesSortField::MIN_END_DATE->value => $query->func()->min('end_date'),
                DateRangesSortField::MAX_END_DATE->value => $query->func()->max('end_date'),
            ])
            ->groupBy('object_id');

        return $query->distinct([
                $this->aliasField($this->getPrimaryKey()),
                $minMaxField->value,
            ])
            ->innerJoin(
                ['DateBoundaries' => $subQuery],
                ['DateBoundaries.date_ranges_object_id = ' . $this->aliasField($this->getPrimaryKey())],
            );
    }

    /**
     * Finder for my objects (i.e.: user created by logged-in user)
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findMine(SelectQuery $query): SelectQuery
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->eq($this->aliasField($this->CreatedByUsers->getForeignKey()), LoggedUser::id());
        });
    }

    /**
     * Finder for objects having a certain `ancestor` on the tree.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string|int $parent Id or unique name of ancestor
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findAncestor(SelectQuery $query, string|int $parent): SelectQuery
    {
        /** @var \BEdita\Core\Model\Behavior\ResourceNameBehavior $resourceNameBehavior */
        $resourceNameBehavior = $this->getBehavior('ResourceName');
        $parentId = $resourceNameBehavior->getId($parent);
        $parentNode = $this->TreeNodes->find()
            ->where([
                $this->TreeNodes->aliasField('object_id') => $parentId,
            ])
            ->firstOrFail();

        return $query->where(function (QueryExpression $exp) use ($parentNode): QueryExpression {
            return $exp->in(
                $this->aliasField('id'),
                $this->TreeNodes->find()
                    ->select(['object_id'])
                    ->where(function (QueryExpression $exp) use ($parentNode) {
                        return $exp
                            ->gt($this->TreeNodes->aliasField('tree_left'), $parentNode->get('tree_left'))
                            ->lt($this->TreeNodes->aliasField('tree_right'), $parentNode->get('tree_right'));
                    }),
            );
        });
    }

    /**
     * Finder for objects having a certain `parent` on the tree.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string|int $value Id or unique name of ancestor
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findParent(SelectQuery $query, string|int $parent): SelectQuery
    {
        /** @var \BEdita\Core\Model\Behavior\ResourceNameBehavior $resourceNameBehavior */
        $resourceNameBehavior = $this->getBehavior('ResourceName');
        $parentId = $resourceNameBehavior->getId($parent);

        return $query
            ->innerJoinWith('TreeNodes', function (SelectQuery $query) use ($parentId) {
                return $query->where([
                    $this->TreeNodes->aliasField('parent_id') => $parentId,
                ]);
            })
            ->orderBy($this->TreeNodes->aliasField('tree_left'));
    }

    /**
     * Retrieve object translation for a language.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string $lang The translation language.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findTranslations(SelectQuery $query, ?string $lang = null): SelectQuery
    {
        return $query->contain('Translations', function (SelectQuery $query) use ($lang) {
            $query = $query->find('statusLevel', level: Configure::read('Status.level', 'all'));
            if ($lang !== null) {
                $query = $query->where(['Translations.lang' => $lang]);
            }

            return $query;
        });
    }

    /**
     * Finder for available objects based on these rules:
     *  - `status`, `publish_start` and `publish_end` should be acceptable via `findPublishable`
     *  - `deleted` should be 0
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findAvailable(SelectQuery $query): SelectQuery
    {
        return $query->find('publishable')
            ->where([$this->aliasField('deleted') => 0]);
    }

    /**
     * Finder for publishable objects based on these rules:
     *  - `status` should be acceptable checking status 'level' configuration
     *  - `publish_start` and `publish_end` should be acceptable, checking 'Publish.checkDate' configuration
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findPublishable(SelectQuery $query): SelectQuery
    {
        $query = $query->find('statusLevel', level: Configure::read('Status.level', 'all'));
        if ((bool)Configure::read('Publish.checkDate', false)) {
            $query = $query->find('publishDateAllowed');
        }

        return $query;
    }

    /**
     * Finder to check if `publish_start` and `publish_end` dates allow object publishing.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findPublishDateAllowed(SelectQuery $query): SelectQuery
    {
        $now = DateTime::now();

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
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string|int $id ID or uname.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findUnameId(SelectQuery $query, string|int $id): SelectQuery
    {
        if (is_numeric($id)) {
            return $query->where([$this->aliasField('id') => (int)$id]);
        }

        return $query->where([$this->aliasField('uname') => $id]);
    }

    /**
     * Finder for categories by name.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array<string>|string $name Category names.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findCategories(SelectQuery $query, array|string $name): SelectQuery
    {
        return $this->categoriesQuery('Categories', $query, $name);
    }

    /**
     * Finder for tags by name.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array<string>|string $name Tag names.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findTags(SelectQuery $query, array|string $name): SelectQuery
    {
        return $this->categoriesQuery('Tags', $query, $name);
    }

    /**
     * Finder for tags and categories by name.
     * $options array MUST contain a list of category/tag names or a single element with a comma separated list.
     *
     * @param string $assoc Association name, 'Tags' or 'Categories'
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array<string>|string $name Tag or category names.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function categoriesQuery(string $assoc, SelectQuery $query, array|string $name): SelectQuery
    {
        /**
         * If a single element is passed with comma separated values
         * a new array is created fromm it.
         */
        if (is_string($name)) {
            $name = array_filter(explode(',', $name));
        }

        return $query->distinct([$this->aliasField('id')])
            ->innerJoinWith($assoc, function (SelectQuery $query) use ($assoc, $name) {
                return $query->where([sprintf('%s.name IN', $assoc) => $name]);
            });
    }
}
