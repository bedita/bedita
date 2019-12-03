<?php
use Migrations\AbstractMigration;

class CorePropertyTypes extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('property_types')
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('core_type', 'boolean', [
                'comment' => 'core property type flag, if true type is immutable',
                'default' => false,
                'length' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'core_type',
                ],
                [
                    'name' => 'propertytypes_coretype_idx',
                ]
            )
            ->update();

        // UPDATE core_type
        $coreTypes = "'boolean', 'date', 'datetime', 'email', 'integer', 'json', 'number', 'status', 'string', 'text', 'url'";
        $true = ($this->getAdapter()->getAdapterType() === 'sqlite') ? "1" : "TRUE";
        $this->query(sprintf("UPDATE property_types SET core_type = %s WHERE name IN (%s)", $true, $coreTypes));
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('property_types')
            ->removeColumn('created')
            ->removeColumn('modified')
            ->removeColumn('core_type')
            ->update();
    }
}
