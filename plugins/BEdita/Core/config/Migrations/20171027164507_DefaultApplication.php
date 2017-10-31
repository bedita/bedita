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

use BEdita\Core\Model\Table\ApplicationsTable;
use Migrations\AbstractMigration;

/**
 * Create a default application if missing.
 *
 * @since 4.0.0
 */
class DefaultApplication extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $appRow = $this->fetchAll("SELECT id FROM applications where id=1");
        if (!empty($appRow)) {
            return 0;
        }

        $this->table('applications')
            ->insert([
                [
                    'id' => 1,
                    'name' => 'default-app',
                    'api_key' => ApplicationsTable::generateApiKey(),
                    'description' => 'Default application',
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s'),
                    'enabled' => 1,
                ]
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

