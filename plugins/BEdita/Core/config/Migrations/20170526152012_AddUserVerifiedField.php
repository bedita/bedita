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
 * Add `verified` field to `users` table.
 *
 * @since 4.0.0
 */
class AddUserVerifiedField extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->table('users')
            ->addColumn('verified', 'timestamp', [
                'comment' => 'Timestamp at which the user became verified',
                'null' => true,
                'default' => null,
                'length' => null,
            ])
            ->addIndex(
                [
                    'verified',
                ],
                [
                    'name' => 'users_verified_idx',
                ]
            )
            ->update();

        // Set `verified = NOW()` to all users that are "on".
        $this->query("UPDATE users SET verified = CURRENT_TIMESTAMP WHERE users.id IN (SELECT objects.id FROM objects WHERE objects.status = 'on');");
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->table('users')
            ->removeIndexByName('users_verified_idx')
            ->removeColumn('verified')
            ->update();
    }
}
