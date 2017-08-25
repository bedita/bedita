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

use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\InternalErrorException;

/**
 * Command to delete an entity.
 *
 * @since 4.0.0
 */
class DeleteEntityAction extends BaseAction
{

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * {@inheritDoc}
     */
    protected function initialize(array $data)
    {
        $this->Table = $this->getConfig('table');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        // If object is not deletable returns 403 FORBIDDEN
        if ((int)$data['entity']['id'] === 1 && ($data['entity'] instanceof \BEdita\Core\Model\Entity\Role || $data['entity'] instanceof \BEdita\Core\Model\Entity\User)) {
            throw new ForbiddenException(__d('bedita', 'Could not delete "{0}" 1', $data['entity']));
        }

        return $this->Table->deleteOrFail($data['entity']);
    }
}
