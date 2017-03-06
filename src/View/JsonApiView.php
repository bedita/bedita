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
    protected $_specialVars = ['_serialize', '_jsonOptions', '_jsonp', '_error', '_links', '_meta', '_type'];

    /**
     * {@inheritDoc}
     */
    protected function _dataToSerialize($serialize = true)
    {
        $type = null;
        if (empty($this->viewVars['_error'])) {
            if (!empty($this->viewVars['_type'])) {
                $type = $this->viewVars['_type'];
            }

            $data = parent::_dataToSerialize($serialize) ?: [];
            if ($data) {
                $data = JsonApi::formatData(reset($data), $type);
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

        return compact('error', 'data', 'links', 'meta');
    }
}
