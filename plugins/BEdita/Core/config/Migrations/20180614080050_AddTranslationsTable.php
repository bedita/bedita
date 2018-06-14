<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

use Migrations\AbstractMigration;

/**
 * Migration class to create `translations` table.
 */
class AddTranslationsTable extends AbstractMigration
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

        $this->table('translations')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('object_id', 'integer', [
                'comment' => 'link to annotated object',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('lang', 'string', [
                'comment' => 'language tag, RFC 5646',
                'limit' => 64,
                'null' => false,
            ])
            ->addColumn('status', $enum, [
                'comment' => 'translation status: on, draft, off, deleted',
                'default' => 'draft',
                'limit' => 255,
                'values' => ['on', 'off', 'draft'],
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
            ])
            ->addColumn('created_by', 'integer', [
                'comment' => 'user who created translation',
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('modified_by', 'integer', [
                'comment' => 'last user who modified translation',
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('fields', 'text', [
                'comment' => 'translated fields (JSON format)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'object_id',
                    'lang',
                ],
                [
                    'name' => 'translations_objectidlang_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'created_by',
                ],
                [
                    'name' => 'translations_createdby_idx',
                ]
            )
            ->addIndex(
                [
                    'modified_by',
                ],
                [
                    'name' => 'translations_modifiedby_idx',
                ]
            )
            ->addIndex(
                [
                    'status',
                ],
                [
                    'name' => 'translations_status_idx',
                ]
            )
            ->create();

        $this->table('translations')
            ->addForeignKey(
                'object_id',
                'objects',
                'id',
                [
                    'constraint' => 'translations_objectid_fk',
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ]
            )
            ->addForeignKey(
                'created_by',
                'users',
                'id',
                [
                    'constraint' => 'translations_createdby_fk',
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT',
                ]
            )
            ->addForeignKey(
                'modified_by',
                'users',
                'id',
                [
                    'constraint' => 'translations_modifiedby_fk',
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT',
                ]
            )
            ->update();

        $this->table('objects')
            ->changeColumn('lang', 'string', [
                'comment' => 'language tag, RFC 5646',
                'default' => null,
                'limit' => 64,
                'null' => true,
            ])
            ->addIndex(
                [
                    'lang',
                ],
                [
                    'name' => 'objects_lang_idx',
                ]
            )
            ->addIndex(
                [
                    'status',
                ],
                [
                    'name' => 'objects_status_idx',
                ]
            )
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('objects')
            ->removeIndexByName('objects_lang_idx')
            ->removeIndexByName('objects_status_idx')
            ->changeColumn('lang', 'char', [
                'comment' => 'language used, ISO 639-3 code',
                'default' => null,
                'limit' => 3,
                'null' => true,
            ])
            ->update();

        $this->table('translations')
            ->dropForeignKey('object_id')
            ->dropForeignKey('created_by')
            ->dropForeignKey('modified_by')
            ->update();

        $this->table('translations')
            ->drop();
    }
}
