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

namespace BEdita\Core\Model\Validation;

use Cake\Validation\Validator;

/**
 * Validate custom URLs using custom protocols.
 *
 * @since 4.0.0
 */
class CustomUrlValidationProvider
{

    /**
     * Checks that a value is a valid URL or custom url as myapp://
     *
     * @param string $value The url to check
     * @param array $context The validation context
     * @return bool
     */
    public function isValidUrl($value, array $context = [])
    {
        // check for a valid scheme (https://, myapp://,...)
        $regex = '/(?<scheme>^[a-z][a-z0-9+\-.]*:\/\/).*/';
        if (!preg_match($regex, $value, $matches)) {
            return false;
        }

        // if scheme is not an URL protocol then it's a custom url (myapp://) => ok
        if (!preg_match('/^(https?|ftps?|sftp|file|news|gopher:\/\/)/', $matches['scheme'])) {
            return true;
        }

        if (!empty($context['providers']['default'])) {
            $provider = $context['providers']['default'];
        } else {
            $provider = (new Validator())->getProvider('default');
        }

        return $provider->url($value, true, $context);
    }
}
