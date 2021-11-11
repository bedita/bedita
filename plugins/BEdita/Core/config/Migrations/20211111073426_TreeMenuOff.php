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
            ->changeColumn('menu', 'integer', [
                'comment' => 'menu on/off (default off)',
                'default' => '0',
                'limit' => 10,
                'null' => true,
                'signed' => false,
            ])
            ->update();
    }
}
