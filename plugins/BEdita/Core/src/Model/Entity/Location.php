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

/**
 * Location Entity
 *
 * @property int $id
 * @property string $coords
 * @property string $address
 * @property string $locality
 * @property string $postal_code
 * @property string $country_name
 * @property string $region
 */
class Location extends ObjectEntity
{

    /**
     * {@inheritdoc}
     */
    protected function _getMeta()
    {
        $meta = parent::_getMeta();
        $meta[] = 'distance';

        return $meta;
    }
}
