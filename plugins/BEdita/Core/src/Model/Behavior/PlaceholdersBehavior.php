<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Utility\JsonSchema;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;

/**
 * Placeholders behavior
 *
 * @since 4.3.0
 */
class PlaceholdersBehavior extends Behavior
{
    /**
     * The regex to use to interpolate placeholders data.
     *
     * @var string
     */
    protected static $regex = '/<!--\s+BE-PLACEHOLDER\.(\d+)\.([A-Za-z0-9+=-]+)\s+-->/';

    /**
     * Add associations using placeholder relation.
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        $properties = $this->getHTMLProperties($entity);
        $placeholders = [];

        foreach ($properties as $prop) {
            $placeholders += $this->extractPlaceholders($entity[$prop]);
        }

        // @TODO save associations
    }

    /**
     * Parse HTML content and extracts media references.
     *
     * @param string $content The content to parse.
     * @return array A list of ids.
     */
    private function extractPlaceholders(string $content)
    {
        try {
            $ids = [];
            // @TODO
            return $ids;
        } catch (Excpetion $error) {
            return [];
        }
    }

    /**
     * Get a list of properties names that accept text/html.
     *
     * @return array A list of properties.
     */
    private function getHTMLProperties()
    {
        $Table = $this->getTable();
        $objectType = $Table->behaviors()->call('objectType', [$Table->getAlias()]);
        $schema = JsonSchema::generate($objectType['name'], '/');

        if (empty($schema['properties'])) {
            return [];
        }

        $properties = [];
        foreach ($schema['properties'] as $name => $propertySchema) {
            if ($this->isHTMLType($propertySchema)) {
                $properties[] = $name;
            }
        }

        return $properties;
    }

    /**
     * Check if a property schema accepts text/html.
     *
     * @param array $propertySchema The property schema.
     * @return bool
     */
    private function isHTMLType($propertySchema)
    {
        if (!empty($propertySchema['oneOf'])) {
            return !empty(
                array_filter($propertySchema['oneOf'], function ($subProp) {
                    return $this->isHTMLType($subProp);
                })
            );
        }

        if (empty($propertySchema['contentMediaType'])) {
            return false;
        }

        return $propertySchema['contentMediaType'] === 'text/html';
    }
}
