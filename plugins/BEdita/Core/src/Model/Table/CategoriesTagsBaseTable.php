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

namespace BEdita\Core\Model\Table;

use Cake\Collection\CollectionInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Categories & tags tables base class
 *
 * @since 4.1.0
 */
abstract class CategoriesTagsBaseTable extends Table
{
    /**
     * Common validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     * @codeCoverageIgnore
     */
    public function validationRules(Validator $validator)
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 50)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('label')
            ->maxLength('label', 255)
            ->allowEmptyString('label');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');

        return $validator;
    }

    /**
     * Find enabled items
     *
     * @param Query $query Query object
     * @return Query
     */
    protected function findEnabled(Query $query)
    {
        return $query->where([
            $this->aliasField('enabled') => true,
        ]);
    }

    /**
     * Find categories or tags ids by name
     * $options array MUST contain following keys
     *  - `names`, categories names array
     *  - `typeId`, object typ id (in case of categories, with `_categories` option set)
     *
     * @param Query $query Query object
     * @param array $options Array containing object type id and category names.
     * @return Query
     */
    protected function findIds(Query $query, array $options)
    {
        if (($this->getAlias() === 'Categories') && empty($options['typeId'])) {
            throw new BadRequestException(__d('bedita', 'Missing required parameter "{0}"', 'typeId'));
        }
        if (empty($options['names']) || !is_array($options['names'])) {
            throw new BadRequestException(__d('bedita', 'Missing or wrong required parameter "{0}"', 'names'));
        }

        $conditions = [
            $this->aliasField('enabled') => true,
            $this->aliasField('name') . ' IN' => $options['names'],
        ];

        if ($this->getAlias() === 'Categories') {
            $conditions[] = [$this->aliasField('object_type_id') => (int)$options['typeId']];
        }

        return $query->select(['id', 'name'])
            ->where($conditions);
    }

    /**
     * Remove some categories fields from Query.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return void
     */
    protected function hideFields(Query $query)
    {
        $hidden = [
            'id',
            'object_type_id',
            'object_type_name',
            'parent_id',
            'tree_left',
            'tree_right',
            'enabled',
            'created',
            'modified'
        ];
        $query->formatResults(function (CollectionInterface $results) use ($hidden) {
            return $results->map(function ($row) use ($hidden) {
                if (!empty($row['_joinData'])) {
                    $row['params'] = $row['_joinData']['params'];
                }
                if (empty(!$row)) {
                    $row->setHidden($hidden, true);
                }

                return $row;
            });
        });
    }
}
