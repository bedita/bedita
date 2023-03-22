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

namespace BEdita\Core\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Fixture for `fake_categories` table.
 */
class FakeCategoriesFixture extends TestFixture
{
    /**
     * {@inheritDoc}
     *
     * @example ```
     * - Science
     *   - Mathematics
     *     - Geometry
     *     - Algebra
     *     - Mathematical Logic
     *   - Physics
     *     - Fluid mechanics
     *     - Rational mechanics
     * - History of Art
     * ```
     */
    public $records = [
        [ // ID: 1
            'name' => 'Science',
            'parent_id' => null,
            'left_idx' => 1,
            'right_idx' => 16,
        ],
        [ // ID: 2
            'name' => 'Mathematics',
            'parent_id' => 1,
            'left_idx' => 2,
            'right_idx' => 9,
        ],
        [ // ID: 3
            'name' => 'Geometry',
            'parent_id' => 2,
            'left_idx' => 3,
            'right_idx' => 4,
        ],
        [ // ID: 4
            'name' => 'Algebra',
            'parent_id' => 2,
            'left_idx' => 5,
            'right_idx' => 6,
        ],
        [ // ID: 5
            'name' => 'Mathematical Logic',
            'parent_id' => 2,
            'left_idx' => 7,
            'right_idx' => 8,
        ],
        [ // ID: 6
            'name' => 'Physics',
            'parent_id' => 1,
            'left_idx' => 10,
            'right_idx' => 15,
        ],
        [ // ID: 7
            'name' => 'Fluid Mechanics',
            'parent_id' => 6,
            'left_idx' => 11,
            'right_idx' => 12,
        ],
        [ // ID: 8
            'name' => 'Rational Mechanics',
            'parent_id' => 6,
            'left_idx' => 13,
            'right_idx' => 14,
        ],
        [ // ID: 9
            'name' => 'History of Art',
            'parent_id' => null,
            'left_idx' => 17,
            'right_idx' => 18,
        ],
    ];
}
