<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddIndexesToAsyncJobs extends BaseMigration
{
    /**
     * @inheritDoc
     */
    public function change()
    {
        $this->table('async_jobs')
            ->addIndex(
                ['service', 'created'],
                ['name' => 'asyncjobs_servicecreated_idx'],
            )
            ->update();
    }
}
