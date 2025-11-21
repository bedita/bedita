<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Migration to change `objects.custom_props` column type from TEXT to JSON/JSONB.
 * Uses JSON for MySQL/MariaDB and JSONB for PostgreSQL for better performance.
 */
class CustomPropsJson extends BaseMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $adapterType = $this->getAdapter()->getAdapterType();
        $columnTypes = $this->getAdapter()->getColumnTypes();

        if ($adapterType === 'pgsql' && in_array('jsonb', $columnTypes)) {
            // For PostgreSQL, use raw SQL with explicit casting
            $this->execute('ALTER TABLE objects ALTER COLUMN custom_props TYPE jsonb USING custom_props::jsonb');
        } else {
            // For other databases having a 'json' column type, use Phinx changeColumn
            if (!in_array('json', $columnTypes)) {
                return;
            }

            $this->table('objects')
                ->changeColumn('custom_props', 'json', [
                    'comment' => 'object custom properties (JSON format)',
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
        // Revert back to TEXT type for all databases
        $this->table('objects')
            ->changeColumn('custom_props', 'text', [
                'comment' => 'object custom properties (JSON format)',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }
}
