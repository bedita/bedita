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

/**
 * Abstract class for updating relations between BEdita objects.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\ORM\Association\RelatedTo $Association
 */
abstract class UpdateRelatedObjectsAction extends UpdateAssociatedAction
{

    /**
     * {@inheritDoc}
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
            return $data;
        }

        $table = $this->Association->junction();
        $entity = $table->find()
            ->where([$table->getAssociation('Objects')->getForeignKey() => $data['entity']->id])
            ->firstOrFail();
        $relatedEntities = $data['relatedEntities'];
        if (is_array($relatedEntities) && count($relatedEntities) === 1) {
            $relatedEntities = reset($relatedEntities);
        }

        $this->Association = $table->getAssociation('ParentObjects');
        $this->setConfig('association', $this->Association);

        return compact('entity', 'relatedEntities') + $data;
    }
}
