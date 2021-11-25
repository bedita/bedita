<?php
use Migrations\AbstractMigration;

class TreeMenuOff extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('trees')
            ->changeColumn('menu', 'boolean', [
                'comment' => 'menu on/off (default off)',
                'default' => '0',
                'limit' => null,
                'null' => false,
                'length' => null,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('trees')
            ->changeColumn('menu', 'boolean', [
                'comment' => 'menu on/off',
                'default' => '1',
                'limit' => null,
                'null' => false,
                'length' => null,
            ])
            ->update();
    }
}
