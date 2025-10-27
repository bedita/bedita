<?php

use Migrations\BaseMigration;

class AddAsyncJobsTableIndex extends BaseMigration
{
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
        $this->table('async_jobs')
            ->addIndex(
                [
                    'created',
                ],
                [
                    'name' => 'asyncjobs_created_idx',
                ]
            )
            ->addIndex(
                [
                    'modified',
                ],
                [
                    'name' => 'asyncjobs_modified_idx',
                ]
            )
            ->update();
    }
}
