<?php
use Migrations\AbstractMigration;
use BEdita\Core\Utility\Resources;

class LinksTable extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $enum = in_array('enum', $columnTypes) ? 'enum' : 'string';

        // links
        $this->table('links', ['id' => false])
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('http_status', 'string', [
                'comment' => 'HTTP status',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('last_update', 'date', [
                'comment' => 'Last update date',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('links')
            ->addForeignKey(
                'id',
                'objects',
                'id',
                [
                    'constraint' => 'links_id_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        Resources::save(
            [
                'create' => [
                    'object_types' => [
                        [
                            'name' => 'links',
                            'singular' => 'link',
                            'description' => 'Links model',
                            'plugin' => 'BEdita/Core',
                            'model' => 'Links',
                        ],
                    ],
                ],
            ],
            ['connection' => $this->getAdapter()->getCakeConnection()]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('links')
            ->drop()
            ->save();
    }
}
