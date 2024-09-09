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

namespace BEdita\Core\Model\Action;

/**
 * Command to delete objects.
 *
 * @since 5.27.0
 */
class DeleteObjectsAction extends BaseAction
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
        $entities = $data['entities'];

        if (!empty($data['hard'])) {
            $action = new DeleteEntitiesAction(['table' => $this->Table]);

            return $action(compact('entities'));
        }
        $result = true;
        foreach ($entities as $entity) {
            $entity->set('deleted', true);
            $result = $result && (bool)$this->Table->save($entity);
        }

        return $result;
    }
}
