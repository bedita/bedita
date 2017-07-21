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
}
