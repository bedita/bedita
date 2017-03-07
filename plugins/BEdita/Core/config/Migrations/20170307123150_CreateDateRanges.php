<?php
use Migrations\AbstractMigration;

class CreateDateRanges extends AbstractMigration
{

    public $autoId = false;

    public function up()
    {

        $this->table('date_ranges')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('object_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('start_date', 'datetime', [
                'comment' => 'range start date time',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('end_date', 'datetime', [
                'comment' => 'range end date time',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('params', 'text', [
                'comment' => 'calendar params in JSON format: e.g. days of week',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'object_id',
                ],
                [
                    'name' => 'dateranges_objectid_idx',
                ]
            )
            ->create();

        $this->table('date_ranges')
            ->addForeignKey(
                'object_id',
                'objects',
                'id',
                [
                    'constraint' => 'dateranges_objectid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('date_ranges')
            ->dropForeignKey(
                'object_id'
            );

        $this->dropTable('date_ranges');
    }
}

