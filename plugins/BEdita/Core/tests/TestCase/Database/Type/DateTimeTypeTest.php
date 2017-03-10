<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Database\Type;

use BEdita\Core\Database\Type\DateTimeType;
use Cake\I18n\Time;
use Cake\TestSuite\TestCase;

/**
 * \BEdita\Core\Database\Type\DateTimeType Test Case
 *
 * @covers \BEdita\Core\Database\Type\DateTimeType
 */
class DateTimeTypeTest extends TestCase
{

    /**
     * Test `marshal` method
     *
     * @covers ::marshal
     * @return void
     */
    public function testMarshal()
    {
        $dateTimeType = new DateTimeType();
        $date = Time::now();
        $result = $dateTimeType->marshal($date);
        $this->assertEquals($date->nice(), $result->nice());

        $date = '2017-03-02 11:22:33';
        $result = $dateTimeType->marshal($date);
        $this->assertEquals(Time::parse($date)->toUnixString(), $result->toUnixString());
    }
}
