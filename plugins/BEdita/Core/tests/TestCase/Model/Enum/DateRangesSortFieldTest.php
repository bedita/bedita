<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Model;

use BEdita\Core\Model\Enum\DateRangesSortField;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Model\Enum\DateRangesSortField} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Enum\DateRangesSortField
 */
class DateRangesSortFieldTest extends TestCase
{
    /**
     * Test that enum cases are correctly defined.
     *
     * @return void
     * @covers ::values()
     */
    public function testValues(): void
    {
        $expected = [
            DateRangesSortField::MIN_START_DATE->value,
            DateRangesSortField::MAX_START_DATE->value,
            DateRangesSortField::MIN_END_DATE->value,
            DateRangesSortField::MAX_END_DATE->value,
        ];
        $this->assertSame($expected, DateRangesSortField::values());
    }
}
