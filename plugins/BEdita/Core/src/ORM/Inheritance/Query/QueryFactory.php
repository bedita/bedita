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

use Cake\ORM\Query\QueryFactory as CakeQueryFactory;
use Cake\ORM\Table;

/**
 * Factory class for generating instances of Select, Insert, Update, Delete queries in inheritance context.
 *
 * @since 6.0.0
 */
class QueryFactory extends CakeQueryFactory
{
    /**
     * @inheritDoc
     */
    public function select(Table $table): SelectQuery
    {
        return new SelectQuery($table);
    }

    /**
     * @inheritDoc
     */
    public function insert(Table $table): InsertQuery
    {
        return new InsertQuery($table);
    }

    /**
     * @inheritDoc
     */
    public function update(Table $table): UpdateQuery
    {
        return new UpdateQuery($table);
    }

    /**
     * @inheritDoc
     */
    public function delete(Table $table): DeleteQuery
    {
        return new DeleteQuery($table);
    }
}
