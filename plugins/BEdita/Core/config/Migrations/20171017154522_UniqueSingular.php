<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

use Migrations\AbstractMigration;

/**
 * Modify `object_types.singular` to be unique.
 *
 * @since 4.0.0
 */
class UniqueSingular extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        // Set `news_item` as singular form of `news` and `media_item` for `media`
        $this->query("UPDATE object_types SET singular = 'news_item' WHERE name = 'news'");
        $this->query("UPDATE object_types SET singular = 'media_item' WHERE name = 'media'");
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // Restore `news` and `media` as singular form
        $this->query("UPDATE object_types SET singular = 'news' WHERE name = 'news'");
        $this->query("UPDATE object_types SET singular = 'media' WHERE name = 'media'");
    }
}
