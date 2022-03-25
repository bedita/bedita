<?php
use Migrations\AbstractMigration;

class AddUserPreferences extends AbstractMigration
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
        $type = in_array('json', $this->getAdapter()->getColumnTypes()) ? 'json' : 'text';

        $this->table('users')
            ->addColumn('user_preferences', $type, [
                'comment' => 'user preferences',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }
}
