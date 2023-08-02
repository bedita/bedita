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

use BEdita\API\Policy\EndpointPolicy;
use BEdita\Core\Utility\System;
use Cake\Core\Configure;

/**
 * Controller for `/sysinfo` endpoint.
 *
 * @since 5.13.9
 */
class SysinfoController extends AppController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $htmlRequest = (Configure::read('debug') || Configure::read('Accept.html')) && $this->request->is('html');
        if (!$this->request->is('jsonapi') && !$htmlRequest) {
            $this->request = $this->request->withHeader('Accept', 'application/json');
        }

        parent::initialize();

        $this->request = $this->request->withAttribute(EndpointPolicy::ADMINISTRATOR_ONLY, true);

        if ($this->JsonApi) {
            $this->JsonApi->setConfig('checkMediaType', false);
        }
    }

    /**
     * Show system info
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->request->allowMethod(['get', 'head']);

        if ($this->request->is('head')) {
            return $this->response;
        }

        $info = System::info();
        $this->set('_meta', compact('info'));
        $this->setSerialize([]);

        return null;
    }
}
