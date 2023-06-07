<?php
use Migrations\AbstractMigration;

class IncreaseLinksUrlMaxLength extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->table('links')
            ->changeColumn('url', 'text', [
                'comment' => 'Url',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(['url'], [
                'name' => 'links_url_idx',
                'limit' => 255,
            ])
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('links')
            ->changeColumn('url', 'string', [
                'comment' => 'Url',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->removeIndexByName('links_url_idx')
            ->update();
    }
}
