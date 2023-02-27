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
    public const SCHEMA_REVISIONS = [
        'applications' => '3594165375',
        'documents' => '887094289',
        'events' => '2652801567',
        'files' => '2463983178',
        'folders' => '3048758948',
        'locations' => '4113928771',
        'profiles' => '3709705564',
        'roles' => '2845943672',
        'users' => '3368903199',
    ];
}
