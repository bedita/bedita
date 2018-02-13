<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Utility;

use BEdita\Core\Utility\JsonApiSerializable;
use BEdita\Core\Utility\JsonSchema;
use Cake\Collection\CollectionInterface;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\Routing\Router;
use Cake\Utility\Hash;

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
     * @param \BEdita\Core\Utility\JsonApiSerializable|\BEdita\Core\Utility\JsonApiSerializable[] $items Items to be formatted.
     * @param int $options Serializer options.
     * @param array $fields Selected fields to view in `attributes` and `meta`, if empty (default) all fields are serialized
     * @param array $included Array to be populated with included resources.
     * @return array
     * @throws \InvalidArgumentException Throws an exception if `$item` could not be converted to array, or
     *      if required key `id` is unset or empty.
     */
    public static function formatData($items, $options = 0, array $fields = [], array &$included = [])
    {
        if ($items instanceof Query || $items instanceof CollectionInterface) {
            $items = $items->toList();
        }

        if (empty($items)) {
            return [];
        }

        $single = false;
        if (!is_array($items) || !Hash::numeric(array_keys($items))) {
            $single = true;
            $items = [$items];
            $options |= JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS;
        }

        $data = $types = [];
        foreach ($items as $item) {
            if (!$item instanceof JsonApiSerializable) {
                throw new \InvalidArgumentException(sprintf(
                    'Objects must implement "%s", got "%s" instead',
                    JsonApiSerializable::class,
                    is_object($item) ? get_class($item) : gettype($item)
                ));
            }

            $item = $item->jsonApiSerialize($options, $fields);
            if (isset($item['included'])) {
                $included = array_merge($included, $item['included']);
                foreach ($included as $inc) {
                    if (!empty($inc['type'])) {
                        $types[] = $inc['type'];
                    }
                }
                unset($item['included']);
            }

            $data[] = $item;
            if (!empty($item['attributes']) && !empty($item['type'])) {
                $types[] = $item['type'];
            }
        }

        $data = $single ? $data[0] : $data;
        $schema = static::metaSchema(array_filter(array_unique($types)));
        if (!empty($schema)) {
            $data['_schema'] = $schema;
        }

        return $data;
    }

    /**
     * Create meta schema info for types
     *
     * @param array $types Type names array
     * @return array
     */
    protected static function metaSchema($types)
    {
        $schema = [];
        foreach ($types as $type) {
            $info = static::schemaInfo($type);
            if ($info) {
                $schema[$type] = $info;
            }
        }

        return $schema;
    }

    /**
     * Get JSON Schema info for a $type: URL and revision.
     *
     * @param string $type Type name
     * @return array|null Schema info array or null if no suitable schema is found
     */
    public static function schemaInfo($type)
    {
        try {
            $revision = JsonSchema::schemaRevision($type);
        } catch (NotFoundException $ex) {
            return null;
        }

        return [
            '$id' => Router::url(
                [
                    '_name' => 'api:model:schema',
                    'type' => $type,
                ],
                true
            ),
            'revision' => $revision,
        ];
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
        if (empty($data)) {
            return [];
        }

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

        if (isset($item['meta']) && is_array($item['meta'])) {
            $data['_meta'] = $item['meta'];
        }

        return $data;
    }
}
