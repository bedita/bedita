<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class UpdateObjectPermissions extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->table('object_permissions')
            ->addColumn('created_by', 'integer', [
                'comment' => 'user who created object',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->removeColumn('params')
            ->addIndex(
                [
                    'created_by',
                ],
                [
                    'name' => 'objectpermissions_createdby_idx',
                ]
            )
            ->addForeignKey(
                'created_by',
                'users',
                'id',
                [
                    'constraint' => 'objectpermissions_createdby_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'RESTRICT',
                ]
            )
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('object_permissions')
            ->removeIndex('created_by')
            ->dropForeignKey('created_by')
            ->removeColumn('created_by')
            ->removeColumn('created')
            ->addColumn('params', 'text', [
                'comment' => 'permission parameters (JSON data)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }
}
