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
            ->changeColumn('stats_code', 'text', [
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
            ->changeColumn('stats_code', 'string', [
                'comment' => 'the code for statistics',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }
}
