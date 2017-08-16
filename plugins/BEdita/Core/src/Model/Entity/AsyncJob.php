<?php
namespace BEdita\Core\Model\Entity;

use BEdita\Core\Job\ServiceRegistry;
use BEdita\Core\Utility\JsonApiSerializable;
use Cake\I18n\Time;
use Cake\ORM\Entity;

/**
 * Asynchronous Job Entity
 *
 * @property string $uuid
 * @property string $service
 * @property int $priority
 * @property array $payload
 * @property \Cake\I18n\Time $scheduled_from
 * @property \Cake\I18n\Time $expires
 * @property int $max_attempts
 * @property \Cake\I18n\Time $locked_until
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $completed
 * @property string $status
 *
 * @since 4.0.0
 */
class AsyncJob extends Entity implements JsonApiSerializable
{

    use JsonApiAdminTrait;

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        'uuid' => true,
        'service' => true,
        'priority' => true,
        'payload' => true,
        'scheduled_from' => true,
        'expires' => true,
        'max_attempts' => true,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_virtual = [
        'status',
    ];

    /**
     * Magic getter for status.
     *
     * @return string
     */
    protected function _getStatus()
    {
        if ($this->completed !== null) {
            return 'completed';
        }

        $now = new Time();
        if ($this->locked_until !== null && $this->locked_until->gte($now)) {
            return 'locked';
        }
        if ($this->max_attempts === 0 || ($this->expires !== null && $this->expires->lt($now))) {
            return 'failed';
        }
        if ($this->scheduled_from !== null && $this->scheduled_from->gt($now)) {
            return 'planned';
        }

        return 'pending';
    }

    /**
     * Run this asynchronous job.
     *
     * @param array $options Additional options.
     * @return bool
     * @throws \BadMethodCallException Throws an exception if job hasn't been locked.
     */
    public function run(array $options = [])
    {
        if ($this->status !== 'locked') {
            throw new \BadMethodCallException('Only locked jobs can be run');
        }

        $service = ServiceRegistry::get($this->service);

        return $service->run($this->payload, $options);
    }
}
