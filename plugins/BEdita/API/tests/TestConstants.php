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
        'applications' => '3594165375',
        'documents' => '2068587631',
        'events' => '627651855',
        'files' => '2771065426',
        'folders' => '821097843',
        'images' => '1595717430',
        'locations' => '2618601730',
        'profiles' => '2380155960',
        'roles' => '122746925',
        'users' => '1523049527',
        'videos' => '3440190746',
    ];
}
