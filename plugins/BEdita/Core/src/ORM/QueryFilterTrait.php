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

namespace BEdita\Core\ORM;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;

/**
 * Query Filter trait.
 *
 * @since 4.0.0
 */
trait QueryFilterTrait
{
    /**
     * Create query filter using various operators on fields
     * Options array must contain fields as keys and operators like
     *   - 'gt' or '>' (greather than)
     *   - 'lt' or '<' (less than),
     *   - 'ge' or '>=' (greater or equal)
     *   - 'le' or '<=' (less or equal) with a date
     *
     * It's also possible to specify an expected value or a list of values for a field
     *
     * Options array examples:
     *
     * ```
     * // field1 greater than 10, field2 less then 5
     * ['field1' => ['gt' => 10], 'field2' => ['lt' => 5]];
     *
     * // field1 in [1, 3, 10]
     * ['field1' => [1, 3, 10]];

     * // field1 equals 10, field2 equals 1
     * ['field1' => 10, 'field1' => ['eq' => 1]];
     *
     * // field1 greater or equal 5, field2 less or equal 4
     * ['field1' => ['>=' => 10], 'field2' => ['<=' => 4]];
     * ```

     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable fields and conditions.
     * @return \Cake\ORM\Query
     */
    public function fieldsFilter(Query $query, array $options)
    {
        return $query->where(function (QueryExpression $exp) use ($options) {
            foreach ($options as $field => $conditions) {
                if ($conditions === null) {
                    $exp = $exp->isNull($field);

                    continue;
                }

                if (!is_array($conditions)) {
                    $exp = $exp->eq($field, $conditions);

                    continue;
                }

                $in = [];
                foreach ($conditions as $operator => $value) {
                    if (is_numeric($operator)) {
                        $in[] = $value;
                        continue;
                    }

                    switch ($operator) {
                        case 'eq':
                        case '=':
                            $exp = $exp->eq($field, $value);
                            break;

                        case 'neq':
                        case 'ne':
                        case '!=':
                        case '<>':
                            $exp = $exp->notEq($field, $value);
                            break;

                        case 'lt':
                        case '<':
                            $exp = $exp->lt($field, $value);
                            break;

                        case 'lte':
                        case 'le':
                        case '<=':
                            $exp = $exp->lte($field, $value);
                            break;

                        case 'gt':
                        case '>':
                            $exp = $exp->gt($field, $value);
                            break;

                        case 'gte':
                        case 'ge':
                        case '>=':
                            $exp = $exp->gte($field, $value);
                    }
                }
                if (!empty($in)) {
                    $exp = $exp->in($field, $in);
                }
            }

            return $exp;
        });
    }
}
