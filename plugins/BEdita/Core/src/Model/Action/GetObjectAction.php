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

use Cake\Core\Configure;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Utility\Hash;

/**
 * Command to get an object.
 *
 * @since 4.0.0
 */
class GetObjectAction extends BaseAction
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
    protected function initialize(array $data)
    {
        $this->Table = $this->getConfig('table');
        $this->objectType = $this->getConfig('objectType');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        // Prepare conditions and contained associations.
        $conditions = $this->getPrimaryKeyConditions($data);
        $conditions += [
            'deleted' => (int)!empty($data['deleted']),
        ];
        if (isset($data['locked'])) {
            $conditions += [
                'locked' => (int)!empty($data['locked']),
            ];
        }
        $contain = array_merge(['ObjectTypes'], (array)Hash::get($data, 'contain'));

        // Build query and add finders.
        $query = $this->Table->find('publishable')
            ->contain($contain)
            ->where($conditions);
        if (isset($this->objectType)) {
            $assoc = $this->objectType->associations;
            if (!empty($assoc)) {
                $query = $query->contain($assoc);
            }
            $query = $query->find('type', (array)$this->objectType->id);
        }
        if (!empty($data['lang'])) {
            $query = $query->find('translations', ['lang' => $data['lang']]);
        }

        return $query->firstOrFail();
    }

    /**
     * Build conditions for primary key.
     *
     * This method performs a basic check on primary key structure, so that the number of values in primary key
     * matches the number of columns the table's primary key consists of. For objects, this always means one column.
     *
     * @param array $data Action data.
     * @return array
     */
    protected function getPrimaryKeyConditions(array $data)
    {
        $key = array_map([$this->Table, 'aliasField'], (array)$this->Table->getPrimaryKey());
        $primaryKey = (array)$data['primaryKey'];
        if (count($key) !== count($primaryKey)) {
            $primaryKey = $primaryKey ?: [null];
            $primaryKey = array_map(function ($key) {
                return var_export($key, true);
            }, $primaryKey);

            throw new InvalidPrimaryKeyException(sprintf(
                'Record not found in table "%s" with primary key [%s]',
                $this->Table->getTable(),
                implode(', ', $primaryKey)
            ));
        }
        $conditions = array_combine($key, $primaryKey);

        return $conditions;
    }
}
