<?php
use Migrations\AbstractMigration;

class ProfileAlias extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('profiles')
            ->addColumn('alias', 'string', [
                'comment' => 'Alias or pseudonym, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('profiles')
            ->removeColumn('alias')
            ->update();
    }
}
