<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Controller;

use BEdita\API\Controller\AppController;

/**
 * Base class for controllers handling pure `application/json` content-type, not using JSON API
 *
 */
abstract class JsonBaseController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();
        if ($this->components()->has('JsonApi')) {
            $this->components()->unload('JsonApi');
        }
        $this->viewBuilder()->setClassName('Json');

        $this->RequestHandler->setConfig('viewClassMap.json', 'Json');
        $this->RequestHandler->setConfig('inputTypeMap.json', ['json_decode', true], false);
    }
}
