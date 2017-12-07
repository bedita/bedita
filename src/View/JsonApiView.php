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

namespace BEdita\API\View;

use BEdita\API\Utility\JsonApi;
use Cake\View\JsonView;

/**
 * A view class that is used for API responses.
 *
 * @since 4.0.0
 */
class JsonApiView extends JsonView
{
    /**
     * {@inheritDoc}
     */
    protected $_responseType = 'jsonapi';

    /**
     * {@inheritDoc}
     */
    protected $_specialVars = ['_serialize', '_jsonOptions', '_jsonp', '_error', '_links', '_meta', '_fields', '_jsonApiOptions'];

    /**
     * {@inheritDoc}
     */
    protected function _dataToSerialize($serialize = true)
    {
        if (empty($this->viewVars['_error'])) {
            $fields = $this->parseFieldsQuery();
            $data = parent::_dataToSerialize($serialize) ?: [];
            $options = !empty($this->viewVars['_jsonApiOptions']) ? $this->viewVars['_jsonApiOptions'] : 0;
            if ($data) {
                $included = [];
                $data = JsonApi::formatData(reset($data), $options, $fields, $included);
            }
            if (empty($included)) {
                unset($included);
            } else {
                $included = JsonApi::formatData($included, $options, $fields);
            }
        } else {
            $error = $this->viewVars['_error'];
        }

        if (!empty($error['status'])) {
            $error['status'] = (string)$error['status'];
        }

        if (!empty($this->viewVars['_links'])) {
            $links = $this->viewVars['_links'];
        }

        if (!empty($this->viewVars['_meta'])) {
            $meta = $this->viewVars['_meta'];
        }

        if (empty($serialize)) {
            unset($data);
        }

        return compact('error', 'data', 'links', 'meta', 'included');
    }

    /**
     * Return a formatted array from `fields` query string to apply common or sparse fields filters.
     * It's an associative array with type names as keys or `_common` key for type independent filters.
     * See http://jsonapi.org/format/#fetching-sparse-fieldsets
     *
     * @return array Formatted `fields` associative array
     */
    protected function parseFieldsQuery()
    {
        if (empty($this->viewVars['_fields'])) {
            return [];
        }
        if (!empty($this->viewVars['_fields']) && is_string($this->viewVars['_fields'])) {
            return ['_common' => explode(',', $this->viewVars['_fields'])];
        }
        $res = [];
        foreach ($this->viewVars['_fields'] as $type => $val) {
            $res[$type] = explode(',', $val);
        }

        return $res;
    }
}
