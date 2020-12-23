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

namespace BEdita\Core\Utility;

/**
 * Simple timing utilities for profiling.
 */
class Timer
{
    const PRECISION = 3;

    /**
     * Split times
     *
     * @var array
     */
    protected static $splitTimes = [];

    /**
     * Add split time
     *
     * @param string $label Time label.
     * @return void
     */
    public static function addTime(string $label): void
    {
        static::$splitTimes[$label] = microtime(true);
    }

    /**
     * Calculate split times array
     *
     * @return array
     */
    public static function calcSplitTimes(): array
    {
        if (empty(static::$splitTimes) || !defined('TIME_START')) {
            return [];
        }

        return array_map(
            function ($t) {
                return round($t - TIME_START, self::PRECISION, PHP_ROUND_HALF_EVEN);
            },
            static::$splitTimes
        );
    }

    /**
     * Elapsed time
     *
     * @return float
     */
    public static function elapsed(): float
    {
        if (!defined('TIME_START')) {
            return -1;
        }

        return round(microtime(true) - TIME_START, self::PRECISION, PHP_ROUND_HALF_EVEN);
    }
}
