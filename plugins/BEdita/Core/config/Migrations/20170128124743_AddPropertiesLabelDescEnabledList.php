<?php
use Migrations\AbstractMigration;

class AddPropertiesLabelDescEnabledList extends AbstractMigration
{

    public function up()
    {

        $this->table('properties')
            ->addColumn('description', 'text', [
                'comment' => 'brief property description',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addColumn('enabled', 'boolean', [
                'comment' => 'property active flag',
                'default' => true,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('label', 'text', [
                'comment' => 'property default label',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addColumn('list_view', 'boolean', [
                'comment' => 'property displayed in list view (backend operations)',
                'default' => true,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('properties')
            ->removeColumn('description')
            ->removeColumn('enabled')
            ->removeColumn('label')
            ->removeColumn('list_view')
            ->update();

        $this->dropTable('b_edita_core_phinxlog');
    }
}

