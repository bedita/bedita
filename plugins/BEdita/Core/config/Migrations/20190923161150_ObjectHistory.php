<?php
use Migrations\AbstractMigration;

class ObjectHistory extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public $autoId = false;

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $enum = in_array('enum', $columnTypes) ? 'enum' : 'string';
        $json = in_array('json', $columnTypes) ? 'json' : 'text';

        $this->table('object_history')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('object_id', 'integer', [
                'comment' => 'link to object',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'comment' => 'change action time',
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'comment' => 'link to user',
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('application_id', 'integer', [
                'comment' => 'link to application',
                'limit' => 10,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('user_action', $enum, [
                'comment' => 'user action (create, update, trash, restore, remove)',
                'default' => 'update',
                'limit' => 255,
                'values' => ['create', 'update', 'trash', 'restore', 'remove'],
                'null' => true,
            ])
            ->addColumn('changed', $json, [
                'comment' => 'changed data (JSON format)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'object_id',
                ],
                [
                    'name' => 'objecthistory_objectid_idx',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'objecthistory_userid_idx',
                ]
            )
            ->create();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('object_history')
            ->drop();
    }
}
