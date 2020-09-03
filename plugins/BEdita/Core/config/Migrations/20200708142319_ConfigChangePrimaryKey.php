<?php

use Aws\MediaPackageVod\Exception\MediaPackageVodException;
use Migrations\AbstractMigration;

class ConfigChangePrimaryKey extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public $autoId = false;

    /**
     * Config rows to keep
     *
     * @var array
     */
    protected $configRows = [];

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->loadConfigRows();
        $this->table('config')->drop()->save();

        $this->table('config')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'comment' => 'configuration key',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->create();

        $this->commonColumns();
        $this->restoreConfigRows();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->loadConfigRows();
        $this->table('config')->drop()->save();

        $this->table('config')
            ->addColumn('name', 'string', [
                'comment' => 'configuration key',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addPrimaryKey('name')
            ->create();

        $this->commonColumns();
        $this->restoreConfigRows();
    }

    /**
     * Add common columns
     *
     * @return void
     */
    protected function commonColumns(): void
    {
        $this->table('config')
            ->addColumn('context', 'string', [
                'comment' => 'group name of configuration parameters',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('content', 'text', [
                'comment' => 'configuration data as string or JSON',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('application_id', 'integer', [
                'comment' => 'link to applications.id - may be null',
                'default' => null,
                'limit' => 5,
                'null' => true,
                'signed' => false,
            ])
            ->addIndex(
                [
                    'context',
                ],
                [
                    'name' => 'config_context_idx',
                ]
            )
            ->addIndex(
                [
                    'application_id',
                ],
                [
                    'name' => 'config_applicationid_idx',
                ]
            )
            ->addIndex(
                [
                    'name',
                    'application_id',
                ],
                [
                    'name' => 'config_nameapplicationid_uq',
                    'unique' => true,
                ]
            )
            ->update();

        $this->table('config')
            ->addForeignKey(
                'application_id',
                'applications',
                'id',
                [
                    'constraint' => 'config_applicationid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'RESTRICT',
                ]
            )
            ->update();
    }

    /**
     * Store config table rows
     *
     * @return void
     */
    protected function loadConfigRows(): void
    {
        $this->configRows = $this->fetchAll("SELECT name, content, context, created, modified, application_id FROM config");
        // remove unwanted array items
        foreach ($this->configRows as &$row) {
            unset($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
        }
    }

    /**
     * Restore config table rows
     *
     * @return void
     */
    protected function restoreConfigRows(): void
    {
        $this->table('config')->insert($this->configRows)->save();
    }
}
