<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Utility;

use BEdita\Core\ORM\Association\ExtensionOf;
use Cake\Collection\CollectionInterface;
use Cake\ORM\Association;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * JSON API formatter API.
 *
 * @since 4.0.0
 */
class JsonApi
{
    /**
     * Format single or multiple data items in JSON API format.
     *
     * @param mixed $items Items to be formatted.
     * @param string|null $type Type of items. If missing, an attempt is made to obtain this info from each item's data.
     * @return array
     * @throws \InvalidArgumentException Throws an exception if `$item` could not be converted to array, or
     *      if required key `id` is unset or empty.
     */
    public static function formatData($items, $type = null)
    {
        if ($items instanceof Query || $items instanceof CollectionInterface) {
            $items = $items->toList();
        }

        if (!is_array($items) || !Hash::numeric(array_keys($items))) {
            return static::formatItem($items, $type, false);
        }

        $data = [];
        foreach ($items as $item) {
            $data[] = static::formatItem($item, $type);
        }

        return $data;
    }

    /**
     * Extract API endpoint for item.
     *
     * @param \Cake\ORM\Entity|array $item Item.
     * @param string|null $type Original item type.
     * @return mixed
     */
    protected static function extractEndpoint($item, $type)
    {
        if ($type === null && isset($item['type'])) {
            return $item['type'];
        }

        return $type;
    }

    /**
     * Extract item's ID, type and attributes.
     *
     * @param array $item Item's data.
     * @param string|null $type Original item type.
     * @return array Array with item's ID, type and attributes.
     */
    protected static function extractAttributes(array $item, $type)
    {
        if (empty($item['id'])) {
            throw new \InvalidArgumentException('Key `id` is mandatory');
        }
        $id = (string)$item['id'];
        unset($item['id']);

        if ($type === null && isset($item['type'])) {
            $type = $item['type'];
        } elseif ($type === 'objects' && isset($item['object_type'], $item['object_type']['name'])) {
            $type = $item['object_type']['name'];
        }
        unset($item['type'], $item['object_type']);

        array_walk(
            $item,
            function (&$attribute) {
                if ($attribute instanceof \JsonSerializable) {
                    $attribute = json_decode(json_encode($attribute), true);
                }
            }
        );

        return [$id, $type, $item];
    }

    /**
     * Extract relationships for an entity.
     *
     * @param Entity $entity Entity item.
     * @param string $endpoint Default API endpoint for entity type.
     * @return array
     */
    protected static function extractRelationships(Entity $entity, $endpoint)
    {
        $relationships = [];
        $associations = TableRegistry::get($entity->source())->associations();
        $relatedParam = sprintf('%s_id', Inflector::singularize($endpoint));

        foreach ($associations as $association) {
            if (!($association instanceof Association) || $association instanceof ExtensionOf) {
                continue;
            }

            $name = $association->property();

            try {
                $self = Router::url(
                    [
                        '_name' => sprintf('api:%s:relationships', $endpoint),
                        'id' => $entity->id,
                        'relationship' => $name,
                    ],
                    true
                );
            } catch (MissingRouteException $e) {
            }

            try {
                $related = Router::url(
                    [
                        '_name' => sprintf('api:%s:%s', $endpoint, $name),
                        $relatedParam => $entity->id,
                    ],
                    true
                );
            } catch (MissingRouteException $e) {
            }

            if (empty($self) && empty($related)) {
                continue;
            }

            $relationships[$name] = [
                'links' => compact('related', 'self'),
            ];
        }

        return $relationships;
    }

    /**
     * Format single data item in JSON API format.
     *
     * @param \Cake\ORM\Entity|array $item Single entity item to be formatted.
     * @param string|null $type Type of item. If missing, an attempt is made to obtain this info from item's data.
     * @param bool $showLink Display item url in 'links.self', default is true
     * @return array
     * @throws \InvalidArgumentException Throws an exception if `$item` could not be converted to array, or
     *      if required key `id` is unset or empty.
     */
    protected static function formatItem($item, $type = null, $showLink = true)
    {
        if (!is_array($item) && !($item instanceof Entity)) {
            throw new \InvalidArgumentException('Unsupported item type');
        }

        $endpoint = static::extractEndpoint($item, $type);

        if ($item instanceof Entity) {
            $relationships = static::extractRelationships($item, $endpoint);
            if (empty($relationships)) {
                unset($relationships);
            }

            $item = $item->toArray();
        }

        if (empty($item)) {
            return [];
        }

        list($id, $type, $attributes) = static::extractAttributes($item, $type);
        if (empty($attributes)) {
            unset($attributes);
        }

        if ($showLink) {
            $links = [
                'self' => Router::url(['_name' => sprintf('api:%s:view', $endpoint), $id], true),
            ];
        }

        return compact('id', 'type', 'attributes', 'links', 'relationships');
    }

    /**
     * Parse single or multiple data items from JSON API format.
     *
     * @param array $data Items to be parsed.
     * @return array
     * @throws \InvalidArgumentException Throws an exception if one of required keys `id` and `type` is unset or empty.
     */
    public static function parseData(array $data)
    {
        if (!Hash::numeric(array_keys($data))) {
            return static::parseItem($data);
        }

        $items = [];
        foreach ($data as $item) {
            $items[] = static::parseItem($item);
        }

        return $items;
    }

    /**
     * Parse single data item from JSON API format.
     *
     * @param array $item Item to be parsed.
     * @return array
     * @throws \InvalidArgumentException Throws an exception if one of required keys `id` and `type` is unset or empty.
     */
    protected static function parseItem(array $item)
    {
        if (empty($item['type'])) {
            throw new \InvalidArgumentException('Key `type` is mandatory');
        }

        $data = [
            'type' => $item['type'],
        ];
        if (!empty($item['id'])) {
            $data['id'] = $item['id'];
        }

        if (isset($item['attributes']) && is_array($item['attributes'])) {
            $data += $item['attributes'];
        }

        return $data;
    }
}
