<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * Migration for `captions` table.
 */
class CreateCaptions extends AbstractMigration
{
    public bool $autoId = false;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $limit = null;
        if ($this->getAdapter()->getAdapterType() === 'mysql') {
            $limit = MysqlAdapter::TEXT_MEDIUM;
        }

        $columnTypes = $this->getAdapter()->getColumnTypes();
        $type = in_array('json', $columnTypes) ? 'json' : 'text';
        $enum = in_array('enum', $columnTypes) ? 'enum' : 'string';

        $this->table('captions')
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
            ->addColumn('status', $enum, [
                'comment' => 'caption status: on, draft, off',
                'default' => 'draft',
                'limit' => 255,
                'values' => ['on', 'off', 'draft'],
                'null' => false,
            ])
            ->addColumn('label', 'string', [
                'comment' => 'caption label',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('format', 'string', [
                'comment' => 'caption format i.e. vtt, srt, etc.',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('lang', 'string', [
                'comment' => 'language tag, RFC 5646',
                'default' => null,
                'limit' => 64,
                'null' => true,
            ])
            ->addColumn('caption_text', 'text', [
                'comment' => 'caption text',
                'default' => null,
                'limit' => $limit,
                'null' => true,
            ])
            ->addColumn('params', $type, [
                'comment' => 'parameters for miscellaneous data (JSON)',
                'default' => null,
                'limit' => null,
                'null' => true,
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
            ->addIndex(
                [
                    'object_id',
                ],
                [
                    'name' => 'captions_objectid_idx',
                ]
            )
            ->addIndex(
                [
                    'status',
                ],
                [
                    'name' => 'captions_status_idx',
                ]
            )
            ->addIndex(
                [
                    'object_id',
                    'lang',
                    'format',
                ],
                [
                    'name' => 'captions_objectlangformat_uq',
                    'unique' => true,
                ]
            )
            ->create();

        $this->table('captions')
            ->addForeignKey(
                'object_id',
                'objects',
                'id',
                [
                    'constraint' => 'captions_objectid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE',
                ]
            )
            ->update();
    }
}
