<?php
use Migrations\AbstractMigration;

class AddPropertiesCreatedModified extends AbstractMigration
{

    public function up()
    {

        $this->table('properties')
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('properties')
            ->removeColumn('created')
            ->removeColumn('modified')
            ->update();

        $this->dropTable('b_edita_core_phinxlog');
    }
}

