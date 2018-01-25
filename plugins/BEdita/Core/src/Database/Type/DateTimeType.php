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

namespace BEdita\Core\Database\Type;

use Cake\Database\Type\DateTimeType as CakeDateTimeType;

/**
 * Custom DateTimeType class with simplified marshal
 */
class DateTimeType extends CakeDateTimeType
{
    /**
     * {@inheritDoc}
     *
     * Accepted date time formats are
     *  - 2017-01-01                    YYYY-MM-DD
     *  - 2017-01-01 11:22              YYYY-MM-DD hh:mm
     *  - 2017-01-01T11:22:33           YYYY-MM-DDThh:mm:ss
     *  - 2017-01-01T11:22:33Z          YYYY-MM-DDThh:mm:ssZ
     *  - 2017-01-01T19:20+01:00        YYYY-MM-DDThh:mmTZD
     *  - 2017-01-01T11:22:33+01:00     YYYY-MM-DDThh:mm:ssTZD
     *  - 2017-01-01T19:20:30.45+01:00  YYYY-MM-DDThh:mm:ss.sTZD
     *
     * See ISO 8601 subset as defined here https://www.w3.org/TR/NOTE-datetime:
     */
    public function marshal($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (is_string($value) && preg_match('/^\d{4}(-\d\d(-\d\d([T ]\d\d:\d\d(:\d\d)?(\.\d+)?(([+-]\d\d:\d\d)|Z)?)?)?)?$/i', $value)) {
            /* @var \Cake\I18n\Time|\Cake\I18n\FrozenTime $value */
            $value = call_user_func([$this->getDateTimeClassName(), 'parse'], $value);
            if ($value->getTimezone()->getName() === 'Z') {
                $value = $value->setTimezone('UTC');
            }
        }

        return !empty($value) ? $value : null;
    }
}
