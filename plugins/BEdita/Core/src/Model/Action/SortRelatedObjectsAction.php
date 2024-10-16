<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Action;

use BEdita\Core\Exception\InvalidDataException;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Log\LogTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;

/**
 * Replace related objects with the same objects ordered by a specific field, with a direction.
 *
 * @since 5.31.0
 */
class SortRelatedObjectsAction extends BaseAction
{
    use LocatorAwareTrait;
    use LogTrait;

    /**
     * {@inheritDoc}
     *
     * Get related objects and sort them.
     * Data must contain:
     * - `entity` (ObjectEntity) the entity of the main object
     * - `field` (string) the field to sort by
     * - `direction` (string) the direction of the sort
     *
     * @throws \BEdita\Core\Exception\InvalidDataException if required data is missing
     */
    public function execute(array $data = [])
    {
        $association = $this->getConfig('association');
        if (!$association instanceof RelatedTo) {
            throw new InvalidDataException(sprintf('Invalid association: %s', $association->getName()));
        }
        $required = ['entity', 'field', 'direction'];
        foreach ($required as $key) {
            if (!array_key_exists($key, $data) || empty($data[$key])) {
                throw new InvalidDataException(sprintf('Missing required key "%s"', $key));
            }
        }

        /** @var \BEdita\Core\Model\Entity\ObjectEntity $entity */
        $entity = Hash::get($data, 'entity');
        $primaryKey = $entity->get('id');
        $field = (string)Hash::get($data, 'field');
        $direction = (string)Hash::get($data, 'direction');
        $action = new ListRelatedObjectsAction(compact('association'));
        $relatedEntities = $action(compact('primaryKey'));
        $relatedEntities = $relatedEntities->toArray();
        usort(
            $relatedEntities,
            function (ObjectEntity $item1, ObjectEntity $item2) use ($field, $direction) {
                $val1 = strtolower((string)$item1->get($field));
                $val2 = strtolower((string)$item2->get($field));

                return $direction === 'asc' ? $val1 <=> $val2 : $val2 <=> $val1;
            }
        );
        $priority = 1;
        foreach ($relatedEntities as &$related) {
            $join = $related->get('_joinData')->toArray();
            $priorities = [
                'priority' => $priority++,
            ];
            $related->set('_joinData', array_filter(array_merge($join, $priorities)));
        }
        $association->junction()->getBehavior('Priority')->setConfig('disabled', true);
        $action = new SetRelatedObjectsAction(compact('association'));
        $action(compact('entity', 'relatedEntities'));
        $association->junction()->getBehavior('Priority')->setConfig('disabled', false);

        return count($relatedEntities);
    }
}
