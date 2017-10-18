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

namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * Fixture for `object_types` table.
 */
class ObjectTypesFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'singular' => 'object',
            'name' => 'objects',
            'is_abstract' => true,
            'parent_id' => null,
            'tree_left' => 1,
            'tree_right' => 16,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
        ],
        [
            'singular' => 'document',
            'name' => 'documents',
            'is_abstract' => false,
            'parent_id' => 1,
            'tree_left' => 2,
            'tree_right' => 3,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
        ],
        [
            'singular' => 'profile',
            'name' => 'profiles',
            'is_abstract' => false,
            'parent_id' => 1,
            'tree_left' => 4,
            'tree_right' => 7,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Profiles',
        ],
        [
            'singular' => 'user',
            'name' => 'users',
            'is_abstract' => false,
            'parent_id' => 3,
            'tree_left' => 5,
            'tree_right' => 6,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Users',
        ],
        [
            'singular' => 'news',
            'name' => 'news',
            'is_abstract' => false,
            'parent_id' => 1,
            'tree_left' => 8,
            'tree_right' => 9,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
            'hidden' => '["body"]',
        ],
        [
            'singular' => 'location',
            'name' => 'locations',
            'is_abstract' => false,
            'parent_id' => 1,
            'tree_left' => 10,
            'tree_right' => 11,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Locations',
        ],
        [
            'singular' => 'event',
            'name' => 'events',
            'is_abstract' => false,
            'parent_id' => 1,
            'tree_left' => 12,
            'tree_right' => 13,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
            'associations' => '["DateRanges"]',
        ],
        [
            'singular' => 'media',
            'name' => 'media',
            'is_abstract' => true,
            'parent_id' => 1,
            'tree_left' => 14,
            'tree_right' => 15,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Media',
            'associations' => '["Streams"]',
        ],
    ];

    /**
     * Before Build Schema callback
     *
     * Remove foreign key on `parent_id` to avoid errors when DROP-ping table.
     *
     * @return void
     */
    public function beforeBuildSchema()
    {
        unset($this->fields['_constraints']['objecttypes_parentid_fk']);
    }
}
