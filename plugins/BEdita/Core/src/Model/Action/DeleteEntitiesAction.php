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

use Cake\Log\LogTrait;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Command to delete entities.
 *
 * @since 5.28.0
 */
class DeleteEntitiesAction extends BaseAction
{
    use LocatorAwareTrait;
    use LogTrait;

    /**
     * @inheritDoc
     */
    public function execute(array $data = [])
    {
        $result = true;
        $payload = $data;
        unset($payload['entities']);
        foreach ($data['entities'] as $entity) {
            $payload['entity'] = $entity;
            $table = $this->fetchTable($entity->get('type') ?: $entity->getSource());
            $action = new DeleteEntityAction(compact('table'));
            $result = $result && $action($payload);
        }

        return $result;
    }
}
