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
            ->addColumn('created', 'timestamp', [
                'comment' => 'creation time',
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'timestamp', [
                'comment' => 'last modification time',
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
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
                    'name' => 'objecttypes_coretype_idx',
                ]
            )
            ->update();

        // UPDATE core_type
        $coreTypes = "'boolean', 'date', 'datetime', 'email', 'integer', 'json', 'number', 'status', 'string', 'text', 'url'";
        $true = ($this->getAdapter()->getAdapterType() === 'sqlite') ? "1" : "TRUE";
        $this->query(sprintf("UPDATE object_types SET core_type = %s WHERE name IN (%s)", $true, $coreTypes));
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
