<?php
use Migrations\AbstractMigration;

class PublicationsUpdate extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->table('publications')
            ->changeColumn('public_url', 'string', [
                'comment' => 'the public url',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('staging_url', 'string', [
                'comment' => 'the staging url',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('stats_code', 'string', [
                'comment' => 'the code for statistics',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('publications')
            ->changeColumn('public_url', 'text', [
                'comment' => 'the public url',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('staging_url', 'text', [
                'comment' => 'the staging url',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('stats_code', 'text', [
                'comment' => 'the code for statistics',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }
}
