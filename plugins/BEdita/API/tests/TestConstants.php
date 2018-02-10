<?php
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

namespace BEdita\API\Test;

/**
 * Test utility class with useful constants
 */
class TestConstants
{
    /**
     * Schema revision for each resource and object used in tests
     *
     * @var array
     */
    const SCHEMA_REVISIONS = [
        'documents' => '1626723409',
        'events' => '4232492187',
        'files' => '2375452932',
        'locations' => '3704626079',
        'profiles' => '2293379270',
        'roles' => '1598151971',
        'users' => '72522241',
    ];
}
