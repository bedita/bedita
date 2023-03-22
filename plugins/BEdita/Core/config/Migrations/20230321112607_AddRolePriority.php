<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddRolePriority extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function change(): void
    {
        $this->table('roles')
            ->addColumn('priority', 'integer', [
                'default' => 100,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->update();
        $this->query('UPDATE roles SET priority = 0 WHERE id = 1');
    }
}
