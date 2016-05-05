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
namespace BEdita\API\Controller\Component;

use Cake\Controller\Component;

/**
 * Handles JSON API data format in input and in output
 *
 */
class JsonApiComponent extends Component
{

    /**
     * Format response data array in JSON API format
     *
     * @param mixed $data Response data, could be an array or a Query / Entity
     * @param bool $multiple Multiple data flag, if true multiple items, if false single item
     * @param string $type Common type for response, if any
     * @return array
     */
    public function formatResponse($data, $multiple = true, $type = null)
    {
        $respData = null;
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        if ($multiple) {
            foreach ($data as $d) {
                $respData[] = $this->formatItem($d, $type);
            }
        } else {
            $respData = $this->formatItem($data, $type);
        }

        $controller = $this->_registry->getController();
        $url = $controller->request->scheme() . '://' . $controller->request->host() . '/' . $controller->request->url;
        $links = ['self' => $url];
        $res = [
            'links' => $links,
            'data' => $respData,
            '_serialize' => ['links', 'data'],
        ];
        return $res;
    }

    /**
     * Format single data item in JSON API format
     *
     * @param object $item Single entity item to format
     * @param string $type Type of item, if missing deduce it from item's data
     * @return array
     */
    protected function formatItem($item, $type = null)
    {
        if (!is_array($item)) {
            $itemData = $item->toArray();
        } else {
            $itemData = $item;
        }
        $data = [
            'id' => is_int($itemData['id']) ? strval($itemData['id']) : $itemData['id'],
            'type' => ($type !== null) ? $type : $itemData['type'],
        ];
        unset($itemData['id']);
        unset($itemData['type']);
        $data['attributes'] = $itemData;
        return $data;
    }
}
