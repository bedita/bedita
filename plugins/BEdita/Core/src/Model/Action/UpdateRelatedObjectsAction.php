<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Action;

use BEdita\Core\Model\Entity\Folder;
use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Http\Exception\BadRequestException;
use Cake\Utility\Hash;

/**
 * Abstract class for updating relations between BEdita objects.
 *
 * @since 4.0.0
 * @property \BEdita\Core\ORM\Association\RelatedTo $Association
 */
abstract class UpdateRelatedObjectsAction extends UpdateAssociatedAction
{
    /**
     * @inheritDoc
     */
    public function execute(array $data = [])
    {
        $data = $this->prepareData($data);

        return parent::execute($data);
    }

    /**
     * Get the right entity for the action.
     *
     * For `Folder` entity with `Parents` association changes the point of view
     * using `Tree` entity with `ParentObjects` association assuring to
     * always use a "to one" relation.
     *
     * @param array $data Action data.
     * @return array
     */
    protected function prepareData(array $data)
    {
        if (empty($data['entity']) || !($data['entity'] instanceof Folder) || $this->Association->getName() !== 'Parents') {
            $this->setupPriority($data);

            return $data;
        }

        $relatedEntities = $data['relatedEntities'];
        if (is_array($relatedEntities) && count($relatedEntities) > 1) {
            throw new BadRequestException(__d('bedita', 'Parents association for folders allows at most one related entity'));
        }

        $table = $this->Association->junction();
        $entity = $table->find()
            ->where([$table->getAssociation('Objects')->getForeignKey() => $data['entity']->id])
            ->firstOrFail();
        if (is_array($relatedEntities) && count($relatedEntities) === 1) {
            $relatedEntities = reset($relatedEntities);
        }
        if (!empty($relatedEntities)) {
            /** @var \BEdita\Core\Model\Action\EntityInterface $relatedEntities */
            $joinData = (array)$relatedEntities->get('_joinData');
            // set join data properties in Tree entity, on empty array no properties are set
            $entity->set($joinData);
        }

        $this->Association = $table->getAssociation('ParentObjects');
        $this->setConfig('association', $this->Association);

        return compact('entity', 'relatedEntities') + $data;
    }

    /**
     * Setup `priority` on `_joinData`.
     * If relation is inverse switch `priority` and `inv_priority`.
     *
     * @param array $data Action data.
     * @return void
     */
    protected function setupPriority(array &$data): void
    {
        if (!$this->Association instanceof RelatedTo || !$this->Association->isInverse()) {
            return;
        }

        foreach ($data['relatedEntities'] as $related) {
            $join = (array)$related->get('_joinData');
            $priorities = [
                'inv_priority' => Hash::get($join, 'priority'),
                'priority' => Hash::get($join, 'inv_priority'),
            ];
            $related->set('_joinData', array_filter(array_merge($join, $priorities)));
        }
    }
}
