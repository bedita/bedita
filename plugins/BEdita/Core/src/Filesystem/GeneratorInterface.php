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

use BEdita\Core\Model\Entity\Stream;

/**
 * Interface for thumbnails generators.
 *
 * This interface exposes methods to be implemented in concrete classes that will actually generate thumbnails.
 *
 * Implementing classes are responsible for:
 *  - generating a thumbnail for a Stream with given options
 *  - returning public URL of a thumbnail for a Stream with given options
 *  - check if a thumbnail for a Stream with given options exist
 *  - delete all thumbnails for a Stream
 *
 * @since 4.0.0
 */
interface GeneratorInterface
{

    /**
     * Get URL for a generated thumbnail.
     *
     * This method should return a URL even if the thumbnail doesn't already exist, if possible.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream entity instance.
     * @param array $options Thumbnail options.
     * @return string
     */
    public function getUrl(Stream $stream, array $options = []);

    /**
     * Generate a thumbnail for a Stream entity using the provided options, and return the URL.
     *
     * This method should always generate a thumbnail, even if it already exists, as it might be used for
     * hard re-generation.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream entity instance.
     * @param array $options Thumbnail options.
     * @return bool Is the thumbnail ready? Synchronous generators should return `true`, asynchronous generators should return `false`.
     */
    public function generate(Stream $stream, array $options = []);

    /**
     * Check if a thumbnail for a Stream entity using the provided options already exists.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream entity instance.
     * @param array $options Thumbnail options.
     * @return bool Is the thumbnail ready?
     */
    public function exists(Stream $stream, array $options = []);

    /**
     * Delete all created thumbnails for a stream.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream entity instance.
     * @return void
     */
    public function delete(Stream $stream);
}
