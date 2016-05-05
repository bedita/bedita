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

use BEdita\API\Utility\JsonApi;
use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Routing\Router;

/**
 * Handles JSON API data format in input and in output
 *
 * @since 4.0.0
 */
class JsonApiComponent extends Component
{
    /**
     * Get links according to JSON API specifications.
     *
     * @return array
     */
    public function getLinks()
    {
        return [
            'self' => Router::url(null, true),
        ];
    }

    /**
     * Format response data array in JSON API format
     *
     * @param mixed $data Response data, could be an array or a Query / Entity
     * @param string $type Common type for response, if any
     * @return array
     */
    public function formatResponse($data, $type = null)
    {
        $links = $this->getLinks();
        $data = JsonApi::formatData($data, $type);

        $res = [
            'links' => $links,
            'data' => $data,
            '_serialize' => ['links', 'data'],
        ];
        return $res;
    }
}
