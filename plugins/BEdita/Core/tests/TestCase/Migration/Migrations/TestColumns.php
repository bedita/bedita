<?php
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
