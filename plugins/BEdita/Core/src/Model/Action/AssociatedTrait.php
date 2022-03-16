<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Action;

use Cake\Datasource\EntityInterface;
use Cake\Event\EventDispatcherTrait;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Association\BelongsToMany;

/**
 * Trait to help with operations on and with associated entities.
 *
 * @since 4.0.0
 * @property-read \Cake\ORM\Association\BelongsToMany|\Cake\ORM\Association\HasMany $Association
 */
trait AssociatedTrait
{
    use EventDispatcherTrait;

    /**
     * Find entity among list of entities.
     *
     * @param \Cake\Datasource\EntityInterface $needle Entity being searched.
     * @param \Cake\Datasource\EntityInterface[] $haystack List of entities.
     * @return \Cake\Datasource\EntityInterface|null
     */
    protected function findMatchingEntity(EntityInterface $needle, array $haystack)
    {
        $bindingKey = (array)$this->Association->getBindingKey();
        foreach ($haystack as $candidate) {
            $found = array_reduce(
                $bindingKey,
                function ($found, $field) use ($candidate, $needle) {
                    return $found && $candidate->get($field) === $needle->get($field);
                },
                true
            );

            if ($found) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Compute set-theory intersection between multiple sets of entities.
     *
     * @param \Cake\Datasource\EntityInterface[] ...$entities Lists of entities.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function intersection(array ...$entities)
    {
        $setA = array_shift($entities);
        foreach ($entities as $setB) {
            $setA = array_filter(
                $setA,
                function (EntityInterface $item) use ($setB) {
                    return $this->findMatchingEntity($item, $setB) !== null;
                }
            );
        }

        return $setA;
    }

    /**
     * Compute set-theory difference between multiple sets of entities.
     *
     * @param \Cake\Datasource\EntityInterface[] ...$entities Lists of entities.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function difference(array ...$entities)
    {
        $setA = array_shift($entities);
        foreach ($entities as $setB) {
            $setA = array_filter(
                $setA,
                function (EntityInterface $item) use ($setB) {
                    return $this->findMatchingEntity($item, $setB) === null;
                }
            );
        }

        return $setA;
    }

    /**
     * Sort an array by copying order from an array that holds analogous elements.
     *
     * @param \Cake\Datasource\EntityInterface[] $array Array to sort.
     * @param \Cake\Datasource\EntityInterface[] $original Array to copy original order from.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function sortByOriginalOrder(array $array, array $original)
    {
        $original = array_values($original);
        usort(
            $array,
            function (EntityInterface $a, EntityInterface $b) use ($original) {
                $originalA = $this->findMatchingEntity($a, $original);
                $originalB = $this->findMatchingEntity($b, $original);

                $idxA = array_search($originalA, $original);
                $idxB = array_search($originalB, $original);

                return $idxA - $idxB;
            }
        );

        return $array;
    }

    /**
     * Find existing associations.
     *
     * @param \Cake\Datasource\EntityInterface $source Source entity.
     * @return \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null
     */
    protected function existing(EntityInterface $source)
    {
        if (!$source->has($this->Association->getProperty())) {
            $this->Association->getSource()->loadInto($source, [$this->Association->getName()]);
        }

        return $source->get($this->Association->getProperty());
    }

    /**
     * Helper method to get extra fields to be set on junction table, derived from Association's conditions.
     *
     * @param \Cake\Datasource\EntityInterface $source Source entity.
     * @param \Cake\Datasource\EntityInterface $target Target entity.
     * @return array
     */
    protected function getJunctionExtraFields(EntityInterface $source, EntityInterface $target)
    {
        $conditions = $this->Association->getConditions();
        $prefix = sprintf('%s.', $this->Association->junction()->getAlias());
        $extraFields = [];
        foreach ($conditions as $field => $value) {
            if (substr($field, 0, strlen($prefix)) !== $prefix) {
                continue;
            }
            $field = substr($field, strlen($prefix));

            $extraFields[$field] = $value;
        }

        $extraFields += array_combine(
            (array)$this->Association->getForeignKey(),
            $source->extract((array)$this->Association->getSource()->getPrimaryKey())
        );
        $extraFields += array_combine(
            (array)$this->Association->getTargetForeignKey(),
            $target->extract((array)$this->Association->getTarget()->getPrimaryKey())
        );

        return $extraFields;
    }

    /**
     * Ensure join data is hydrated.
     *
     * @param \Cake\Datasource\EntityInterface $source Source entity.
     * @param \Cake\Datasource\EntityInterface $target Target entity.
     * @return \Cake\Datasource\EntityInterface
     */
    protected function hydrateLink(EntityInterface $source, EntityInterface $target)
    {
        if (!($this->Association instanceof BelongsToMany)) {
            return $target;
        }

        $data = $target->get('_joinData');
        $joinData = $this->Association->junction()->newEntity([]);
        if ($data instanceof EntityInterface) {
            $joinData = $data;
            $data = [];
        }

        $joinData->set($this->getJunctionExtraFields($source, $target), ['guard' => false]);

        // ensure that if source was not linked to target through joinData the join entity is marked as new
        // foreign key corresponds to source primary key
        $fk = $this->Association->getForeignKey();
        if (!$joinData->isNew() && !empty($joinData->extractOriginalChanged([$fk]))) {
            $joinData->setNew(true);
        }

        $this->Association->junction()->patchEntity($joinData, $data ?: []);
        $errors = $joinData->getErrors();
        if (!empty($errors)) {
            throw new BadRequestException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => $errors,
            ]);
        }

        $target->set('_joinData', $joinData);

        return $target;
    }

    /**
     * Patch an existing link entity if the link itself needs to be updated.
     *
     * @param \Cake\Datasource\EntityInterface $source Source entity.
     * @param \Cake\Datasource\EntityInterface $existing Existing link.
     * @param \Cake\Datasource\EntityInterface $new New link data.
     * @return \Cake\Datasource\EntityInterface|false
     */
    protected function patchLink(EntityInterface $source, EntityInterface $existing, EntityInterface $new)
    {
        if (!($this->Association instanceof BelongsToMany)) {
            return false;
        }

        $existingJoin = $existing->get('_joinData');
        $newJoin = $new->get('_joinData');
        if ($newJoin === null) {
            return false;
        }
        if ($newJoin instanceof EntityInterface) {
            $newJoin = $newJoin->toArray();
        }
        $newJoin = array_diff_key(
            $newJoin,
            array_flip([$this->Association->getForeignKey(), $this->Association->getTargetForeignKey()])
        );

        $data = [];
        foreach ($newJoin as $field => $value) {
            if ($existingJoin->get($field) !== $value) {
                $data[$field] = $value;
            }
        }
        if (empty($data)) {
            return false;
        }

        $existingJoin->set($this->getJunctionExtraFields($source, $new), ['guard' => false]);
        $existingJoin = $this->Association->junction()->patchEntity($existingJoin, $data);
        $errors = $existingJoin->getErrors();
        if (!empty($errors)) {
            throw new BadRequestException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => $errors,
            ]);
        }

        return $existing;
    }

    /**
     * Compute difference for the current operation.
     *
     * @param \Cake\Datasource\EntityInterface $source Source entity.
     * @param \Cake\Datasource\EntityInterface[] $targetEntities Target entities.
     * @param bool $replace Is this a full-replacement operation?
     * @param \Cake\Datasource\EntityInterface[] $affected Entities affected by this operation.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function diff(EntityInterface $source, array $targetEntities, $replace, &$affected = [])
    {
        $existing = (array)$this->existing($source);
        $kept = $this->intersection($existing, $targetEntities);

        $added = array_map(
            function (EntityInterface $target) use ($source) {
                return $this->hydrateLink($source, $target);
            },
            $this->difference($targetEntities, $existing)
        );
        $changed = array_filter(
            array_map(
                function (EntityInterface $existing) use ($source, $targetEntities) {
                    $relatedEntity = $this->findMatchingEntity($existing, $targetEntities);

                    return $this->patchLink($source, $existing, $relatedEntity);
                },
                $kept
            )
        );
        $affected = $diff = array_merge($added, $changed);

        if ($replace === true) {
            $unchanged = $this->difference($kept, $added, $changed);
            $deleted = $this->difference($existing, $targetEntities);

            $affected = array_merge($affected, $deleted);
            $diff = array_merge($diff, $unchanged);
        }

        $diff = $this->sortByOriginalOrder($diff, $targetEntities);

        return $diff;
    }
}
