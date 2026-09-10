<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AsyncJobsAddColumnOptions extends BaseMigration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $json = in_array('json', $columnTypes) ? 'json' : 'text';
        $this->table('async_jobs')
            ->addColumn('job_options', $json, [
                'after' => 'priority',
                'comment' => 'Job options (JSON)',
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
            ->removeColumn('job_options')
            ->update();
    }
}
