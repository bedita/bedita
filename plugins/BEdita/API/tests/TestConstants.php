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
        'applications' => '549590360',
        'documents' => '711243664',
        'events' => '2573168521',
        'files' => '61843119',
        'folders' => '960941387',
        'images' => '377670688',
        'locations' => '383128401',
        'profiles' => '2929647885',
        'roles' => '1165313018',
        'users' => '3078046273',
        'videos' => '820471986',
    ];
}
