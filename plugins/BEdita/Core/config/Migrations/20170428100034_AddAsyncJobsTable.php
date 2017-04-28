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
            ])
            ->addColumn('service', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('priority', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('payload', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('not_before', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('not_after', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('max_attempts', 'integer', [
                'default' => '1',
                'limit' => 3,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('locked_until', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('completed', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
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
                    'not_before',
                ],
                [
                    'name' => 'asyncjobs_notbefore_idx',
                ]
            )
            ->addIndex(
                [
                    'not_after',
                ],
                [
                    'name' => 'asyncjobs_notafter_idx',
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

