<?php
use Migrations\AbstractMigration;

/**
 * Use dedicated column for deleted objects.
 *
 * @see https://github.com/bedita/bedita/issues/1027
 */
class DeleteTrash extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $enum = in_array('enum', $columnTypes) ? 'enum' : 'string';

        $this->table('objects')
            ->changeColumn('status', $enum, [
                'comment' => 'object status: on, draft, off',
                'default' => 'draft',
                'limit' => 255,
                'values' => ['on', 'off', 'draft'],
                'null' => false,
            ])
            ->update();

        $this->table('objects')
            ->addColumn('deleted', 'boolean', [
                'after' => 'locked',
                'comment' => 'deleted flag: if true object is in trashcan, default false',
                'default' => false,
                'length' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'deleted',
                ],
                [
                    'name' => 'objects_deleted_idx',
                ]
            )
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $enum = in_array('enum', $columnTypes) ? 'enum' : 'string';

        $this->table('objects')
            ->removeIndexByName('objects_deleted_idx')
            ->update();

        $this->table('objects')
            ->changeColumn('status', $enum, [
                'comment' => 'object status: on, draft, off, deleted',
                'default' => 'draft',
                'limit' => 255,
                'values' => ['on', 'off', 'draft'],
                'null' => false,
            ])
            ->removeColumn('deleted')
            ->update();
    }
}

