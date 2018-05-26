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
        $conditions = [
            'deleted' => (int)!empty($data['deleted']),
        ];
        $conditions += $this->statusCondition();
        $contain = array_merge(['ObjectTypes'], (array)Hash::get($data, 'contain'));
        $options = [];
        if (!empty($this->objectType)) {
            $finder = 'type';
            $options = (array)$this->objectType->id;

            $assoc = $this->objectType->associations;
            if (!empty($assoc)) {
                $contain = array_merge($contain, $assoc);
            }
        }

        return $this->Table->get($data['primaryKey'], $options + compact('conditions', 'contain', 'finder'));
    }
}
