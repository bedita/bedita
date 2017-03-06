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

use Cake\Log\LogTrait;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;

/**
 * Command to save an entity.
 *
 * @since 4.0.0
 */
class SaveEntityAction extends BaseAction
{

    use LogTrait;

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * {@inheritDoc}
     */
    protected function initialize(array $config)
    {
        $this->Table = $this->getConfig('table');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $entity = $this->Table->patchEntity($data['entity'], $data['data']);
        $success = $this->Table->save($entity);

        if ($success === false) {
            $errors = $entity->getErrors();
            if (!empty($errors)) {
                $this->log('Entity save failed', 'debug', compact('entity'));

                throw new BadRequestException([
                    'title' => __d('bedita', 'Invalid data'),
                    'detail' => [$errors],
                ]);
            }

            $this->log('Save failed');

            throw new InternalErrorException(__d('bedita', 'Save failed'));
        }
        return $success;
    }
}
