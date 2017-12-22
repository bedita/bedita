<?php
use Migrations\AbstractMigration;

/**
 * Add label, description, enabled and list view columns to `properties` table.
 */
class AddPropertiesLabelDescEnabledList extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
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
                'comment' => 'property displayed in list view backend operations',
                'default' => true,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('properties')
            ->removeColumn('description')
            ->removeColumn('enabled')
            ->removeColumn('label')
            ->removeColumn('list_view')
            ->update();
    }
}

