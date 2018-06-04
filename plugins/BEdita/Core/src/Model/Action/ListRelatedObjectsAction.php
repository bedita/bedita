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

use BEdita\Core\Model\Table\ObjectsTable;
use BEdita\Core\ORM\Association\RelatedTo;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\Core\Configure;
use Cake\ORM\Association;
use Cake\ORM\TableRegistry;

/**
 * Command to list associated objects.
 *
 * @since 4.0.0
 */
class ListRelatedObjectsAction extends ListAssociatedAction
{

    /**
     * {@inheritDoc}
     */
    protected function initialize(array $config)
    {
        parent::initialize($config);

        if ($this->Association instanceof RelatedTo) {
            $objectTypes = TableRegistry::get('ObjectTypes')
                ->find('byRelation', [
                    'name' => $this->Association->getName(),
                    'side' => 'right',
                ])
                ->toArray();
            $table = $this->Association->getTarget();
            if (count($objectTypes) === 1) {
                $objectType = current($objectTypes);
            }
            $this->ListAction = new ListObjectsAction(compact('table', 'objectType'));
        } elseif ($this->Association->getTarget() instanceof ObjectsTable
                || $this->Association->getTarget() instanceof Table) {
            $table = $this->Association->getTarget();
            $this->ListAction = new ListObjectsAction(compact('table'));
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function buildQuery($primaryKey, array $data, Association $inverseAssociation)
    {
        $data += ['joinData' => true];

        $query = parent::buildQuery($primaryKey, $data, $inverseAssociation);

        if (!empty($data['list']) && $this->Association->getTarget()->hasField('object_type_id')) {
            $query = $query->select([
                $this->Association->getTarget()->aliasField('object_type_id'),
            ]);
        }
        if (Configure::check('Status.level')) {
            $query = $query->find('status', [Configure::read('Status.level')]);
        }

        return $query;
    }
}
