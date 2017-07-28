<?php
use Migrations\AbstractMigration;

class ChangeApplicationDescription extends AbstractMigration
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

