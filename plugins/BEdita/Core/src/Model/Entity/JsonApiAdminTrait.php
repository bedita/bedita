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

namespace BEdita\Core\Model\Entity;

use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Trait for exposing useful properties required for JSON API response on `/admin` resources.
 *
 * @since 4.0.0
 */
trait JsonApiAdminTrait
{
    use JsonApiTrait;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function routeNamePrefix()
    {
        return 'api:admin:resources';
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function getRelationships()
    {
         return [[], []];
    }
}
