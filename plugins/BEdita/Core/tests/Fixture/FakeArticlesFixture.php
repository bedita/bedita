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
 * Fixture for `fake_articles` table.
 */
class FakeArticlesFixture extends TestFixture
{
    /**
     * @inheritDoc
     */
    public $records = [
        ['title' => 'The cat', 'body' => 'article body', 'fake_animal_id' => 1],
        ['title' => 'Puss in boots', 'body' => 'text', 'fake_animal_id' => 1],
    ];
}
