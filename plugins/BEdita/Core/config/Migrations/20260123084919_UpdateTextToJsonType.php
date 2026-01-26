<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Migration to change column type from TEXT to JSON/JSONB.
 * Uses JSON for MySQL/MariaDB and JSONB for PostgreSQL for better performance.
 */
class UpdateTextToJsonType extends BaseMigration
{
    protected array $columnsToAlter = [
        'annotations' => ['params'],
        'async_jobs' => ['payload'],
        'auth_providers' => ['params'],
        'date_ranges' => ['params'],
        'external_auth' => ['params'],
        'media' => ['provider_extra'],
        'object_categories' => ['params'],
        'object_relations' => ['params'],
        'object_types' => ['associations', 'hidden'],
        'property_types' => ['params'],
        'relations' => ['params'],
    ];

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $adapterType = $this->getAdapter()->getAdapterType();
        $columnTypes = $this->getAdapter()->getColumnTypes();
        if (!in_array('json', $columnTypes)) {
            return;
        }

        if ($adapterType === 'sqlite') {
            return;
        }

        foreach ($this->columnsToAlter as $tableName => $columns) {
            $this->textToJson($tableName, $columns);
        }
    }

    /**
     * Alter columns of table to JSONB (Postgres) or JSON.
     * Skip if json is not a valid type for DB.
     *
     * @param string $tableName The table name
     * @param array $columns List of columns to alter
     * @return void
     */
    protected function textToJson(string $tableName, array $columns): void
    {
        $adapterType = $this->getAdapter()->getAdapterType();
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $table = null;
        foreach ($columns as $columnName) {
            if ($adapterType === 'pgsql' && in_array('jsonb', $columnTypes)) {
                // For PostgreSQL, use raw SQL with explicit casting
                $this->execute(sprintf('ALTER TABLE %s ALTER COLUMN %s TYPE jsonb USING %s::jsonb', $tableName, $columnName, $columnName));

                continue;
            }

            $this->table($tableName)
                ->changeColumn($columnName, 'json', [
                    'comment' => sprintf('%s %s (JSON format)', $tableName, $columnName),
                    'default' => null,
                    'null' => true,
                ])
                ->update();
        }
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        $adapterType = $this->getAdapter()->getAdapterType();
        $columnTypes = $this->getAdapter()->getColumnTypes();
        if (!in_array('json', $columnTypes)) {
            return;
        }

        if ($adapterType === 'sqlite') {
            return;
        }

        foreach ($this->columnsToAlter as $tableName => $columns) {
            foreach ($columns as $columnName) {
                if ($adapterType === 'pgsql' && in_array('jsonb', $columnTypes)) {
                    // For PostgreSQL, use raw SQL with explicit casting
                    $this->execute(sprintf('ALTER TABLE %s ALTER COLUMN %s TYPE text USING %s::text', $tableName, $columnName, $columnName));

                    continue;
                }

                $this->table($tableName)
                    ->changeColumn($columnName, 'text', [
                        'comment' => sprintf('%s %s (JSON format)', $tableName, $columnName),
                        'default' => null,
                        'length' => null,
                        'null' => true,
                    ])
                    ->update();
            }
        }
    }
}
