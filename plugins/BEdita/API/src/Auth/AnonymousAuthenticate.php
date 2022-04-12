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

namespace BEdita\API\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

/**
 * Pseudo-authentication for anonymous users.
 *
 * @since 4.0.0
 */
class AnonymousAuthenticate extends BaseAuthenticate
{
    /**
     * @inheritDoc
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        return $this->getUser($request);
    }

    /**
     * @inheritDoc
     */
    public function getUser(ServerRequest $request)
    {
        return [
            '_anonymous' => true,
        ];
    }
}
