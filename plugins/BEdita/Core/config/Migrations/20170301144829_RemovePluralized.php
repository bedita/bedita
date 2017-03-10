<?php
use Migrations\AbstractMigration;

/**
 * Make pluralized form the most significant column for `object_types` table.
 */
class RemovePluralized extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public $autoId = false;

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->table('object_types')
            ->removeIndexByName('objecttypes_name_uq')
            ->removeIndexByName('objecttypes_plural_uq')
            ->update();

        $this->table('object_types')
            ->renameColumn('name', 'singular')
            ->update();

        $this->table('object_types')
            ->renameColumn('pluralized', 'name')
            ->update();

        $this->table('object_types')
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'objecttypes_name_uq',
                    'unique' => true,
                ]
            )
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
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->table('object_types')
            ->removeIndexByName('objecttypes_name_uq')
            ->removeIndexByName('objecttypes_singular_uq')
            ->update();

        $this->table('object_types')
            ->renameColumn('name', 'pluralized')
            ->update();

        $this->table('object_types')
            ->renameColumn('singular', 'name')
            ->update();

        $this->table('object_types')
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'objecttypes_name_uq',
                    'unique' => true,
                ]
            )
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
