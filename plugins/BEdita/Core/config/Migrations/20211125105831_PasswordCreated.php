<?php
use Migrations\AbstractMigration;

class PasswordCreated extends AbstractMigration
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
        $this->table('users')
            ->addColumn('password_modified', 'datetime', [
                'comment' => 'Password last modification date',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }
}
