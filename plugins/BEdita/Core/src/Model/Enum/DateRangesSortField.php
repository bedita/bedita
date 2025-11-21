<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Model\Enum;

/**
 * DateRangesSortField enum.
 *
 * @since 6.0.0
 */
enum DateRangesSortField: string
{
    use EnumValuesTrait;

    case MIN_START_DATE = 'date_ranges_min_start_date';
    case MAX_START_DATE = 'date_ranges_max_start_date';
    case MIN_END_DATE = 'date_ranges_min_end_date';
    case MAX_END_DATE = 'date_ranges_max_end_date';
}
