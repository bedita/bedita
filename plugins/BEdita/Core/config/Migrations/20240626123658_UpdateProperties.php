<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class UpdateProperties extends BaseMigration
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
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down(): void
    {
        $this->table('properties')
            ->removeColumn('default_value')
            ->update();
    }
}
