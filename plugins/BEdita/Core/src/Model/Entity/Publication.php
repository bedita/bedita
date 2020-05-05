<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
 * Publication Entity
 *
 * @property int $id
 * @property string|null $public_name
 * @property string|null $public_url
 * @property string|null $staging_url
 * @property string|null $stats_code
 */
class Publication extends ObjectEntity
{
}
