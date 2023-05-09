<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAsyncJobsResults extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $json = in_array('json', $columnTypes) ? 'json' : 'text';
        $this->table('async_jobs')
            ->addColumn('results', $json, [
                'comment' => 'Job results (JSON array)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('async_jobs')
            ->removeColumn('results')
            ->update();
    }
}
