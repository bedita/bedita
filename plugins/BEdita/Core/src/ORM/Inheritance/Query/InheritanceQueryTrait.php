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

use BEdita\Core\ORM\Inheritance\Table;
use Cake\ORM\Table as CakeTable;

/**
 * Contains methods common logic for queries on tables that use class table inheritance (CTI).
 *
 * @since 5.24.0
 */
trait InheritanceQueryTrait
{
    /**
     * @inheritDoc
     */
    public function addDefaultTypes(CakeTable $table)
    {
        parent::addDefaultTypes($table);

        if ($table instanceof Table) {
            // Add types of fields from inherited tables, so that they are cast to the correct type.
            $alias = $table->getAlias();
            foreach ($table->inheritedTables() as $table) {
                $map = $table->getSchema()->typeMap();
                $fields = [];
                foreach ($map as $f => $type) {
                    $fields[$f] = $fields[$alias . '.' . $f] = $fields[$alias . '__' . $f] = $type;
                }
                $this->getTypeMap()->addDefaults($fields);
            }
        }

        return $this;
    }
}
