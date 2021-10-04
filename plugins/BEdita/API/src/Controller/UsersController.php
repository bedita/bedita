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

/**
 * Controller for `/users` endpoint.
 *
 * @since 4.0.0
 */
class UsersController extends ObjectsController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Users';

    /**
     * Meta properties accessible for admins
     *
     * @var array
     */
    protected const ADMIN_META_ACCESSIBLE = ['blocked', 'locked'];

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'roles' => ['roles'],
            'parents' => ['folders'],
        ],
    ];
}
