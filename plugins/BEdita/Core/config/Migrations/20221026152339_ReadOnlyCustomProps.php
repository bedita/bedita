<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class ReadOnlyCustomProps extends BaseMigration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->table('properties')
            ->addColumn('read_only', 'boolean', [
                'comment' => 'property read-only flag',
                'default' => false,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('properties')
            ->removeColumn('read_only')
            ->update();
    }
}
