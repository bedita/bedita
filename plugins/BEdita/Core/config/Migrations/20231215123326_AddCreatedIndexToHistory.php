<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddCreatedIndexToHistory extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function change(): void
    {
        $this->table('history')
            ->addIndex(['created'], ['name' => 'history_created_idx'])
            ->addIndex(['user_id', 'created'], ['name' => 'history_usercreated_idx'])
            ->addIndex(['resource_type', 'resource_id', 'created'], ['name' => 'history_resourcecreated_idx'])
            ->update();
    }
}
