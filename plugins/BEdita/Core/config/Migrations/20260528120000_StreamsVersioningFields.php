<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class StreamsVersioningFields extends BaseMigration
{
    /**
     * @inheritDoc
     */
    public function change(): void
    {
        $this->table('streams')
            ->addColumn('created_by', 'integer', [
                'comment' => 'User who created the stream',
                'default' => null,
                'limit' => 11,
                'null' => true,
                'signed' => false,
            ])
            ->addIndex(
                ['created_by'],
                ['name' => 'streams_createdby_idx'],
            )
            ->addIndex(
                ['object_id', 'version'],
                [
                    'name' => 'streams_objectid_version_uq',
                    'unique' => true,
                ],
            )
            ->update();
    }
}
