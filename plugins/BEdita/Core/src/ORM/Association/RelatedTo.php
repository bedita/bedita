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

namespace BEdita\Core\ORM\Association;

use Cake\ORM\Association\BelongsToMany;

/**
 * Plain extension of {@see \Cake\ORM\Association\BelongsToMany} used to detect relations between BEdita objects.
 *
 * @since 4.0.0
 */
class RelatedTo extends BelongsToMany
{

    /**
     * Get sub-query for matching.
     *
     * @param array $options Options array.
     * @return \Cake\ORM\Query
     */
    public function getSubQueryForMatching(array $options)
    {
        if (!isset($options['conditions'])) {
            $options['conditions'] = [];
        }
        $junction = $this->junction();
        $belongsTo = $junction->association($this->getSource()->getAlias());
        $condition = $belongsTo->_joinCondition(['foreignKey' => $belongsTo->getForeignKey()]);

        $subQuery = $this->find()
            ->select(array_values($condition))
            ->where($options['conditions'])
            ->andWhere($this->junctionConditions());

        if (!empty($options['queryBuilder'])) {
            $subQuery = $options['queryBuilder']($subQuery);
        }

        $assoc = $junction->association($this->getTarget()->getAlias());
        $conditions = $assoc->_joinCondition([
            'foreignKey' => $this->getTargetForeignKey()
        ]);
        $subQuery = $this->_appendJunctionJoin($subQuery, $conditions);

        return $subQuery;
    }
}
