<?php
use Cake\Auth\WeakPasswordHasher;
use Migrations\AbstractMigration;

/**
 * Replace `CASCADE` with `RESTRICT` on `objects.created_by`, `objects.modified_by`, `objects.object_type_id`
 * delete condition
 */
class DeleteRestrict extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('objects')
            ->dropForeignKey('created_by')
            ->dropForeignKey('modified_by')
            ->dropForeignKey('object_type_id')
            ->update();

        $this->table('objects')
            ->addForeignKey(
                'created_by',
                'users',
                'id',
                [
                    'constraint' => 'objects_createdby_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'modified_by',
                'users',
                'id',
                [
                    'constraint' => 'objects_modifiedby_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'constraint' => 'objects_objtype_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('objects')
            ->dropForeignKey('created_by')
            ->dropForeignKey('modified_by')
            ->dropForeignKey('object_type_id')
            ->update();

        $this->table('objects')
            ->addForeignKey(
                'created_by',
                'users',
                'id',
                [
                    'constraint' => 'objects_createdby_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'modified_by',
                'users',
                'id',
                [
                    'constraint' => 'objects_modifiedby_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'constraint' => 'objects_objtype_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }
}
