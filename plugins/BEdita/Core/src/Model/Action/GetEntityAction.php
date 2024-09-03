<?php
declare(strict_types=1);

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
 * Command to get an entity.
 *
 * @since 4.0.0
 */
class GetEntityAction extends BaseAction
{
    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * @inheritDoc
     */
    protected function initialize(array $data)
    {
        $this->Table = $this->getConfig('table');
    }

    /**
     * @inheritDoc
     */
    public function execute(array $data = [])
    {
        if (!empty($data['contain'])) {
            return $this->Table->get($data['primaryKey'], [ 'contain' => $data['contain'] ]);
        }

        return $this->Table->get($data['primaryKey']);
    }
}
