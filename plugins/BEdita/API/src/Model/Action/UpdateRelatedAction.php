<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Model\Action;

use BEdita\Core\Model\Table\ObjectsTable;
use BEdita\Core\ORM\Inheritance\Table as InheritanceTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Association;
use Cake\ORM\Table;
use Cake\Utility\Hash;

/**
 * Command to update relations between objects.
 *
 * @since 4.4.0
 */
class UpdateRelatedAction extends UpdateAssociatedAction
{
    /** @inheritDoc */
    protected function getTargetEntities(array $data, Association $association): array
    {
        $target = $association->getTarget();
        $isObjectsTable = $target instanceof ObjectsTable || ($target instanceof InheritanceTable && $target->isTableInherited('Objects', true));
        if ($isObjectsTable) {
            $data = $this->ensureIds($target, $data);
        }

        return parent::getTargetEntities($data, $association);
    }

    /**
     * Assuming any non-numeric ID is a uname, convert them to IDs.
     *
     * Any `uname` that is not present in the database will be left as-is.
     * An error would still be raised later on.
     *
     * @param \Cake\ORM\Table $table Target table.
     * @param array $data Request data.
     * @return array Remapped data.
     */
    protected function ensureIds(Table $table, array $data): array
    {
        $nonNumericIds = array_filter(
            array_unique(Hash::extract($data, '{*}.id')),
            function (string $id): bool {
                return !is_numeric($id);
            }
        );
        if (empty($nonNumericIds)) {
            // Nothing to do.
            return $data;
        }

        // Query database to map `uname`s to the corresponding `id`s.
        $map = $table->find()
            ->select(['id', 'uname'])
            ->where(function (QueryExpression $exp) use ($nonNumericIds): QueryExpression {
                return $exp->in('uname', $nonNumericIds);
            })
            ->distinct()
            ->disableHydration()
            ->combine('uname', 'id')
            ->toArray();

        // Update data in place to avoid duplicating possibly large array in-memory.
        array_walk(
            $data,
            function (array &$item) use ($table, $map): void {
                $id = $item['id'];
                if (is_numeric($id)) {
                    return;
                }
                if (!isset($map[$id])) {
                    throw new RecordNotFoundException(
                        sprintf(
                            'Record not found in table "%s"',
                            Hash::get($item, 'type', $table->getTable())
                        )
                    );
                }
                $item['id'] = $map[$id];
            }
        );

        return $data;
    }
}
