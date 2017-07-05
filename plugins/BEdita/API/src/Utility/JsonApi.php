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

use BEdita\Core\Utility\JsonApiSerializable;
use Cake\Collection\CollectionInterface;
use Cake\ORM\Query;
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
     * @param array $included Array to be populated with included resources.
     * @return array
     * @throws \InvalidArgumentException Throws an exception if `$item` could not be converted to array, or
     *      if required key `id` is unset or empty.
     */
    public static function formatData($items, array &$included = [])
    {
        if ($items instanceof Query || $items instanceof CollectionInterface) {
            $items = $items->toList();
        }

        if (empty($items)) {
            return [];
        }

        $single = false;
        $options = 0;
        if (!is_array($items) || !Hash::numeric(array_keys($items))) {
            $single = true;
            $items = [$items];
            $options |= JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS;
        }

        $data = [];
        foreach ($items as $item) {
            if (!$item instanceof JsonApiSerializable) {
                throw new \InvalidArgumentException(sprintf(
                    'Objects must implement "%s", got "%s" instead',
                    JsonApiSerializable::class,
                    is_object($item) ? get_class($item) : gettype($item)
                ));
            }

            $item = $item->jsonApiSerialize($options);
            if (isset($item['included'])) {
                $included = array_merge($included, $item['included']);
                unset($item['included']);
            }

            $data[] = $item;
        }

        return $single ? $data[0] : $data;
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
