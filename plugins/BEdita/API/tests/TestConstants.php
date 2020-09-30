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
        'applications' => '2244706479',
        'documents' => '3090683659',
        'events' => '1906204265',
        'files' => '894208275',
        'folders' => '1906204265',
        'locations' => '1369205356',
        'profiles' => '593147109',
        'roles' => '2845943672',
        'users' => '1014664219',
    ];
}
