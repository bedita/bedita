<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\ORM\Inheritance\Query;

use Cake\ORM\Query\UnhydratedSelectQuery as CakeUnhydratedSelectQuery;

/**
 * UnhydratedSelectQuery class for tables that use class table inheritance (CTI).
 * Replicates the behavior of CakePHP's UnhydratedSelectQuery for inheritance.
 *
 * @see {\Cake\ORM\Query\UnhydratedSelectQuery}
 * @since 5.49.0
 */
class UnhydratedSelectQuery extends CakeUnhydratedSelectQuery
{
    use InheritanceQueryTrait;
    use SelectQueryTrait;
}
