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

/**
 * Command to delete entities.
 *
 * @since 5.27.0
 */
class DeleteEntitiesAction extends BaseAction
{
    use LogTrait;

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
        try {
            $this->Table->deleteManyOrFail($data['entities']);
        } catch (\Throwable $e) {
            $this->log(sprintf('Delete many failed - data: %s', json_encode($data['data'])), 'error');

            return false;
        }
        return true;
    }
}
