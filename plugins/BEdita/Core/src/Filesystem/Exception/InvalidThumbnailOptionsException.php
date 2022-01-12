<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Filesystem\Exception;

use Cake\Core\Exception\Exception;

/**
 * Exception thrown when attempting to generate a thumbnail with invalid options.
 *
 * @since 4.0.0
 */
class InvalidThumbnailOptionsException extends \Cake\Core\Exception\CakeException
{
    /**
     * {@inheritDoc}
     */
    protected $_defaultCode = 400;
}
