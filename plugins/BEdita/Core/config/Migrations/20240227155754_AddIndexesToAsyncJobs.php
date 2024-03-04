<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddIndexesToAsyncJobs extends AbstractMigration
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
