<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

/**
 * UniqueName behavior
 *
 * Creates or updates a unique name of objects (see `objects.uname` field).
 *
 * Unique name is created typically from object title or from other object properties in case of missing title.
 * An object type may impose custom rule.
 * Name must be unique inside current project.
 *
 * @since 4.0.0
 */
class UniqueNameBehavior extends Behavior
{
    /**
     * Max regenerate iterations to avoid duplicates.
     *
     * @var int
     */
    const UNAME_MAX_REGENERATE = 10;

    /**
     * Max regenerate iterations to avoid duplicates.
     *
     * @var int
     */
    const UNAME_MAX_LENGTH = 255;

    /**
     * Default configuration.
     *
     * Possible keys are:
     *  - 'sourceField' field value to use for unique name creation
     *  - 'prefix' constant prefix to use
     *  - 'replacement' character replacement for space
     *  - 'preserve' non-word character to preserve when creating slug
     *  - 'separator' hash suffix separator
     *  - 'hashlength' hash suffix length
     *  - 'generator' callable function for unique name generation, if set all other keys are ignored
     *
     * @var array
     */
    protected $_defaultConfig = [
        'sourceField' => 'title',
        'prefix' => '',
        'replacement' => '-',
        'preserve' => '_',
        'separator' => '-',
        'hashlength' => 6,
        'generator' => null,
    ];

    /**
     * Setup unique name of a BEdita object $entity if a new entity is created
     * If no custom generator is used unique name is built using a friendly
     * url `slug` version of a `sourceField` (default 'title')
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @return void
     */
    public function uniqueName(EntityInterface $entity): void
    {
        if (!$entity->isNew() && !$entity->has('uname')) {
            return;
        }
        $uname = $entity->get('uname');
        if (empty($uname)) {
            $uname = $this->generateUniqueName($entity);
        } else {
            $uname = strtolower(Text::slug($uname, [
                'replacement' => $this->getConfig('replacement'),
                'preserve' => $this->getConfig('preserve'),
            ]));
            if ($uname === $entity->get('uname') && !$entity->isDirty('uname')) {
                return;
            }
        }
        $count = 0;
        while (
            $this->uniqueNameExists($uname, $entity->get('id'))
            && ($count++ < self::UNAME_MAX_REGENERATE)
        ) {
            $uname = $this->generateUniqueName($entity, true);
        }

        $entity->set('uname', Text::truncate($uname, self::UNAME_MAX_LENGTH));
    }

    /**
     * Generate unique name string from $config parameters.
     * If $regenerate parameter is true, random hash is added to uname string.
     * If the user has specifically set an uname, the `sourceField` config is ignored and the provided
     * uname value is used.
     * A 'callable' item is called if set in config('generator') instead of generateUniqueName(...)
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @param bool $regenerate if true it adds hash string to uname
     * @param array $cfg Optional config parameters to override defaults
     * @return string uname
     */
    public function generateUniqueName(EntityInterface $entity, bool $regenerate = false, array $cfg = []): string
    {
        $config = array_merge($this->getConfig(), $cfg);
        $generator = $config['generator'];
        if (is_callable($generator)) {
            return $generator($entity, $regenerate);
        }
        $fieldValue = $entity->get($config['sourceField']);
        if ($entity->isDirty('uname') && !empty($entity->get('uname'))) {
            $fieldValue = $entity->get('uname');
        }
        if (empty($fieldValue)) {
            $fieldValue = (string)$entity->get('type');
            $regenerate = true;
        }

        return $this->uniqueNameFromValue($fieldValue, $regenerate);
    }

    /**
     * Generate unique name string from $config parameters.
     * If $regenerate parameter is true, random hash is added to uname string.
     *
     * @param string $value String to use in unique name creation
     * @param bool $regenerate if true it adds hash string to uname
     * @param array $cfg parameters to create unique name
     * @return string uname
     */
    public function uniqueNameFromValue(string $value, bool $regenerate = false, array $cfg = []): string
    {
        $config = array_merge($this->getConfig(), $cfg);
        $slug = Text::slug($value, [
            'replacement' => $config['replacement'],
            'preserve' => $config['preserve'],
        ]);
        $uname = $config['prefix'] . $slug;
        if ($regenerate) {
            $hash = Text::uuid();
            $hash = str_replace('-', '', $hash);
            if (!empty($config['hashlength'])) {
                $hash = substr($hash, 0, $config['hashlength']);
            }
            $maxLen = self::UNAME_MAX_LENGTH - strlen($hash) - 1;
            $uname = Text::truncate($uname, $maxLen) . $config['separator'] . $hash;
        }

        return strtolower($uname);
    }

    /**
     * Verify $uname is unique
     *
     * @param string $uname to check
     * @param int|null $id object id to exclude from check
     * @return bool
     */
    public function uniqueNameExists(string $uname, int $id = null): bool
    {
        $options = ['uname' => $uname];
        if (!empty($id)) {
            $options['id <>'] = $id;
        }

        return TableRegistry::getTableLocator()->get('Objects')->exists($options);
    }

    /**
     * Setup unique name for a BEdita object represented by $entity
     * through `uniqueName()` method
     *
     * @param \Cake\Event\Event $event The event dispatched
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @return void
     */
    public function beforeRules(\Cake\Event\EventInterface $event, EntityInterface $entity)
    {
        $this->uniqueName($entity);
    }
}
