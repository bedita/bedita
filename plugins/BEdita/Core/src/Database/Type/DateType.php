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

use Cake\Database\Type\DateType as CakeDateType;
use DateTime;
use DateTimeInterface;

/**
 * Custom DateType class with simplified marshal
 */
class DateType extends CakeDateType
{
    /**
     * @inheritDoc
     */
    public function marshal($value): ?DateTimeInterface
    {
        $date = DateTimeType::marshalDateTime($value, $this->getDateTimeClassName());
        if ($date instanceof DateTime) {
            $date->setTime(0, 0, 0);
        }

        return $date;
    }
}
