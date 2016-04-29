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
namespace BEdita\API\Controller;

use Cake\Controller\Controller;

/**
 * Base class for all API Controller endpoints
 */
class AppController extends Controller
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->RequestHandler->renderAs($this, 'json');
    }

    /**
     * Prepare response data, format using selected response format
     * (only JSON API at this point)
     *
     * @param mixed $data Response data, could be an array or a Query / Entity
     * @param bool $multiple Multiple data flag, if true multiple items, if false single item
     * @param string $type Common type for response, if any
     * @return void
     */
    protected function prepareResponseData($data, $multiple = true, $type = null)
    {
        $this->loadComponent('BEdita/API.JsonApi');
        $responseData = $this->JsonApi->formatResponse($data, $multiple, $type);
        $this->set($responseData);
    }
}
