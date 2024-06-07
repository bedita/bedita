<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\ORM\Inheritance;

use BEdita\Core\ORM\Inheritance\Query\InheritanceQueryTrait;
use Cake\ORM\Query as CakeQuery;

/**
 * Extends `\Cake\ORM\Query` to set `FROM` clause and add default types and fields.
 *
 * It will be removed in 6.x.
 *
 * @since 4.0.0
 * @property \BEdita\Core\ORM\Inheritance\Table _repository
 * @deprecated 5.24.0 Will be removed in 6.x
 */
class Query extends CakeQuery
{
    use InheritanceQueryTrait;
}
