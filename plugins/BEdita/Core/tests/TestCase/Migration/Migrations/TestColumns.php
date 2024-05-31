<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Migration\Migrations;

use BEdita\Core\Migration\ResourcesMigration;
use BEdita\Core\Test\TestCase\Migration\MockMigrationsTable;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;

class TestColumns extends ResourcesMigration
{
    /**
     * {@inheritdoc}
     */
    protected function getConnection(): Connection
    {
        return ConnectionManager::get('default');
    }

    /**
     * {@inheritdoc}
     */
    public function table($tableName, $options = [])
    {
        return new MockMigrationsTable($tableName, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function columnTypes(): array
    {
        return [
            'text',
            'string',
            'float',
            'integer',
        ];
    }
}
