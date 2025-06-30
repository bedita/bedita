<?php
declare(strict_types=1);

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
namespace BEdita\Core\Test\Utility;

/**
 * Trait to add `assertArraySubset()` method to test cases.
 */
trait TestArraySubsetTrait
{
    /**
     * Assert that an array is a subset of another array.
     *
     * @param array $subset The subset to check.
     * @param array $array The array to check against.
     * @param string $message Optional message to display on failure.
     * @return void
     */
    public static function assertArraySubset(array $subset, array $array, string $message = ''): void
    {
        foreach ($subset as $key => $value) {
            self::assertArrayHasKey($key, $array, $message);
            self::assertEquals($value, $array[$key], $message);
        }
    }
}
