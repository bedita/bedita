<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

use Migrations\AbstractMigration;

class StreamsUriLimit extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        // remove index streams_uri_uq
        $this->table('streams')
            ->removeIndexByName('streams_uri_uq')
            ->update();

        // change column uri, no limit
        $this->table('streams')
            ->changeColumn('uri', 'string', [
                'comment' => 'Path where physical file is stored',
                'default' => '',
                'limit' => null,
                'null' => false,
            ])
            ->update();

        // add index streams_uri_uq
        $this->table('streams')
            ->addIndex(
                [
                    'uri',
                ],
                [
                    'name' => 'streams_uri_uq',
                    'unique' => true,
                ]
            )
            ->update();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // rollback streams.uri to limit 255
        // remove index streams_uri_uq
        $this->table('streams')
            ->removeIndexByName('streams_uri_uq')
            ->update();

        // change column uri, no limit
        $this->table('streams')
            ->changeColumn('uri', 'string', [
                'comment' => 'Path where physical file is stored',
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->update();

        // add index streams_uri_uq
        $this->table('streams')
            ->addIndex(
                [
                    'uri',
                ],
                [
                    'name' => 'streams_uri_uq',
                    'unique' => true,
                ]
            )
            ->update();
    }
}
