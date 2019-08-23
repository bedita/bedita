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
use Cake\Event\EventManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;
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
    public function __construct(
        ServerRequest $request = null,
        Response $response = null,
        EventManager $eventManager = null,
        array $viewOptions = []
    ) {
        if ($request && $request->is('json')) {
            // change default response type if request is `json`
            $this->_responseType = 'json';
        }
        parent::__construct($request, $response, $eventManager, $viewOptions);
    }

    /**
     * {@inheritDoc}
     */
    protected function _dataToSerialize($serialize = true)
    {
        if (!empty($this->viewVars['_error'])) {
            return $this->serializeError();
        }

        $links = Hash::get($this->viewVars, '_links');
        $meta = Hash::get($this->viewVars, '_meta');
        if (empty($serialize)) {
            return array_filter(compact('links', 'meta'));
        }

        $fields = $this->parseFieldsQuery();
        $data = parent::_dataToSerialize() ?: [];
        $options = !empty($this->viewVars['_jsonApiOptions']) ? $this->viewVars['_jsonApiOptions'] : 0;
        if ($data) {
            $included = [];
            $data = JsonApi::formatData(reset($data), $options, $fields, $included);
        }

        if (!empty($data['_schema'])) {
            $meta['schema'] = $data['_schema'];
            unset($data['_schema']);
        }

        // `data` key may be empty, `links` and `meta` may not
        $res = compact('data') + array_filter(compact('links', 'meta'));

        if (!empty($included)) {
            $included = JsonApi::formatData($included, $options, $fields);
            unset($included['_schema']);
            $res += compact('included');
        }

        return $res;
    }

    /**
     * Serialize error data
     *
     * @return array
     */
    protected function serializeError()
    {
        $error = $this->viewVars['_error'];
        if (!empty($error['status'])) {
            $error['status'] = (string)$error['status'];
        }
        $links = Hash::get($this->viewVars, '_links');
        $meta = Hash::get($this->viewVars, '_meta');

        return array_filter(compact('error', 'links', 'meta'));
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
        if (is_string($this->viewVars['_fields'])) {
            return ['_common' => explode(',', $this->viewVars['_fields'])];
        }
        $res = [];
        foreach ($this->viewVars['_fields'] as $type => $val) {
            $res[$type] = explode(',', $val);
        }

        return $res;
    }
}
