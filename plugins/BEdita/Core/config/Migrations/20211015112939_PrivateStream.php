<?php
use Migrations\AbstractMigration;

class PrivateStream extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->table('streams')
            ->addColumn('private_url', 'boolean', [
                'comment' => 'keep stream URL private (default false)',
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->update();
    }
}
