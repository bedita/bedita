<?php
use Migrations\AbstractMigration;

class RemovePluralized extends AbstractMigration
{

    public $autoId = false;

    public function up()
    {

        $this->table('object_types')
            ->renameColumn('name', 'singular')
            ->update();

        $this->table('object_types')
            ->renameColumn('pluralized', 'name')
            ->update();

        $this->table('object_types')
            ->addIndex(
                [
                    'singular',
                ],
                [
                    'name' => 'objecttypes_singular_uq',
                    'unique' => true,
                ]
            )
            ->update();

        $this->table('object_types')
            ->removeIndexByName('objecttypes_plural_uq')
            ->update();
    }

    public function down()
    {
        $this->table('object_types')
            ->addColumn('pluralized', 'string', [
                'after' => 'name',
                'comment' => 'pluralized object type name',
                'default' => null,
                'length' => 50,
                'null' => false,
            ])
            ->removeColumn('singular')
            ->addIndex(
                [
                    'pluralized',
                ],
                [
                    'name' => 'objecttypes_plural_uq',
                    'unique' => true,
                ]
            )
            ->update();
    }
}
