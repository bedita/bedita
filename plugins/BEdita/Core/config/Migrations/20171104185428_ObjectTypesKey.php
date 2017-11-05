<?php

use Cake\ORM\Table;
use Migrations\AbstractMigration;

/**
 * Fix `object_type_id` foreign key index.
 */
class ObjectTypesKey extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('objects')
            ->dropForeignKey(
                'object_type_id'
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
    }
}
