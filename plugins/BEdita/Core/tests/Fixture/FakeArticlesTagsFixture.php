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

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Fixture for `fake_articles_tags` table.
 */
class FakeArticlesTagsFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => true],
        'fake_article_id' => ['type' => 'integer', 'null' => true],
        'fake_tag_id' => ['type' => 'integer', 'null' => true],
        'fake_params' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public $records = [
        ['fake_article_id' => 1, 'fake_tag_id' => 1],
        ['fake_article_id' => 2, 'fake_tag_id' => 2],
    ];
}
