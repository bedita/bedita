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
use Cake\Network\Exception\NotAcceptableException;

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
        $accepts = $this->request->accepts();

        $jsonAccepts = ['application/json', 'application/vnd.api+json'];
        if (!empty(array_intersect($jsonAccepts, $accepts))) {
            $this->RequestHandler->renderAs($this, 'json');
            if (in_array('application/vnd.api+json', $accepts)) {
                $this->response->type('jsonapi');
            } else {
                $this->response->type('json');
            }
        } else {
            $htmlAccepts = ['text/xhtml', 'application/xhtml+xml', 'application/xhtml', 'text/html'];
            if (empty(array_intersect($htmlAccepts, $accepts))) {
                throw new NotAcceptableException('Bad request content type "' . implode('" "', $accepts) .
                    '" valid content types are: "' . implode('" "', array_merge($jsonAccepts, $htmlAccepts)) . '"');
            } else {
                // Set the layout.
                $this->viewBuilder()->layout('default-api');
            }
        }
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
