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
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\NotAcceptableException;

/**
 * Base class for all API Controller endpoints
 */
class AppController extends Controller
{

    /**
     * Response content type, can be 'json' (default) 'jsonapi' or 'html'
     *
     * @var string
     */
    protected $responseType = 'json';

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        // check request "Accept:" content types
        $accepts = $this->request->accepts();
        $jsonAccepts = ['application/json', 'application/vnd.api+json'];
        if (!empty(array_intersect($jsonAccepts, $accepts))) {
            if (in_array('application/vnd.api+json', $accepts)) {
                $this->responseType = 'jsonapi';
            }
        } else {
            $htmlAccepts = ['text/html', 'application/xhtml+xml', 'application/xhtml', 'text/xhtml'];
            $acceptHml = array_intersect($htmlAccepts, $accepts);
            if (empty($acceptHml) || !(Configure::read('debug') || Configure::read('Accept.html'))) {
                throw new NotAcceptableException('Bad request content type "' . implode('" "', $accepts) . '"');
            }
            $this->responseType = 'html';
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event)
    {
        if ($this->responseType === 'html') {
            $this->viewBuilder()->layout('default_api');
            $templatePath = $this->viewBuilder()->templatePath();
            $templatePath = substr($templatePath, 0, strrpos($templatePath, DS));
            $this->viewBuilder()->templatePath($templatePath . 'Common');
            $this->viewBuilder()->template('html_json');
        } else {
            $this->RequestHandler->renderAs($this, 'json');
        }
    }


    /**
     * {@inheritDoc}
     */
    public function afterFilter(Event $event)
    {
        // setting response type before has no effect for 'jsonapi'
        // since JSON View changes type to 'application/json'
        $this->response->type($this->responseType);
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
