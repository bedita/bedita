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
        'applications' => '320029666',
        'documents' => '1389311771',
        'events' => '3616621047',
        'files' => '336351369',
        'locations' => '1962607368',
        'profiles' => '4263816212',
        'roles' => '2455170079',
        'users' => '3778063754',
    ];
}
