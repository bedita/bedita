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

namespace BEdita\Core\Test\TestCase\Filesystem\Thumbnail;

use BEdita\Core\Filesystem\ThumbnailGenerator;
use BEdita\Core\Model\Entity\Stream;

/**
 * Test generator class.
 */
class TestGenerator extends ThumbnailGenerator
{

    /**
     * Fake thumbnail URL.
     *
     * @var string
     */
    const THUMBNAIL_URL = 'https://assets.example.org/thumbnail.jpg';

    /**
     * {@inheritDoc}
     */
    public function getUrl(Stream $stream, array $options = [])
    {
        return static::THUMBNAIL_URL;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(Stream $stream, array $options = [])
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Stream $stream, array $options = [])
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Stream $stream)
    {
        return;
    }
}
