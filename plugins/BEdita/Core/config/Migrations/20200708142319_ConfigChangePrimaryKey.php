<?php
use Migrations\AbstractMigration;

class ConfigChangePrimaryKey extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('config')
            ->changePrimaryKey(['name', 'application_id'])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('config')
            ->changePrimaryKey(['name'])
            ->update();
    }
}
