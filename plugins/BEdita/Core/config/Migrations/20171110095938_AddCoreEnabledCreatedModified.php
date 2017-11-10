<?php
use Migrations\AbstractMigration;

/**
 * Add `core_type`, `created`, `modified`, `enabled` columns to `object_types`.
 *
 * @see https://github.com/bedita/bedita/1366
 */
class AddCoreEnabledCreatedModified extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('object_types')
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
                'comment' => 'core object type flag',
                'default' => false,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('enabled', 'boolean', [
                'comment' => 'object type active flag',
                'default' => true,
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
            ->addIndex(
                [
                    'enabled',
                ],
                [
                    'name' => 'objecttypes_enabled_idx',
                ]
            )
            ->update();

        // UPDATE created, modified
        $this->query(sprintf("UPDATE object_types SET created = '%s', modified = '%s'", date('Y-m-d H:i:s'), date('Y-m-d H:i:s')));

        $this->table('object_types')
            ->changeColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->changeColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->update();

        // UPDATE core_type
        $coreTypes = "'objects', 'profiles', 'users', 'documents', 'events', 'media', 'images', 'audio', 'videos', 'files', 'news', 'locations'";
        $true = ($this->getAdapter()->getAdapterType() === 'sqlite') ? "1" : "TRUE";
        $this->query(sprintf("UPDATE object_types SET core_type = %s WHERE name IN (%s)", $true, $coreTypes));
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('object_types')
            ->removeColumn('created')
            ->removeColumn('modified')
            ->removeColumn('core_type')
            ->removeColumn('enabled')
            ->update();
    }
}

