<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class DropTreesDepthLevel extends BaseMigration
{
    /**
     * {@inheritDoc}
     */
    public function up(): void
    {
        $this->table('trees')
            ->removeColumn('depth_level')
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down(): void
    {
        $this->table('trees')
            ->addColumn('depth_level', 'integer', [
                'comment' => 'tree depth level',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->update();
    }
}
