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

use BEdita\Core\Utility\System;

/**
 * Controller for `/status` endpoint.
 *
 * @since 4.0.0
 */
class StatusController extends AppController
{

    /**
     * Show system status info
     *
     * @return void
     */
    public function index()
    {
        $this->request->allowMethod('get');

        $status = System::status();
        $this->set('_meta', compact('status'));
        $this->set('_serialize', []);
    }
}
