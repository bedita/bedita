<?php
namespace BEdita\Core\Model\Entity;

use Cake\I18n\Time;
use Cake\ORM\Entity;

/**
 * Asynchronous Job Entity
 *
 * @property string $uuid
 * @property string $service
 * @property int $priority
 * @property array $payload
 * @property \Cake\I18n\Time $not_before
 * @property \Cake\I18n\Time $not_after
 * @property int $max_attempts
 * @property \Cake\I18n\Time $locked_until
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $completed
 * @property string $status
 *
 * @since 4.0.0
 */
class AsyncJob extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        'uuid' => true,
        'service' => true,
        'priority' => true,
        'payload' => true,
        'not_before' => true,
        'not_after' => true,
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
        if ($this->max_attempts === 0 || ($this->not_after !== null && $this->not_after->lt($now))) {
            return 'failed';
        }
        if ($this->not_before !== null && $this->not_before->gt($now)) {
            return 'planned';
        }

        return 'pending';
    }

    /**
     * Run this asynchronous job.
     *
     * @return bool
     * @todo Implement this method!
     */
    public function run()
    {
        return false;
    }
}
