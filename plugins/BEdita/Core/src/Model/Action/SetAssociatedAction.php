<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Action;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;
use InvalidArgumentException;

/**
 * Command to replace all entities associated to another entity.
 *
 * @since 4.0.0
 */
class SetAssociatedAction extends UpdateAssociatedAction
{
    use AssociatedTrait;

    /**
     * Replace existing relations.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entity(-ies).
     * @return int|false Number of updated relationships, or `false` on failure.
     * @throws \RuntimeException Throws an exception if an unsupported association is passed.
     */
    protected function update(EntityInterface $entity, $relatedEntities)
    {
        if ($this->Association instanceof BelongsToMany || $this->Association instanceof HasMany) {
            if ($relatedEntities === null) {
                $relatedEntities = [];
            } elseif (!is_array($relatedEntities)) {
                $relatedEntities = [$relatedEntities];
            }

            $res = $this->toMany($entity, $relatedEntities);
            foreach ($relatedEntities as $relatedEntity) {
                if (
                    $relatedEntity->has('_joinData') &&
                    ($relatedEntity->get('_joinData') instanceof EntityInterface) &&
                    $relatedEntity->get('_joinData')->getErrors()
                ) {
                    throw new BadRequestException([
                        'title' => __d('bedita', 'Error linking entities'),
                        'detail' => $relatedEntity->get('_joinData')->getErrors(),
                    ]);
                }
            }

            return $res;
        }

        if ($relatedEntities === []) {
            $relatedEntities = null;
        }

        if ($relatedEntities !== null && !($relatedEntities instanceof EntityInterface)) {
            throw new InvalidArgumentException(__d('bedita', 'Unable to link multiple entities'));
        }

        if ($this->Association instanceof BelongsTo) {
            return $this->Association->getConnection()->transactional(function () use ($entity, $relatedEntities) {
                return $this->belongsTo($entity, $relatedEntities);
            });
        }

        if ($this->Association instanceof HasOne) {
            return $this->Association->getConnection()->transactional(function () use ($entity, $relatedEntities) {
                return $this->hasOne($entity, $relatedEntities);
            });
        }

        throw new \RuntimeException(__d('bedita', 'Unknown association of type "{0}"', get_class($this->Association)));
    }

    /**
     * Process action for to-many relationships.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface[] $relatedEntities Related entities.
     * @return int|false
     */
    protected function toMany(EntityInterface $entity, array $relatedEntities)
    {
        $relatedEntities = new ArrayObject($relatedEntities);
        $this->dispatchEvent('Associated.beforeSave', compact('entity', 'relatedEntities') + ['action' => 'set', 'association' => $this->Association]);

        // This doesn't need to be in a transaction.
        $relatedEntities = $this->diff($entity, $relatedEntities->getArrayCopy(), true, $affectedEntities);
        $count = count($affectedEntities);

        if ($this->Association instanceof HasMany) {
            if ($this->Association->replace($entity, $relatedEntities, ['atomic' => false]) === false) {
                return false;
            }

            $this->dispatchEvent('Associated.afterSave', compact('entity', 'relatedEntities') + ['action' => 'set', 'association' => $this->Association]);

            return $count;
        }

        if ($this->Association instanceof BelongsToMany) {
            if ($this->Association->replaceLinks($entity, $relatedEntities, ['atomic' => false]) === false) {
                return false;
            }

            $this->dispatchEvent('Associated.afterSave', compact('entity', 'relatedEntities') + ['action' => 'set', 'association' => $this->Association]);

            return $count;
        }

        return false;
    }

    /**
     * Process action for "belongs to" relationships.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface|null $relatedEntity Related entity.
     * @return int|false
     */
    protected function belongsTo(EntityInterface $entity, EntityInterface $relatedEntity = null)
    {
        // `Tree` Entity can be dirty as join data are set in `ParentObjects`
        $dirty = $entity->isDirty();
        $existing = $this->existing($entity);

        $relatedEntities = new ArrayObject([$relatedEntity]);
        $this->dispatchEvent('Associated.beforeSave', compact('entity', 'relatedEntities') + ['action' => 'set', 'association' => $this->Association]);
        $relatedEntity = $relatedEntities[0];

        if ($existing === null && $relatedEntity === null) {
            return 0;
        } elseif (!$dirty && $relatedEntity !== null) {
            $bindingKey = (array)$this->Association->getBindingKey();

            if ($existing !== null && $relatedEntity->extract($bindingKey) == $existing->extract($bindingKey)) {
                return 0;
            }
        }
        $entity->set($this->Association->getProperty(), $relatedEntity);

        if ($this->Association->getSource()->save($entity) === false) {
            return false;
        }

        $relatedEntities = [$relatedEntity];
        $this->dispatchEvent('Associated.afterSave', compact('entity', 'relatedEntities') + ['action' => 'set', 'association' => $this->Association]);

        return 1;
    }

    /**
     * Process action for "has one" relationships.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface|null $relatedEntity Related entity.
     * @return int|false
     */
    protected function hasOne(EntityInterface $entity, EntityInterface $relatedEntity = null)
    {
        $foreignKey = (array)$this->Association->getForeignKey();
        $bindingKeyValue = $entity->extract((array)$this->Association->getBindingKey());
        $existing = $this->existing($entity);

        $relatedEntities = new ArrayObject([$relatedEntity]);
        $this->dispatchEvent('Associated.beforeSave', compact('entity', 'relatedEntities') + ['action' => 'set', 'association' => $this->Association]);
        $relatedEntity = $relatedEntities[0];

        if ($existing === null && $relatedEntity === null) {
            return 0;
        }

        if ($relatedEntity !== null) {
            $primaryKey = (array)$this->Association->getPrimaryKey();

            if ($relatedEntity->extract($primaryKey) == $existing->extract($primaryKey)) {
                return 0;
            }
        }

        $this->Association->getTarget()->updateAll(
            array_combine(
                $foreignKey,
                array_fill(0, count($foreignKey), null)
            ),
            array_combine(
                $foreignKey,
                $bindingKeyValue
            )
        );

        if ($relatedEntity === null) {
            return 0;
        }

        $relatedEntity->set(array_combine(
            $foreignKey,
            $bindingKeyValue
        ));

        if ($this->Association->getTarget()->save($relatedEntity) === false) {
            return false;
        }

        $relatedEntities = [$relatedEntity];
        $this->dispatchEvent('Associated.afterSave', compact('entity', 'relatedEntities') + ['action' => 'set', 'association' => $this->Association]);

        return 1;
    }
}
