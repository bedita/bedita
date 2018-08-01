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
        // 1
        [
            'singular' => 'object',
            'name' => 'objects',
            'is_abstract' => true,
            'parent_id' => null,
            'tree_left' => 1,
            'tree_right' => 20,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
            'created' => '2017-11-10 09:27:23',
            'modified' => '2017-11-10 09:27:23',
            'enabled' => true,
            'core_type' => true,
        ],
        // 2
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
            'created' => '2017-11-10 09:27:23',
            'modified' => '2017-11-10 09:27:23',
            'enabled' => true,
            'core_type' => true,
        ],
        // 3
        [
            'singular' => 'profile',
            'name' => 'profiles',
            'is_abstract' => false,
            'parent_id' => 1,
            'tree_left' => 4,
            'tree_right' => 5,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Profiles',
            'created' => '2017-11-10 09:27:23',
            'modified' => '2017-11-10 09:27:23',
            'enabled' => true,
            'core_type' => true,
        ],
        // 4
        [
            'singular' => 'user',
            'name' => 'users',
            'is_abstract' => false,
            'parent_id' => 1,
            'tree_left' => 6,
            'tree_right' => 7,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Users',
            'created' => '2017-11-10 09:27:23',
            'modified' => '2017-11-10 09:27:23',
            'enabled' => true,
            'core_type' => true,
        ],
        // 5
        [
            'singular' => 'news_item',
            'name' => 'news',
            'is_abstract' => false,
            'parent_id' => 1,
            'tree_left' => 8,
            'tree_right' => 9,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
            'hidden' => '["body"]',
            'created' => '2017-11-10 09:27:23',
            'modified' => '2017-11-10 09:27:23',
            'enabled' => false,
            'core_type' => false,
        ],
        // 6
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
            'created' => '2017-11-10 09:27:23',
            'modified' => '2017-11-10 09:27:23',
            'enabled' => true,
            'core_type' => true,
        ],
        // 7
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
            'created' => '2017-11-10 09:27:23',
            'modified' => '2017-11-10 09:27:23',
            'enabled' => true,
            'core_type' => true,
        ],
        // 8
        [
            'singular' => 'media_item',
            'name' => 'media',
            'is_abstract' => true,
            'parent_id' => 1,
            'tree_left' => 14,
            'tree_right' => 17,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Media',
            'associations' => '["Streams"]',
            'created' => '2017-11-10 09:27:23',
            'modified' => '2017-11-10 09:27:23',
            'enabled' => true,
            'core_type' => true,
        ],
        // 9
        [
            'singular' => 'file',
            'name' => 'files',
            'is_abstract' => false,
            'parent_id' => 8,
            'tree_left' => 15,
            'tree_right' => 16,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Media',
            'associations' => '["Streams"]',
            'created' => '2017-11-10 09:27:23',
            'modified' => '2017-11-10 09:27:23',
            'enabled' => true,
            'core_type' => true,
        ],
        // 10
        [
            'singular' => 'folder',
            'name' => 'folders',
            'is_abstract' => false,
            'parent_id' => 1,
            'tree_left' => 18,
            'tree_right' => 19,
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
            'created' => '2018-01-29 08:47:29',
            'modified' => '2018-01-29 08:47:29',
            'enabled' => true,
            'core_type' => true,
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
