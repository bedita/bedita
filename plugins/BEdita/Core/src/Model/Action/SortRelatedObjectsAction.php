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

use BEdita\Core\Model\Entity\ObjectEntity;
use Cake\Log\LogTrait;
use Cake\Utility\Hash;

/**
 * Replace related objects with the same objects ordered by a specific field, with a direction.
 *
 * @since 4.0.0
 */
class SortRelatedObjectsAction extends BaseAction
{
    use LogTrait;

    /**
     * {@inheritDoc}
     *
     * Get related objects and sort them.
     * Data must contain:
     * - `entity` (ObjectEntity) the entity of the main object
     * - `field` (string) the field to sort by
     * - `direction` (string) the direction of the sort
     */
    public function execute(array $data = [])
    {
        $required = ['entity', 'field', 'direction'];
        foreach ($required as $key) {
            if (!array_key_exists($key, $data) || empty($data[$key])) {
                $this->log(sprintf('Missing required key "%s"', $key), 'error');

                return false;
            }
        }
        /** @var \BEdita\Core\Model\Entity\ObjectEntity $entity */
        $entity = Hash::get($data, 'entity');
        $primaryKey = $entity->get('id');
        $field = (string)Hash::get($data, 'field');
        $direction = (string)Hash::get($data, 'direction');
        $association = $this->getConfig('association');
        $action = new ListRelatedObjectsAction(compact('association'));
        $relatedEntities = $action(compact('primaryKey'));
        $relatedEntities = $relatedEntities->toArray();
        usort(
            $relatedEntities,
            function (ObjectEntity $item1, ObjectEntity $item2) use ($field, $direction) {
                $val1 = (string)$item1->get($field);
                $val2 = (string)$item2->get($field);

                return $direction === 'asc' ? $val1 <=> $val2 : $val2 <=> $val1;
            }
        );
        $priority = 1;
        $count = count($relatedEntities);
        foreach ($relatedEntities as &$related) {
            $join = (array)$related->get('_joinData');
            $priorities = [
                'inv_priority' => $count--,
                'priority' => $priority++,
            ];
            $related->set('_joinData', array_filter(array_merge($join, $priorities)));
        }
        $action = new SetRelatedObjectsAction(compact('association'));
        $action(compact('entity', 'relatedEntities'));

        return count($relatedEntities);
    }
}
