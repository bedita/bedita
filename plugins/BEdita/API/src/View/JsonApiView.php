<?php
/**
 * BEdita - a semantic content management framework
 * Copyright (C) 2008-2016  Chia Lab s.r.l., Channelweb s.r.l.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
    protected $_responseType = 'jsonApi';

    /**
     * {@inheritDoc}
     */
    protected $_specialVars = ['_serialize', '_jsonOptions', '_jsonp', '_links', '_meta', '_type'];

    /**
     * {@inheritDoc}
     */
    protected function _dataToSerialize($serialize = true)
    {
        $type = null;
        if (!empty($this->viewVars['_type'])) {
            $type = $this->viewVars['_type'];
        }

        $data = parent::_dataToSerialize($serialize) ?: [];
        if ($data) {
            $data = JsonApi::formatData(reset($data), $type);
        }

        if (!empty($this->viewVars['_links'])) {
            $links = $this->viewVars['_links'];
        }

        if (!empty($this->viewVars['_meta'])) {
            $meta = $this->viewVars['_meta'];
        }

        return compact('data', 'links', 'meta');
    }
}
