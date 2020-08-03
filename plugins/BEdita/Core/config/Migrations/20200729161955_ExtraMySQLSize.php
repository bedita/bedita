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
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * Increase `objects.extra` column size to 16MB (on MySQL only)
 */
class ExtraMySQLSize extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $limit = null;
        if ($this->adapter->getAdapterType() === 'mysql') {
            $limit = MysqlAdapter::TEXT_MEDIUM;
        }
        $this->table('objects')
            ->changeColumn('extra', 'text', [
                'comment' => 'object data extensions (JSON format)',
                'default' => null,
                'limit' => $limit,
                'null' => true,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('objects')
            ->changeColumn('extra', 'text', [
                'comment' => 'object data extensions (JSON format)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }
}
