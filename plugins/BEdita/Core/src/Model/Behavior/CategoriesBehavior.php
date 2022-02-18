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

use BEdita\Core\Model\Entity\Tag;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Categories and Tags behavior
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
     * @param \Cake\Datasource\EntityInterface $entity Entity data
     * @return void
     */
    protected function prepareData(string $item, EntityInterface $entity): void
    {
        if (!$entity->isDirty($item)) {
            unset($entity[$item]);

            return;
        }
        // Check if `Tags` or `Categories` associations are enabled
        $objectType = $this->getTable()
            ->getAssociation('ObjectTypes')
            ->get($entity->get('type'));
        if (!in_array(Inflector::humanize($item), (array)$objectType->get('associations'))) {
            unset($entity[$item]);

            return;
        }

        if ($item === 'categories') {
            $entities = $this->fetchCategories($entity, $objectType);
        } else {
            $entities = $this->fetchTags($entity);
        }
        $this->updateData($item, $entity, $entities);
    }

    /**
     * Fetch categories with id from entity data
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity data
     * @param \Cake\Datasource\EntityInterface $objectType Object type entity
     * @return array
     */
    protected function fetchCategories(EntityInterface $entity, EntityInterface $objectType): array
    {
        $data = (array)$entity->get('categories');
        $options = [
            'names' => Hash::extract($data, '{n}.name'),
            'typeId' => (int)$objectType->get('id'),
        ];

        return TableRegistry::getTableLocator()->get('Categories')
                ->find('ids', $options)
                ->toArray();
    }

    /**
     * Fetch tags with id from entity.
     * Create new tag if no existing tag is found.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity data
     * @return array
     */
    protected function fetchTags(EntityInterface $entity): array
    {
        return array_filter(array_map(
            function ($item) {
                return $this->checkTag($item);
            },
            (array)$entity->get('tags')
        ));
    }

    /**
     * Check if tag exists
     *  - the existing Tag is returned if found
     *  - a new Tag is created if missing
     *  - NULL is returned if Tag exists but is disabled
     *
     * @param \BEdita\Core\Model\Entity\Tag $item Tag entity to check or create
     * @return \BEdita\Core\Model\Entity\Tag|null
     */
    protected function checkTag(Tag $item): ?Tag
    {
        $Tags = TableRegistry::getTableLocator()->get('Tags');
        $name = Hash::get($item, 'name');
        /** @var \BEdita\Core\Model\Entity\Tag|null $tag */
        $tag = $Tags->find()
            ->where([$Tags->aliasField('name') => $name])
            ->first();

        if (empty($tag)) {
            $label = Hash::get($item, 'label', Inflector::humanize($name));
            $tag = $Tags->newEntity(compact('name', 'label'));

            return $Tags->saveOrFail($tag);
        }

        if ($tag->get('enabled')) {
            return $tag;
        }

        return null;
    }

    /**
     * Update entity data from using tag or category entities array.
     * Data are removed if no category or tag has been found.
     *
     * @param string $item Item type, 'tags' or 'categories'
     * @param \Cake\Datasource\EntityInterface $entity Entity data
     * @param array $entities Entities array with 'name' and 'id'
     * @return void
     */
    protected function updateData(string $item, EntityInterface $entity, array $entities): void
    {
        $names = Hash::combine($entities, '{n}.name', '{n}.id');
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
