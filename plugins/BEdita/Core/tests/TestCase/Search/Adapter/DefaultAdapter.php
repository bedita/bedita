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

namespace BEdita\Core\Test\TestCase\Search\Adapter;

use BEdita\Core\Search\BaseAdapter;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Default adapter for testing purposes.
 */
class DefaultAdapter extends BaseAdapter
{
    public function search(Query $query, string $text, array $options = []): Query
    {
        return $query;
    }

    public function indexResource(EntityInterface $entity, string $operation): void
    {
    }
}
