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

namespace BEdita\Core\Filesystem;

use Cake\Core\InstanceConfigTrait;

/**
 * Thumbnail generator.
 *
 * This is a base class that might be extended by concrete Thumbnail generators that
 */
abstract class ThumbnailGenerator implements GeneratorInterface
{

    use InstanceConfigTrait;

    /**
     * Default configuration for adapter.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Initialize filesystem adapter class.
     *
     * @param array $config Configuration.
     * @return bool
     */
    public function initialize(array $config)
    {
        $this->setConfig($config);

        return true;
    }
}
