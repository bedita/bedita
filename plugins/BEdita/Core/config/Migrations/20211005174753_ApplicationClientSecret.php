<?php
use Migrations\BaseMigration;

class ApplicationClientSecret extends BaseMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('applications')
            ->addColumn('client_secret', 'string', [
                'comment' => 'client secret value for application',
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
        $this->table('applications')
            ->removeColumn('client_secret')
            ->update();
    }
}
