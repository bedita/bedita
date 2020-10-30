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
use Cake\ORM\Exception\PersistenceFailedException;
use RuntimeException;

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
    const REGEX = '/<!--\s+BE-PLACEHOLDER\.(?P<id>\d+)\.(?P<params>[A-Za-z0-9+=-]+)\s+-->/';

    /**
     * Add associations using placeholder relation.
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity)
    {
        $properties = $this->getHTMLProperties($entity);

        $placeholders = array_unique(array_reduce(
            $properties,
            function (array $placeholders, string $property) use ($entity): array {
                return array_merge($placeholders, static::extractPlaceholders($entity->get($property)));
            },
            []
        ));

        $relatedEntities = array_map(function (int $id): array {
            return compact('id');
        }, $placeholders);
        $Association = $this->getTable()->getAssociation('Placeholder');
        if (!$Association->replaceLinks($entity, $relatedEntities)) {
            throw new PersistenceFailedException($entity, __d('bedita', 'Could not save placeholder relations'));
        }
    }

    /**
     * Parse HTML content and extracts media references.
     *
     * @param string $content The content to parse.
     * @return int[] A list of ids.
     */
    private static function extractPlaceholders(string $content): array
    {
        if (preg_match_all(static::REGEX, $content, $matches) === false) {
            throw new RuntimeException('Error extracting placeholders');
        }

        return $matches['id'];
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
