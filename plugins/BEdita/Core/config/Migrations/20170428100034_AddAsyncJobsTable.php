<?php
use Migrations\AbstractMigration;

/**
 * Add `async_jobs` table.
 */
class AddAsyncJobsTable extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public function up()
    {

        $this->table('async_jobs', ['id' => false, 'primary_key' => ['uuid']])
            ->addColumn('uuid', 'uuid', [
                'limit' => null,
                'null' => false,
                'comment' => 'Unique identifier for the job',
            ])
            ->addColumn('service', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Service type',
            ])
            ->addColumn('priority', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
                'signed' => false,
                'comment' => 'Job priority - jobs with higher priority run first',
            ])
            ->addColumn('payload', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'comment' => 'JSON payload being passed to job runner',
            ])
            ->addColumn('scheduled_from', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'comment' => 'Timestamp at which this job becomes valid',
            ])
            ->addColumn('expires', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'comment' => 'Timestamp at which this job becomes expired',
            ])
            ->addColumn('max_attempts', 'integer', [
                'default' => '1',
                'limit' => 3,
                'null' => false,
                'signed' => false,
                'comment' => 'Maximum number of attempts left for this job - it is decremented after each try',
            ])
            ->addColumn('locked_until', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'comment' => 'Timestamp at which the lock expires - runners lock jobs for a given amount of time',
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
                'comment' => 'Timestamp at which this job was created',
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
                'comment' => 'Timestamp at which this job was last modified',
            ])
            ->addColumn('completed', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'comment' => 'Timestamp at which this job was marked as completed',
            ])
            ->addIndex(
                [
                    'service',
                ],
                [
                    'name' => 'asyncjobs_service_idx',
                ]
            )
            ->addIndex(
                [
                    'priority',
                ],
                [
                    'name' => 'asyncjobs_priority_idx',
                ]
            )
            ->addIndex(
                [
                    'scheduled_from',
                ],
                [
                    'name' => 'asyncjobs_scheduledfrom_idx',
                ]
            )
            ->addIndex(
                [
                    'expires',
                ],
                [
                    'name' => 'asyncjobs_expires_idx',
                ]
            )
            ->addIndex(
                [
                    'locked_until',
                ],
                [
                    'name' => 'asyncjobs_lockeduntil_idx',
                ]
            )
            ->addIndex(
                [
                    'max_attempts',
                ],
                [
                    'name' => 'asyncjobs_maxattempts_idx',
                ]
            )
            ->addIndex(
                [
                    'completed',
                ],
                [
                    'name' => 'asyncjobs_completed_idx',
                ]
            )
            ->create();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->dropTable('async_jobs');
    }
}

