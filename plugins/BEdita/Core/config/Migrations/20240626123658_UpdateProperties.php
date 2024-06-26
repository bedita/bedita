<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class UpdateProperties extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up(): void
    {
        $this->table('properties')
            ->addColumn('default_value', 'text', [
                'after' => 'read_only',
                'comment' => 'Default value',
                'length' => null,
                'null' => true,
            ])
            ->addColumn('options', 'text', [
                'after' => 'default_value',
                'comment' => 'Options',
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down(): void
    {
        $this->table('properties')
            ->removeColumn('default_value')
            ->removeColumn('options')
            ->update();
    }
}
