<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\ORM\Rule;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Rule\IsUnique;

/**
 * Check that each of a list of fields from an entity are unique among all other fields.
 *
 * @since 4.0.0
 */
class IsUniqueAmongst extends IsUnique
{

    /**
     * Build uniqueness conditions.
     *
     * @param string $alias The alias to add.
     * @param array $extracted Extracted fields.
     * @return array
     */
    protected function buildConditions($alias, $extracted)
    {
        $conditions = [];
        foreach ($extracted as $field => $value) {
            foreach ($this->_fields[$field] as $uniqueCheck) {
                $uniqueCheck .= ' IN';
                if (!isset($conditions[$uniqueCheck])) {
                    $conditions[$uniqueCheck] = [];
                }
                $conditions[$uniqueCheck][] = $value;
            }
        }

        return [
            'OR' => $this->_alias($alias, $conditions, true),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(EntityInterface $entity, array $options)
    {
        if (!$entity->extract(array_keys($this->_fields), true)) {
            return true;
        }

        $alias = $options['repository']->alias();
        $conditions = $this->buildConditions($alias, $entity->extract(array_keys($this->_fields)));
        if ($entity->isNew() === false) {
            $keys = (array)$options['repository']->primaryKey();
            $keys = $this->_alias($alias, $entity->extract($keys), true);
            if (array_filter($keys, 'strlen')) {
                $conditions['NOT'] = $keys;
            }
        }

        return !$options['repository']->exists($conditions);
    }
}
