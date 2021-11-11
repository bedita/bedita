<?php
use Migrations\AbstractMigration;

class TreeMenuOff extends AbstractMigration
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function change()
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
}
