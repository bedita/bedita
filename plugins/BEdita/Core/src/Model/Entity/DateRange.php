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

namespace BEdita\Core\Model\Entity;

use Cake\ORM\Entity;

/**
 * DateRange Entity
 *
 * @property int $id
 * @property int $object_id
 * @property \Cake\I18n\Time $start_date
 * @property \Cake\I18n\Time|null $end_date
 * @property array $params
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 */
class DateRange extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'id',
        'object_id',
    ];

    /**
     * Check if this Date Range is before the passed Date Range.
     *
     * A Date Range is "before" another Date Range if its `end_date` is lower than,
     * or equal, to the other Date Range's `start_date`. If `end_date` is `null`,
     * for this purpose it assumes the same value as `start_date`.
     *
     * **Warning**: this method **does not** take `params` into account.
     *
     * @param \BEdita\Core\Model\Entity\DateRange $dateRange Date Range being compared.
     * @return bool
     */
    public function isBefore(DateRange $dateRange)
    {
        return $this->start_date < $dateRange->start_date && ($this->end_date === null || $this->end_date <= $dateRange->start_date);
    }

    /**
     * Check if this Date Range is after the passed Date Range.
     *
     * A Date Range is "after" another Date Range if its `start_date` is greater than,
     * or equal, to the other Date Range's `end_date`. If `end_date` is `null`,
     * for this purpose it assumes the same value as `start_date`.
     *
     * **Warning**: this method **does not** take `params` into account.
     *
     * @param \BEdita\Core\Model\Entity\DateRange $dateRange Date Range being compared.
     * @return bool
     */
    public function isAfter(DateRange $dateRange)
    {
        return $this->start_date > $dateRange->start_date && ($dateRange->end_date === null || $this->start_date >= $dateRange->end_date);
    }

    /**
     * Normalize an array of Date Ranges by sorting and joining overlapping Date Ranges.
     *
     * Normalization sorts Date Ranges in a set by `start_date` in ascending order.
     * Also, if two or more Date Ranges do overlap, or are adjacent
     * (i.e. `$d1->end_date === $d2->start_date`), they are merged in one Date Range.
     * Duplicate Date Ranges are removed.
     *
     * **Warning**: this method **does not** take `params` into account.
     *
     * @param \BEdita\Core\Model\Entity\DateRange[] $dateRanges Set of Date Ranges.
     * @return \BEdita\Core\Model\Entity\DateRange[]
     */
    public static function normalize(array $dateRanges)
    {
        if (empty($dateRanges)) {
            return [];
        }

        // Sort items.
        usort($dateRanges, function (DateRange $dateRange1, DateRange $dateRange2) {
            if ($dateRange1->isBefore($dateRange2)) {
                return -1;
            }
            if ($dateRange1->isAfter($dateRange2)) {
                return 1;
            }

            return 0;
        });

        // Merge items.
        $result = [];
        $last = array_shift($dateRanges);
        while (($current = array_shift($dateRanges)) !== null) {
            if ($last->isBefore($current) && $last->end_date < $current->start_date) {
                $result[] = $last;
                $last = $current;

                continue;
            }

            $last->start_date = $last->start_date->min($current->start_date);
            if ($last->end_date === null || ($current->end_date !== null && $last->end_date < $current->end_date)) {
                $last->end_date = $current->end_date;
            }
        }
        $result[] = $last;

        return $result;
    }

    /**
     * Compute difference between two sets of Date Ranges.
     *
     * When computing complement of `$array1` with respect to `$array2`:
     *  - Date Ranges with `end_date = null` are treated as unit sets, all
     *    other Date Ranges are considered intervals.
     *  - complement of an interval with respect to another interval results
     *    in the difference of the two sets.
     *  - complement of an interval with respect to a unit set results in
     *    the interval unmodified.
     *  - complement of a unit sets with respect to an interval results in
     *    either the unit set unmodified if they are not overlapping, or in
     *    the empty set otherwise.
     *  - complement of a unit sets with respect to another unit set results
     *    in either the unit set unmodified if they are not the same, or in
     *    the empty set otherwise.
     *
     * **Warning**: this method does **not** take `params` into account.
     *
     * @param \BEdita\Core\Model\Entity\DateRange[] $array1 First set of Date Ranges.
     * @param \BEdita\Core\Model\Entity\DateRange[] $array2 Second set of Date Ranges.
     * @return \BEdita\Core\Model\Entity\DateRange[]
     */
    public static function diff(array $array1, array $array2)
    {
        // Ensure arrays are normalized.
        $array1 = static::normalize($array1);
        $array2 = static::normalize($array2);

        $result = [];
        $dateRange = null;
        foreach ($array1 as $dateRange1) {
            if ($dateRange !== null) {
                $result[] = $dateRange;
            }
            $dateRange = clone $dateRange1;

            while (($dateRange2 = current($array2)) !== false) {
                if ($dateRange->end_date === null && $dateRange2->end_date === null && $dateRange->start_date == $dateRange2->start_date) {
                    // Unit sets match. Discard range.
                    $dateRange = null;
                    next($array2);

                    break;
                }
                if ($dateRange2->end_date === null || $dateRange2->isBefore($dateRange)) {
                    // Does not affect intersection.
                    next($array2);

                    continue;
                }
                if ($dateRange2->isAfter($dateRange)) {
                    // A step too far.
                    break;
                }
                if ($dateRange->start_date < $dateRange2->start_date) {
                    // Split the range.
                    $temp = clone $dateRange;
                    $temp->end_date = $dateRange2->start_date;
                    $result[] = $temp;

                    $dateRange->start_date = $dateRange2->start_date;
                }
                if ($dateRange->end_date < $dateRange2->end_date) {
                    // Discard range.
                    $dateRange = null;

                    break;
                }

                $dateRange->start_date = $dateRange2->end_date;

                next($array2);
            }
        }

        if ($dateRange !== null) {
            $result[] = $dateRange;
        }

        return $result;
    }
}
