<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * Async Jobs Fixture
 *
 * @since 4.0.0
 */
class AsyncJobsFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            // Pending.
            'uuid' => 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c',
            'service' => 'example',
            'priority' => 1,
            'payload' => '{"key":"value"}',
            'not_before' => null,
            'not_after' => null,
            'max_attempts' => 1,
            'locked_until' => null,
            'created' => '2017-04-28 19:29:31',
            'modified' => '2017-04-28 19:29:31',
            'completed' => null,
        ],
        [
            // Planned.
            'uuid' => '66594f3c-995f-49d2-9192-382baf1a12b3',
            'service' => 'example',
            'priority' => 1,
            'payload' => '{"key":"value"}',
            'not_before' => '+1 day',
            'not_after' => null,
            'max_attempts' => 1,
            'locked_until' => null,
            'created' => '2017-04-28 19:29:31',
            'modified' => '2017-04-28 19:29:31',
            'completed' => null,
        ],
        [
            // Completed.
            'uuid' => '1e2d1c66-c0bb-47d7-be5a-5bc92202333e',
            'service' => 'example',
            'priority' => 1,
            'payload' => '{"key":"value"}',
            'not_before' => null,
            'not_after' => null,
            'max_attempts' => 1,
            'locked_until' => null,
            'created' => '2017-04-28 19:29:31',
            'modified' => '2017-04-28 19:29:31',
            'completed' => '2017-04-28 19:29:31',
        ],
        [
            // Locked.
            'uuid' => '6407afa6-96a3-4aeb-90c1-1541756efdef',
            'service' => 'example',
            'priority' => 1,
            'payload' => '{"key":"value"}',
            'not_before' => null,
            'not_after' => null,
            'max_attempts' => 1,
            'locked_until' => '+1 day',
            'created' => '2017-04-28 19:29:31',
            'modified' => '2017-04-28 19:29:31',
            'completed' => null,
        ],
        [
            // No more attempts.
            'uuid' => '40e22034-213f-4028-9930-81c0ed79c5a6',
            'service' => 'example',
            'priority' => 1,
            'payload' => '{"key":"value"}',
            'not_before' => null,
            'not_after' => null,
            'max_attempts' => 0,
            'locked_until' => null,
            'created' => '2017-04-28 19:29:31',
            'modified' => '2017-04-28 19:29:31',
            'completed' => null,
        ],
        [
            // Expired.
            'uuid' => '0c833458-dff1-4fbb-bbf6-a30818b60616',
            'service' => 'example',
            'priority' => 1,
            'payload' => '{"key":"value"}',
            'not_before' => null,
            'not_after' => '1992-08-17 19:29:31',
            'max_attempts' => 1,
            'locked_until' => null,
            'created' => '2017-04-28 19:29:31',
            'modified' => '2017-04-28 19:29:31',
            'completed' => null,
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();

        foreach ($this->records as &$record) {
            if (!empty($record['not_before'])) {
                $record['not_before'] = date('Y-m-d H:i:s', strtotime($record['not_before']));
            }
            if (!empty($record['locked_until'])) {
                $record['locked_until'] = date('Y-m-d H:i:s', strtotime($record['locked_until']));
            }
        }
        unset($record);
    }
}
