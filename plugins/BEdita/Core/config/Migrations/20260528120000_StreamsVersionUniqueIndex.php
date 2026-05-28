<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class StreamsVersionUniqueIndex extends BaseMigration
{
    /**
     * @inheritDoc
     */
    public function change(): void
    {
        $this->table('streams')
            ->addIndex(
                ['object_id', 'version'],
                [
                    'name' => 'streams_objectid_version_uidx',
                    'unique' => true,
                ],
            )
            ->update();
    }
}
