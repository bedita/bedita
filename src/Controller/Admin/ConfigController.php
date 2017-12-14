<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller\Admin;

/**
 * Controller for `/admin/config` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\ConfigTable $Config
 */
class ConfigController extends AdminController
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        if (isset($this->JsonApi)) {
            $this->JsonApi->setConfig('clientGeneratedIds', true);
        }
    }

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Config';
}
