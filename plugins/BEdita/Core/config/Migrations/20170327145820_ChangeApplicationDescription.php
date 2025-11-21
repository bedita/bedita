<?php
use Migrations\BaseMigration;

class ChangeApplicationDescription extends BaseMigration
{

    public function up()
    {
        $this->table('applications')
            ->changeColumn('description', 'text', [
                'comment' => 'application description',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {
        $this->table('applications')
            ->changeColumn('description', 'text', [
                'comment' => 'application description',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->update();
    }
}

