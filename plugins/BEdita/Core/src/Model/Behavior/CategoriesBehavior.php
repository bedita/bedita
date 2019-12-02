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

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Categories behavior
 *
 * @since 4.1.0
 */
class CategoriesBehavior extends Behavior
{
    /**
     * Set categories or tags `id` in save data or
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        if ($entity->get('tags')) {
            $this->prepareData('tags', $entity);
        }
        if ($entity->get('categories')) {
            $this->prepareData('categories', $entity);
        }
    }

    /**
     * Prepare 'categories' or 'tags' data
     *
     * @param string $item Item type, 'tags' or 'categories'
     * @param EntityInterface $entity Entity data
     * @return void
     */
    protected function prepareData(string $item, EntityInterface $entity)
    {
        if (!$entity->isDirty($item)) {
            $entity->unsetProperty($item);

            return;
        }
        // Check if `Tags` or `Categories` associations are enabled
        $objectType = $this->getTable()
                    ->getAssociation('ObjectTypes')
                    ->get($entity->get('type'));
        if (!in_array(Inflector::humanize($item), (array)$objectType->get('associations'))) {
            $entity->unsetProperty($item);

            return;
        }

        $ids = $this->retrieveIds($item, $entity, $objectType);
        $this->updateData($item, $entity, $ids);
    }

    /**
     * Retrieve categories or tags ids from entity data
     *
     * @param string $item Item type, 'tags' or 'categories'
     * @param EntityInterface $entity Entity data
     * @param EntityInterface $objectType Object type entity
     * @return array
     */
    protected function retrieveIds(string $item, EntityInterface $entity, EntityInterface $objectType)
    {
        $data = (array)$entity->get($item);
        $names = Hash::extract($data, '{n}.name');
        $options = compact('names');

        if ($item === 'categories') {
            $options['typeId'] = (int)$objectType->get('id');
        }

        // Invoke `categoriesIds` or `tagsIds` finder
        return TableRegistry::getTableLocator()->get('Categories')
            ->find(sprintf('%sIds', $item), $options)
            ->toArray();
    }

    /**
     * Update entity data from using $ids array.
     * Data are removed if no category or tag has been found.
     *
     * @param string $item Item type, 'tags' or 'categories'
     * @param EntityInterface $entity Entity data
     * @param array $ids Item array with 'name' and 'id'
     * @return void
     */
    protected function updateData(string $item, EntityInterface $entity, array $ids)
    {
        $names = Hash::combine($ids, '{n}.name', '{n}.id');
        $data = (array)$entity->get($item);
        foreach ($data as $k => $value) {
            $name = $value['name'];
            if (!empty($names[$name])) {
                $data[$k]['id'] = $names[$name];
            } else {
                unset($data[$k]);
            }
        }
        $entity->set($item, $data);
    }
}
