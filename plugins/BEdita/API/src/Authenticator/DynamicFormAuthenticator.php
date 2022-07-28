<?php
declare(strict_types=1);

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
namespace BEdita\API\Authenticator;

use Authentication\Authenticator\FormAuthenticator;
use Cake\Utility\Hash;
use Psr\Http\Message\ServerRequestInterface;

class DynamicFormAuthenticator extends FormAuthenticator
{
    /**
     * @inheritDoc
     */
    protected function _getData(ServerRequestInterface $request): ?array
    {
        /** @var array $fields */
        $fields = $this->_config['fields'];
        /** @var array $body */
        $body = $request->getParsedBody();

        $data = [];
        foreach ($fields as $key => $field) {
            $data[$key] = Hash::get($body, $field);
        }

        return $data;
    }
}
