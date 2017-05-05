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
 * @property string $uri
 * @property string $name
 * @property string $mime_type
 * @property int $file_size
 * @property string $hash_file
 * @property string $original_name
 * @property int $width
 * @property int $height
 * @property string $provider
 * @property string $media_uid
 * @property string $thumbnail
 */
class Media extends ObjectEntity
{
}
