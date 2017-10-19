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
 * Add `videos`, `audio` and `files` core media types.
 *
 * @since 4.0.0
 */
class CoreMediaTypes extends AbstractMigration
{

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->table('object_types')
            ->insert([
                [
                    'name' => 'videos',
                    'singular' => 'video',
                    'description' => 'Videos media model',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Media',
                    'associations' => '["Streams"]',
                ],
                [
                    'name' => 'audio',
                    'singular' => 'audio_item',
                    'description' => 'Audio media model',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Media',
                    'associations' => '["Streams"]',
                ],
                [
                    'name' => 'files',
                    'singular' => 'file',
                    'description' => 'Files media model',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Media',
                    'associations' => '["Streams"]',
                ],
            ])
            ->save();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
    }
}

