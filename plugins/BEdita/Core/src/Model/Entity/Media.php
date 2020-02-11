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

use Cake\Utility\Hash;

/**
 * Media Entity
 *
 * @property int $id
 * @property string $name
 * @property string $provider
 * @property string $provider_uid
 * @property string $provider_url
 * @property string $provider_thumbnail
 * @property array $provider_extra
 * @property \BEdita\Core\Model\Entity\Stream[] $streams
 */
class Media extends ObjectEntity
{
    /**
     * {@inheritDoc}
     */
    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);

        // Virtual properties.
        $this->setVirtual(['media_url'], true);
        $this->setAccess('media_url', false);
    }

    /**
     * Getter for media url.
     *
     * @return string|null
     */
    protected function _getMediaUrl(): ?string
    {
        if ($this->streams === null) {
            $this->getTable()->loadInto($this, ['Streams']);
        }

        return Hash::get((array)$this->streams, '0.url', $this->provider_url);
    }
}
