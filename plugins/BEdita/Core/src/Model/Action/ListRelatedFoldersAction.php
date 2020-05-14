<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Action;

use BEdita\Core\Model\Entity\Tree;
use Cake\Datasource\EntityInterface;

/**
 * Command to list associated objects for folders.
 *
 * It behaves exactly as `ListRelatedObjectsAction` except for `Parents` association.
 * In that case only the first result is returned.
 *
 * @since 4.0.0
 */
class ListRelatedFoldersAction extends ListRelatedObjectsAction
{
    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $result = parent::execute($data);

        if ($this->Association->getName() === 'Parents') {
            return $result->first();
        }

        return $result;
    }

    /**
     * Remove unnecessary fields from `Tree` entity.
     *
     * @param \Cake\Datasource\EntityInterface $joinData Join data entity.
     * @return void
     */
    protected function prepareJoinEntity(EntityInterface $joinData): void
    {
        if ($joinData instanceof Tree) {
            $joinData->unsetProperty([
                'id',
                'parent_id',
                'object_id',
                'root_id',
                'parent_node_id',
                'tree_left',
                'tree_right',
            ]);
        }
    }
}
