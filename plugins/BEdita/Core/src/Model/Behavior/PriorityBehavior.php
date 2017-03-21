<?php
namespace BEdita\Core\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\Utility\Hash;

/**
 * Behavior to manage priorities.
 *
 * @since 4.0.0
 */
class PriorityBehavior extends Behavior
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'fields' => [],
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $defaultConfig = (array)$this->getConfig('fields._all');
        $defaultConfig += [
            'startFrom' => 0,
            'scope' => false,
        ];

        $fields = Hash::normalize($this->getConfig('fields'));
        unset($fields['_all']);
        foreach ($fields as $field => &$config) {
            $config = (array)$config + $defaultConfig;
        }
        unset($config);

        $this->setConfig('fields', $fields, false);
    }

    /**
     * Set up priorities before an entity is saved.
     *
     * @todo At the present state, priorities are simply set as `0` if missing.
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        $fields = $this->getConfig('fields');

        foreach ($fields as $field => $config) {
            if ($entity->has($field)) {
                continue;
            }

            $entity->set($field, $config['startFrom']);
        }
    }

    /**
     * Compact other priorities before the entity is deleted.
     *
     * @todo Implement this method.
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @return void
     */
    public function beforeDelete(Event $event, EntityInterface $entity)
    {
        return;
    }
}
