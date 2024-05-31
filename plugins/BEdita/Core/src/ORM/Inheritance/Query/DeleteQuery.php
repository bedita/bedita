<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\ORM\Inheritance\Query;

use Cake\ORM\Query\DeleteQuery as CakeDeleteQuery;

/**
 * Delete Query class for tables that use class table inheritance (CTI).
 *
 * @since 5.24.0
 */
class DeleteQuery extends CakeDeleteQuery
{
    use InheritanceQueryTrait;
}
