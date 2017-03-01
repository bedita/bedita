<?php
use Migrations\AbstractMigration;

class RemovePluralized extends AbstractMigration
{

    public $autoId = false;

    public function up()
    {
        $this->table('object_types')
            ->addColumn('singular', 'string', [
                'after' => 'name',
                'comment' => 'singular object type name',
                'default' => null,
                'length' => 50,
                'null' => true,
            ])
            ->update();

        $this->query('UPDATE object_types set singular = name');
        $this->query('UPDATE object_types set name = pluralized');

        $this->table('object_types')
            ->changeColumn('singular', 'string', ['null' => false])
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

        $this->table('object_types')
            ->removeColumn('pluralized')
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

