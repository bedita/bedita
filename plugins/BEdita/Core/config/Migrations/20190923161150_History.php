<?php
use Migrations\AbstractMigration;

class History extends AbstractMigration
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

        $this->table('history')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('resource_id', 'string', [
                'comment' => 'resource identifier, may be integer or string',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('resource_type', 'string', [
                'comment' => 'resource type name, defaults to objects',
                'default' => 'objects',
                'limit' => 128,
                'null' => true,
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
                'null' => true,
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
                    'resource_id',
                ],
                [
                    'name' => 'history_resourceid_idx',
                ]
            )
            ->addIndex(
                [
                    'resource_type',
                ],
                [
                    'name' => 'history_resourcetype_idx',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'history_userid_idx',
                ]
            )
            ->create();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('history')
            ->drop()
            ->save();
    }
}
