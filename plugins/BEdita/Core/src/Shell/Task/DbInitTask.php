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
namespace BEdita\Core\Shell\Task;

use BEdita\Core\Utility\Database;
use Cake\Console\Shell;
use Cake\Core\Plugin;
use Cake\Datasource\Exception\MissingDatasourceConfigException;

/**
 * Init database schema and populate initial data
 *
 * @since 4.0.0
 */
class DbInitTask extends Shell
{

    /**
     * Initialize BE4 database schema
     * SQL schema in BEdita/Core/config/schema/be4-schema-<vendor>.sql
     *
     * @return void
     */
    public function main()
    {
        $this->out('Creating new database schema...');
        $schemaDir = Plugin::path('BEdita/Core') . 'config' . DS . 'schema' . DS;
        $info = Database::basicInfo();
        $schemaFile = $schemaDir . 'be4-schema-' . $info['vendor'] . '.sql';
        if (!file_exists($schemaFile)) {
            $this->abort('Schema file not found: ' . $schemaFile);
        }
        $sqlSchema = file_get_contents($schemaFile);
        try {
            $result = Database::executeTransaction($sqlSchema);
            if (!$result['success']) {
                $this->abort('Error creating database schema: ' . $result['error']);
            }
        } catch (MissingDatasourceConfigException $e) {
            $this->abort('Database connection not configured!');
        }
        $this->info('New database schema set');
    }
}
