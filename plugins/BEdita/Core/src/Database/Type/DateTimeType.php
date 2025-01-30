<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
use DateTimeInterface;

/**
 * Custom DateTimeType class with simplified marshal
 */
class DateTimeType extends CakeDateTimeType
{
    /**
     * {@inheritDoc}
     *
     * Accepted date time formats are:
     * - 2017-01-01                    YYYY-MM-DD
     * - 2017-01-01 11:22              YYYY-MM-DD hh:mm
     * - 2017-01-01T11:22:33           YYYY-MM-DDThh:mm:ss
     * - 2017-01-01T11:22:33.123456    YYYY-MM-DDThh:mm:ss.s
     * - 2017-01-01T11:22              YYYY-MM-DDThh:mm
     * - 2017-01-01T19:20+01:00        YYYY-MM-DDThh:mmTZD
     * - 2017-01-01T11:22:33+0100      YYYY-MM-DDThh:mm:ssTZD
     * - 2017-01-01T19:20:30.45+01     YYYY-MM-DDThh:mm:ss.sTZD
     * - 2017-01-01T11:22:33Z          YYYY-MM-DDThh:mm:ssZ
     *
     * See ISO 8601 subset as defined here https://www.w3.org/TR/NOTE-datetime:
     * Valid TZD formats are: ±hh:mm, ±hhmm and ±hh, e.g. +01:00, +0100 and +01
     */
    protected array $_marshalFormats = [
        'Y-m-d', // YYYY-MM-DD
        'Y-m-d H:i', // YYYY-MM-DD hh:mm
        'Y-m-d H:i:s', // YYYY-MM-DD hh:mm:ss
        'Y-m-d H:i:s.u', // YYYY-MM-DD hh:mm:ss.s
        'Y-m-d\TH:i', // YYYY-MM-DDThh:mm
        'Y-m-d\TH:i:s', // YYYY-MM-DDThh:mm:ss
        'Y-m-d\TH:i:sP', // YYYY-MM-DDThh:mm:ssTZD
        'Y-m-d\TH:i:s.uP', // YYYY-MM-DDThh:mm:ss.sTZD
        'Y-m-d\TH:i:sZ', // YYYY-MM-DDThh:mm:ssZ
        'Y-m-d H:i:sZ', // YYYY-MM-DD hh:mm:ssZ
    ];

    /**
     * @inheritDoc
     */
    public function marshal(mixed $value): ?DateTimeInterface
    {
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $value .= ' 00:00:00';
        }

        return parent::marshal($value);
    }
}
