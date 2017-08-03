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
 * Move `width`, `height` and `duration` from `media` to `streams`. Add `provider_extra` to `media`.
 *
 * @since 4.0.0
 */
class MoveWidthHeightDurationToStreams extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public function up()
    {

        if ($this->getAdapter()->getAdapterType() === 'sqlite') {
            // Change comment for some columns before deleting to avoid SQLite adapter bug.
            $this->table('media')
                ->changeColumn('width', 'integer', [
                    'comment' => 'bogus comment',
                    'default' => null,
                    'limit' => 10,
                    'null' => true,
                ])
                ->changeColumn('height', 'integer', [
                    'comment' => 'bogus comment',
                    'default' => null,
                    'limit' => 10,
                    'null' => true,
                ])
                ->changeColumn('duration', 'integer', [
                    'comment' => 'bogus comment',
                    'default' => null,
                    'limit' => 10,
                    'null' => true,
                ])
                ->update();
        }

        $this->table('media')
            ->removeColumn('width')
            ->removeColumn('height')
            ->removeColumn('duration')
            ->update();

        $this->table('media')
            ->addColumn('provider_extra', 'text', [
                'after' => 'provider_thumbnail',
                'comment' => 'Additional provider metadata',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('streams')
            ->addColumn('width', 'integer', [
                'after' => 'hash_sha1',
                'comment' => 'Width (if applicable)',
                'default' => null,
                'length' => 10,
                'null' => true,
            ])
            ->addColumn('height', 'integer', [
                'after' => 'width',
                'comment' => 'Height (if applicable)',
                'default' => null,
                'length' => 10,
                'null' => true,
            ])
            ->addColumn('duration', 'integer', [
                'after' => 'height',
                'comment' => 'Duration (if applicable)',
                'default' => null,
                'length' => 10,
                'null' => true,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('media')
            ->addColumn('width', 'integer', [
                'after' => 'name',
                'comment' => 'Width (if applicable)',
                'default' => null,
                'length' => 10,
                'null' => true,
            ])
            ->addColumn('height', 'integer', [
                'after' => 'width',
                'comment' => 'Height (if applicable)',
                'default' => null,
                'length' => 10,
                'null' => true,
            ])
            ->addColumn('duration', 'integer', [
                'after' => 'height',
                'comment' => 'Duration (if applicable)',
                'default' => null,
                'length' => 10,
                'null' => true,
            ])
            ->removeColumn('provider_extra')
            ->update();

        $this->table('streams')
            ->removeColumn('width')
            ->removeColumn('height')
            ->removeColumn('duration')
            ->update();
    }
}
