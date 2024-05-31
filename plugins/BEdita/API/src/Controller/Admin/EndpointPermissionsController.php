<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
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
 * Controller for `/admin/endpoint_permissions` endpoint.
 *
 * @since 4.7.0
 * @property \BEdita\Core\Model\Table\EndpointPermissionsTable $EndpointPermissions
 */
class EndpointPermissionsController extends AdminController
{
    /**
     * @inheritDoc
     */
    public $defaultTable = 'EndpointPermissions';
}
