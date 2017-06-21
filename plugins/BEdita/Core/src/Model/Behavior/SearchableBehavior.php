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

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\ORM\Inheritance\Table as InheritanceTable;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\Table;

/**
 * Behavior to add text-based search to model.
 *
 * @since 4.0.0
 */
class SearchableBehavior extends Behavior
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'minLength' => 3,
        'maxWords' => 10,
        'columnTypes' => [
            'string',
            'text',
        ],
        'fields' => [
            '*' => 1,
        ],
        'implementedFinders' => [
            'query' => 'findQuery',
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * If fields or column types are specified - do *not* merge them with existing config,
     * overwrite the fields to search on.
     */
    public function initialize(array $config)
    {
        foreach (['columnTypes', 'fields'] as $key) {
            if (isset($config[$key])) {
                $this->setConfig($key, $config[$key], false);
            }
        }
    }

    /**
     * Get all fields whose column type is amongst those allowed in `columnTypes` configuration key.
     *
     * @param \Cake\ORM\Table $table Table object.
     * @return string[]
     */
    protected function getAllFields(Table $table)
    {
        $columnTypes = $this->getConfig('columnTypes');
        $fields = array_filter( // Filter fields that are of a searchable type.
            $table->getSchema()->columns(),
            function ($column) use ($columnTypes, $table) {
                return in_array($table->getSchema()->columnType($column), $columnTypes);
            }
        );

        if ($table instanceof InheritanceTable && $table->inheritedTable() !== null) {
            // If table inherits from another table, merge parent table's fields.
            $fields = array_merge($fields, $this->getAllFields($table->inheritedTable()));
        }

        return $fields;
    }

    /**
     * Get searchable fields and their priorities.
     *
     * @return array Array where keys are columns, and values are priorities.
     */
    public function getFields()
    {
        $wildCard = $this->getConfig('fields.*');

        $fields = (array)$this->getConfig('fields');
        $allFields = $this->getAllFields($this->getTable());

        $fields = array_intersect_key($fields, array_flip($allFields));
        if ($wildCard !== null) {
            // If wildcard `*` is present, all other fields have default priority.
            $fields += array_diff_key(
                array_fill_keys($allFields, $wildCard),
                $fields
            );
        }

        return $fields;
    }

    /**
     * Finder for query search.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options.
     * @return \Cake\ORM\Query
     */
    public function findQuery(Query $query, array $options)
    {
        if (!isset($options[0]) || !is_string($options[0])) {
            // Bad filter options.
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'query filter requires a non-empty query string',
            ]);
        }

        $minLength = $this->getConfig('minLength');
        $maxWords = $this->getConfig('maxWords');
        $words = array_unique(array_map( // Escape `%`, `_` and `\` characters in words.
            function ($word) {
                return str_replace(
                    ['%', '_', '\\'],
                    ['\\%', '\\_', '\\\\'],
                    mb_strtolower($word)
                );
            },
            array_filter( // Filter out words that are too short.
                preg_split('/\W+/', $options[0]), // Split words.
                function ($word) use ($minLength) {
                    return mb_strlen($word) >= $minLength;
                }
            )
        ));
        if (count($words) === 0) {
            // Query contained only short words.
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'query filter requires a non-empty query string',
            ]);
        }
        if ($maxWords > 0 && count($words) > $maxWords) {
            // Conditions with too many words would make our database hang for a long time.
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'query string too long',
            ]);
        }

        // Concat all fields into a single, lower-cased string.
        $fields = [];
        /* @var \Cake\ORM\Table $table */
        $table = $query->repository();
        foreach (array_keys($this->getFields()) as $field) {
            $fields[] = $query->func()->coalesce([
                $table->aliasField($field) => 'identifier',
                '',
            ]);
            $fields[] = ' '; // Add a spacer.
        }
        array_pop($fields); // Remove last spacer.
        $field = new FunctionExpression('LOWER', [$query->func()->concat($fields)]);

        // Build query conditions.
        return $query
            ->where(function (QueryExpression $exp) use ($field, $words) {
                foreach ($words as $word) {
                    $exp->like(
                        $field,
                        sprintf('%%%s%%', $word)
                    );
                }

                return $exp;
            });
    }
}
