<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Entity\AsyncJob;
use BEdita\Core\Model\Table\AsyncJobsTable;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Table\AsyncJobsTable
 */
class AsyncJobsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    public $AsyncJobs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.async_jobs',
    ];

    /**
     * Async job connection config.
     *
     * @var array
     */
    protected $connection;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->AsyncJobs = TableRegistry::get('AsyncJobs');

        if (in_array('async_jobs', ConnectionManager::configured())) {
            $this->connection = ConnectionManager::getConfig('async_jobs');
            ConnectionManager::drop('async_jobs');
        }
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AsyncJobs);

        if (in_array('async_jobs', ConnectionManager::configured())) {
            ConnectionManager::drop('async_jobs');
        }
        if (!empty($this->connection)) {
            ConnectionManager::setConfig('async_jobs', $this->connection);
        }

        parent::tearDown();
    }

    /**
     * Test default connection name.
     *
     * @return void
     *
     * @covers ::defaultConnectionName()
     */
    public function testDefaultConnectionName()
    {
        $connectionName = AsyncJobsTable::defaultConnectionName();
        static::assertEquals('default', $connectionName);

        ConnectionManager::setConfig('async_jobs', ConnectionManager::getConfig('default'));

        $connectionName = AsyncJobsTable::defaultConnectionName();
        static::assertEquals('async_jobs', $connectionName);
    }

    /**
     * Test locking.
     *
     * @return void
     *
     * @covers ::lock()
     */
    public function testLock()
    {
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';

        $eventDispatched = 0;
        $this->AsyncJobs->eventManager()->on('AsyncJob.lock', function () use (&$eventDispatched) {
            $eventDispatched++;

            /* @var \Cake\Database\Connection $connection */
            $connection = ConnectionManager::get('default');

            static::assertInstanceOf(AsyncJob::class, func_get_arg(1));
            static::assertTrue($connection->inTransaction());
        });

        $entity = $this->AsyncJobs->lock($uuid);

        static::assertSame(1, $eventDispatched);
        static::assertInstanceOf(AsyncJob::class, $entity);

        $entity = $this->AsyncJobs->get($uuid);
        static::assertNotNull($entity->locked_until);
        static::assertSame(0, $entity->max_attempts);
    }

    /**
     * Test locking a job that is not pending.
     *
     * @return void
     *
     * @covers ::lock()
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testLockNotPending()
    {
        $this->AsyncJobs->lock('6407afa6-96a3-4aeb-90c1-1541756efdef');
    }

    /**
     * Test unlocking a job after successful execution.
     *
     * @return void
     *
     * @covers ::unlock()
     */
    public function testUnlockSuccess()
    {
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        $success = 'Job completed successfully thanks to Gustavo Supporto!';

        $eventDispatched = 0;
        $this->AsyncJobs->eventManager()->on('AsyncJob.complete', function () use (&$eventDispatched, $success) {
            $eventDispatched++;

            /* @var \Cake\Database\Connection $connection */
            $connection = ConnectionManager::get('default');

            static::assertInstanceOf(AsyncJob::class, func_get_arg(1));
            static::assertSame($success, func_get_arg(2));
            static::assertTrue($connection->inTransaction());
        });
        $this->AsyncJobs->eventManager()->on('AsyncJob.fail', function () {
            static::fail('Wrong event dispatched');
        });

        $this->AsyncJobs->unlock($uuid, $success);

        static::assertSame(1, $eventDispatched);

        $entity = $this->AsyncJobs->get($uuid);
        static::assertNull($entity->locked_until);
        static::assertNotNull($entity->completed);
    }

    /**
     * Test unlocking a job after failed execution.
     *
     * @return void
     *
     * @covers ::unlock()
     */
    public function testUnlockFail()
    {
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        $success = false;

        $eventDispatched = 0;
        $this->AsyncJobs->eventManager()->on('AsyncJob.fail', function () use (&$eventDispatched, $success) {
            $eventDispatched++;

            /* @var \Cake\Database\Connection $connection */
            $connection = ConnectionManager::get('default');

            static::assertInstanceOf(AsyncJob::class, func_get_arg(1));
            static::assertSame($success, func_get_arg(2));
            static::assertTrue($connection->inTransaction());
        });
        $this->AsyncJobs->eventManager()->on('AsyncJob.complete', function () {
            static::fail('Wrong event dispatched');
        });

        $this->AsyncJobs->unlock($uuid, $success);

        static::assertSame(1, $eventDispatched);

        $entity = $this->AsyncJobs->get($uuid);
        static::assertNull($entity->locked_until);
        static::assertNull($entity->completed);
    }

    /**
     * Test finder for pending jobs.
     *
     * @return void
     *
     * @covers ::findPending()
     */
    public function testFindPending()
    {
        $expected = [
            'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c' => [
                'key' => 'value',
            ],
        ];

        $actual = $this->AsyncJobs->find('pending')->find('list')->toArray();

        static::assertSame($expected, $actual);
    }

    /**
     * Test finder for failed jobs.
     *
     * @return void
     *
     * @covers ::findFailed()
     */
    public function testFindFailed()
    {
        $expected = [
            '40e22034-213f-4028-9930-81c0ed79c5a6' => [
                'key' => 'value',
            ],
            '0c833458-dff1-4fbb-bbf6-a30818b60616' => [
                'key' => 'value',
            ],
        ];
        ksort($expected);

        $actual = $this->AsyncJobs->find('failed')->find('list')->toArray();
        ksort($actual);

        static::assertSame($expected, $actual);
    }

    /**
     * Test finder for completed jobs.
     *
     * @return void
     *
     * @covers ::findCompleted()
     */
    public function testFindCompleted()
    {
        $expected = [
            '1e2d1c66-c0bb-47d7-be5a-5bc92202333e' => [
                'key' => 'value',
            ],
        ];

        $actual = $this->AsyncJobs->find('completed')->find('list')->toArray();

        static::assertSame($expected, $actual);
    }
}
