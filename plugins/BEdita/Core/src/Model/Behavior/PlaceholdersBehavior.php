<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Exception\LockedResourceException;
use BEdita\Core\Model\Action\SetRelatedObjectsAction;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Association;
use Cake\ORM\Behavior;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use InvalidArgumentException;
use RuntimeException;

/**
 * Placeholders behavior
 *
 * @since 4.3.0
 */
class PlaceholdersBehavior extends Behavior
{
    use LocatorAwareTrait;

    /**
     * The default regex to use to interpolate placeholders data.
     *
     * @var string
     */
    const REGEX = '/<!--\s*BE-PLACEHOLDER\.(?P<id>\d+)(?:\.(?P<params>[A-Za-z0-9+=-]+))?\s*-->/';

    /**
     * Default configurations. Available configurations include:
     *
     * - `relation`: name of the BEdita relation to use.
     * - `fields`: list of fields from which placeholders should be extracted.
     * - `extract`: extract function that will be called on each entity; it will receive
     *      the entity instance and an array of fields as input, and is expected to return
     *      a list of associative arrays with `id` and `params` fields.
     *      If `null`, uses {@see \BEdita\Core\Model\Behavior\PlaceholdersBehavior::extractPlaceholders()}.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'relation' => 'placeholder',
        'fields' => ['description', 'body'],
        'extract' => null,
    ];

    /**
     * Relation instance.
     *
     * @var \BEdita\Core\Model\Entity\Relation|null
     */
    protected $relation = null;

    /**
     * Extract placeholders from an entity.
     *
     * @param EntityInterface $entity The entity from which to extract placeholder references.
     * @param string[] $fields Field names.
     * @return array[] A list of arrays, each with `id` and `params` set.
     */
    public static function extractPlaceholders(EntityInterface $entity, array $fields): array
    {
        $placeholders = [];
        foreach ($fields as $field) {
            $datum = $entity->get($field);
            if (empty($datum)) {
                continue;
            }

            if (!is_string($datum) || preg_match_all(static::REGEX, $datum, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE) === false) {
                throw new RuntimeException(__d('bedita', 'Error extracting placeholders'));
            }

            foreach ($matches as $match) {
                $offsetBytes = $match[0][1]; // This is the offset in bytes!!
                $offset = mb_strlen(substr($datum, 0, $offsetBytes)); // Turn bytes offset into character offset.
                $length = mb_strlen($match[0][0]);
                $id = (int)$match['id'][0];
                $params = null;
                if (!empty($match['params'][0])) {
                    $params = base64_decode($match['params'][0]);
                }

                if (!isset($placeholders[$id])) {
                    $placeholders[$id] = [
                        'id' => $id,
                        'params' => [],
                    ];
                }
                $placeholders[$id]['params'][$field][] = compact('offset', 'length', 'params');
            }
        }

        return array_values($placeholders);
    }

    /**
     * Get Association.
     *
     * @param bool $direct `true` for direct association, or `false` to use inverse.
     * @return \Cake\ORM\Association
     */
    protected function getAssociation(bool $direct): Association
    {
        $relName = $this->getConfigOrFail('relation');
        if (!isset($this->relation)) {
            /** @var \BEdita\Core\Model\Table\RelationsTable $Relations */
            $Relations = $this->getTableLocator()->get('Relations');

            $this->relation = $Relations->get($relName);
        }

        $assoc = $this->relation->alias;
        if ((!$direct && $this->relation->name === $relName) || ($direct && $this->relation->inverse_name === $relName)) {
            $assoc = $this->relation->inverse_alias;
        }

        $association = $this->getTable()->getAssociation($assoc);
        if (!in_array($association->type(), [Association::ONE_TO_MANY, Association::MANY_TO_MANY])) {
            throw new InvalidArgumentException(sprintf('Invalid association type "%s"', get_class($association)));
        }

        return $association;
    }

    /**
     * Add associations using placeholder relation.
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity): void
    {
        $fields = $this->getConfig('fields', []);
        $anyDirty = array_reduce(
            $fields,
            function (bool $isDirty, string $field) use ($entity): bool {
                return $isDirty || $entity->isDirty($field);
            },
            false
        );
        if ($anyDirty === false) {
            // Nothing to do.
            return;
        }

        $association = $this->getAssociation(true);

        $extract = $this->getConfig('extract', [static::class, 'extractPlaceholders']);
        $placeholders = $extract($entity, $fields);
        $relatedEntities = $this->prepareEntities($association->getTarget(), $placeholders);

        $action = new SetRelatedObjectsAction(compact('association'));
        $action(compact('entity', 'relatedEntities'));
    }

    /**
     * Prepare target entities.
     *
     * @param \Cake\ORM\Table $table Target table.
     * @param array[] $placeholders Placeholders data.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function prepareEntities(Table $table, array $placeholders): array
    {
        $pk = $table->getPrimaryKey();
        $ids = array_column($placeholders, 'id');
        if (empty($ids)) {
            return [];
        }

        return $table->find()
            ->select($table->aliasField($pk))
            ->where(function (QueryExpression $exp) use ($table, $pk, $ids): QueryExpression {
                return $exp->in($table->aliasField($pk), $ids);
            })
            ->map(function (EntityInterface $entity) use ($pk, $placeholders): EntityInterface {
                $id = $entity->get($pk);
                foreach ($placeholders as $datum) {
                    if ($datum['id'] == $id) {
                        $entity->set('_joinData', [
                            'params' => $datum['params'],
                        ]);

                        break;
                    }
                }

                return $entity;
            })
            ->toList();
    }

    /**
     * Lock entity from being soft-deleted if it is placeholded somewhere.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity): void
    {
        if (!$entity->isDirty('deleted') || !$entity->get('deleted')) {
            return;
        }

        $this->ensureNotPlaceholded($entity);
    }

    /**
     * Lock entity from being hard-deleted if it is placeholded somewhere.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return void
     */
    public function beforeDelete(Event $event, EntityInterface $entity): void
    {
        $this->ensureNotPlaceholded($entity);
    }

    /**
     * Ensure an entity does not appear as a placeholder.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity being checked.
     * @return void
     *
     * @throws \BEdita\Core\Exception\LockedResourceException
     */
    protected function ensureNotPlaceholded(EntityInterface $entity): void
    {
        $Table = $this->getTable();
        $association = $this->getAssociation(false);

        $refCount = $Table->find()
            ->select(['existing' => 1])
            ->where(array_combine(
                array_map([$Table, 'aliasField'], (array)$Table->getPrimaryKey()),
                $entity->extract((array)$Table->getPrimaryKey())
            ))
            ->innerJoinWith($association->getName())
            ->count();
        if ($refCount === 0) {
            return;
        }

        throw new LockedResourceException(__d(
            'bedita',
            'Cannot delete object {0} because it is still placeholded in {1,plural,=1{one object} other{# objects}}',
            $entity->id,
            $refCount
        ));
    }
}
