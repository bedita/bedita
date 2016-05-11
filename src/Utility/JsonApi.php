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

use Cake\Collection\CollectionInterface;
use Cake\ORM\Entity;
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
            return static::formatItem($items, $type);
        }

        $data = [];
        foreach ($items as $item) {
            $data[] = static::formatItem($item, $type);
        }

        return $data;
    }

    /**
     * Format single data item in JSON API format.
     *
     * @param \Cake\ORM\Entity|array $item Single entity item to be formatted.
     * @param string|null $type Type of item. If missing, an attempt is made to obtain this info from item's data.
     * @return array
     * @throws \InvalidArgumentException Throws an exception if `$item` could not be converted to array, or
     *      if required key `id` is unset or empty.
     */
    protected static function formatItem($item, $type = null)
    {
        if ($item instanceof Entity) {
            $item = $item->toArray();
        }

        if (!is_array($item)) {
            throw new \InvalidArgumentException('Unsupported item type');
        }

        if (empty($item)) {
            return [];
        }

        if (empty($item['id'])) {
            throw new \InvalidArgumentException('Key `id` is mandatory');
        }

        $attributes = $item;

        $id = (string)$attributes['id'];
        unset($attributes['id']);

        if ($type === null && isset($attributes['type'])) {
            $type = $attributes['type'];
            unset($attributes['type']);
        }

        foreach ($attributes as &$attribute) {
            if ($attribute instanceof \JsonSerializable) {
                $attribute = json_decode(json_encode($attribute), true);
            }
        }
        unset($attribute);

        return compact('id', 'type', 'attributes');
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
        if (empty($item['id']) || empty($item['type'])) {
            throw new \InvalidArgumentException('Keys `id` and `type` are mandatory');
        }

        $data = [
            'id' => $item['id'],
            'type' => $item['type'],
        ];

        if (isset($item['attributes']) && is_array($item['attributes'])) {
            $data += $item['attributes'];
        }

        return $data;
    }
}
