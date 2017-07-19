<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
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
 * Refactor `media` table and add `streams` table.
 *
 * @since 4.0.0
 */
class AddStreamsTable extends AbstractMigration
{

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->table('media')
            ->removeIndexByName('media_hashfile_idx')
            ->update();

        $this->table('media')
            ->removeColumn('uri')
            ->removeColumn('mime_type')
            ->removeColumn('file_size')
            ->removeColumn('hash_file')
            ->removeColumn('original_name')
            ->removeColumn('media_uid')
            ->removeColumn('thumbnail')
            ->changeColumn('name', 'text', [
                'comment' => 'Media name',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('width', 'integer', [
                'comment' => 'Width (if applicable)',
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->changeColumn('height', 'integer', [
                'comment' => 'Height (if applicable)',
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->changeColumn('provider', 'string', [
                'comment' => 'External provider or service name',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->update();

        $this->table('streams', ['id' => false, 'primary_key' => ['uuid']])
            ->addColumn('uuid', 'uuid', [
                'comment' => 'Stream UUID',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('version', 'integer', [
                'comment' => 'Stream version',
                'default' => 1,
                'limit' => 11,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('object_id', 'integer', [
                'comment' => 'Object ID',
                'default' => null,
                'limit' => 11,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('uri', 'string', [
                'comment' => 'Path where physical file is stored',
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('file_name', 'text', [
                'comment' => 'Original file name',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('mime_type', 'string', [
                'comment' => 'Mime-type of file',
                'default' => 'application/octet-stream',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('file_size', 'integer', [
                'comment' => 'File size (in bytes)',
                'default' => null,
                'limit' => 11,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('hash_md5', 'string', [
                'comment' => 'MD5 hash',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('hash_sha1', 'string', [
                'comment' => 'SHA1 hash',
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'comment' => 'Creation time',
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'timestamp', [
                'comment' => 'Last modification time',
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'uri',
                ],
                [
                    'name' => 'streams_uri_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'object_id',
                ],
                [
                    'name' => 'streams_objectid_idx',
                ]
            )
            ->addIndex(
                [
                    'version',
                ],
                [
                    'name' => 'streams_version_idx',
                ]
            )
            ->addIndex(
                [
                    'mime_type',
                ],
                [
                    'name' => 'streams_mimetype_idx',
                ]
            )
            ->addIndex(
                [
                    'file_size',
                ],
                [
                    'name' => 'streams_filesize_idx',
                ]
            )
            ->addIndex(
                [
                    'hash_md5',
                ],
                [
                    'name' => 'streams_hashmd5_idx',
                ]
            )
            ->addIndex(
                [
                    'hash_sha1',
                ],
                [
                    'name' => 'streams_hashsha1_idx',
                ]
            )
            ->create();

        $this->table('streams')
            ->addForeignKey(
                'object_id',
                'objects',
                'id',
                [
                    'constraint' => 'streams_objectid_fk',
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->update();

        $this->table('media')
            ->addColumn('duration', 'integer', [
                'after' => 'height',
                'comment' => 'Duration (if applicable)',
                'default' => null,
                'length' => 10,
                'null' => true,
            ])
            ->addColumn('provider_uid', 'string', [
                'after' => 'provider',
                'comment' => 'ID from remote provider',
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->addColumn('provider_url', 'text', [
                'after' => 'provider_uid',
                'comment' => 'Remote URL',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addColumn('provider_thumbnail', 'string', [
                'after' => 'provider_url',
                'comment' => 'Remote thumbnail',
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->addIndex(
                [
                    'provider',
                    'provider_uid',
                ],
                [
                    'name' => 'media_provider_idx',
                ]
            )
            ->update();

        $this->table('object_types')
            ->insert([
                [
                    'singular' => 'media',
                    'name' => 'media',
                    'description' => 'Media model with streams',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Media',
                    'associations' => '["Streams"]',
                ],
                [
                    'name' => 'images',
                    'singular' => 'image',
                    'description' => 'Image model',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Media',
                    'associations' => '["Streams"]',
                ],
            ])
            ->save();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->table('streams')
            ->dropForeignKey(
                'object_id'
            );

        $this->table('media')
            ->removeIndexByName('media_provider_idx')
            ->update();

        $this->table('media')
            ->addColumn('uri', 'text', [
                'after' => 'id',
                'comment' => 'media uri: relative path on local filesystem or remote URL',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('mime_type', 'string', [
                'after' => 'name',
                'comment' => 'resource mime type',
                'default' => null,
                'length' => 255,
                'null' => false,
            ])
            ->addColumn('file_size', 'integer', [
                'after' => 'mime_type',
                'comment' => 'file size in bytes (if local)',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('hash_file', 'string', [
                'after' => 'file_size',
                'comment' => 'md5 hash of local file',
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->addColumn('original_name', 'text', [
                'after' => 'hash_file',
                'comment' => 'original name for uploaded file',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addColumn('media_uid', 'string', [
                'after' => 'provider',
                'comment' => 'uid, used for remote videos',
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->addColumn('thumbnail', 'string', [
                'after' => 'media_uid',
                'comment' => 'remote media thumbnail URL',
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->changeColumn('name', 'text', [
                'comment' => 'file name',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->changeColumn('width', 'integer', [
                'comment' => '(image) width',
                'default' => null,
                'length' => 6,
                'null' => true,
            ])
            ->changeColumn('height', 'integer', [
                'comment' => '(image) height',
                'default' => null,
                'length' => 6,
                'null' => true,
            ])
            ->changeColumn('provider', 'string', [
                'comment' => 'external provider/service name',
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->removeColumn('duration')
            ->removeColumn('provider_uid')
            ->removeColumn('provider_url')
            ->removeColumn('provider_thumbnail')
            ->addIndex(
                [
                    'hash_file',
                ],
                [
                    'name' => 'media_hashfile_idx',
                ]
            )
            ->update();

        $this->dropTable('streams');
    }
}

