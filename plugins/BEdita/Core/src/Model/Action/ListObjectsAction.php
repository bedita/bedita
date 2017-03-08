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

namespace BEdita\Core\Model\Action;

/**
 * Command to save an entity.
 *
 * @since 4.0.0
 */
class ListObjectsAction extends BaseAction
{

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * Object type.
     *
     * @var \BEdita\Core\Model\Entity\ObjectType|null
     */
    protected $objectType;

    /**
     * {@inheritDoc}
     */
    protected function initialize(array $config)
    {
        $this->Table = $this->getConfig('table');
        $this->objectType = $this->getConfig('objectType');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $filter = [
            'deleted' => (int)!empty($data['deleted']),
        ];
        if (!empty($this->objectType)) {
            $filter['object_type_id'] = $this->objectType->id;
        }

        if (!empty($data['filter'])) {
            $filter = array_merge(
                ListEntitiesAction::parseFilter($data['filter']),
                $filter // Later values overwrite previous ones.
            );
        }

        $action = new ListEntitiesAction(['table' => $this->Table]);

        $query = $action->execute(compact('filter'));

        return $query
            ->contain('ObjectTypes');
    }
}
