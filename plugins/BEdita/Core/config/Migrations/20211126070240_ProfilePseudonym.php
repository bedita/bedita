<?php
use Migrations\AbstractMigration;

class ProfilePseudonym extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('profiles')
            ->addColumn('pseudonym', 'string', [
                'comment' => 'Pseudonym, can be NULL',
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
            ->removeColumn('pseudonym')
            ->update();
    }
}
