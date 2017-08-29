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
 * Add `custom_props` field to `objects` table.
 *
 * @since 4.0.0
 */
class AddCustomProps extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->table('objects')
            ->addColumn('custom_props', 'text', [
                'after' => 'body',
                'comment' => 'object custom properties (JSON format)',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->table('objects')
            ->removeColumn('custom_props')
            ->update();
    }
}

