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

namespace BEdita\Core\Utility;

/**
 * Interface for serializing JSON API objects.
 *
 * @since 4.0.0
 */
interface JsonApiSerializable
{

    /**
     * Tell JSON API serializer to exclude `attributes` from resource.
     *
     * @var int
     */
    const JSONAPIOPT_EXCLUDE_ATTRIBUTES = 1;

    /**
     * Tell JSON API serializer to exclude `meta` from resource.
     *
     * @var int
     */
    const JSONAPIOPT_EXCLUDE_META = 2;

    /**
     * Tell JSON API serializer to exclude `links` from resource.
     *
     * @var int
     */
    const JSONAPIOPT_EXCLUDE_LINKS = 4;

    /**
     * Tell JSON API serializer to exclude `relationships` from resource.
     *
     * @var int
     */
    const JSONAPIOPT_EXCLUDE_RELATIONSHIPS = 8;

    /**
     * JSON API serializer.
     *
     * This method **MUST** return a resource object as per JSON API specifications.
     *
     * @param int $options Options for serializing. Can be any combination of `JSONAPIOPT_*` constants.
     * @return array
     */
    public function jsonApiSerialize($options = 0);
}
