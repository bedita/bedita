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

use Cake\Datasource\EntityInterface;

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
     *
     * @return \Cake\ORM\Query
     */
    protected function existing(EntityInterface $entity)
    {
        $jointTable = $this->Association->junction();

        $sourcePrimaryKey = $entity->extract((array)$this->Association->getSource()->getPrimaryKey());
        $foreignKey = array_map([$jointTable, 'aliasField'], (array)$this->Association->getForeignKey());

        $conditionsPrefix = sprintf('%s.', $jointTable->getAlias());
        $conditions = array_filter(
            $this->Association->getConditions(),
            function ($key) use ($conditionsPrefix) {
                // Filter only conditions that apply to junction table.
                return substr($key, 0, strlen($conditionsPrefix)) === $conditionsPrefix;
            },
            ARRAY_FILTER_USE_KEY
        );

        $existing = $jointTable->find()
            ->where($conditions)
            ->andWhere(array_combine($foreignKey, $sourcePrimaryKey));

        return $existing;
    }
}
