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

use BEdita\Core\Exception\InvalidDataException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Log\LogTrait;
use Cake\Utility\Hash;

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
     * @inheritDoc
     */
    protected function initialize(array $config)
    {
        $this->Table = $this->getConfig('table');
    }

    /**
     * @inheritDoc
     */
    public function execute(array $data = [])
    {
        $entityOptions = (array)Hash::get($data, 'entityOptions');
        $saveOptions = (array)Hash::get($data, 'saveOptions');
        $entity = $data['entity'];
        // object `id` added to data to avoid bad side effects on DataCleanup behavior
        if (!$entity->isNew()) {
            $data['data']['id'] = $entity->get('id');
        }
        $entity = $this->Table->patchEntity($entity, $data['data'], $entityOptions);
        $success = $this->Table->save($entity, $saveOptions);

        if ($success === false) {
            $errors = $entity->getErrors();
            if (!empty($errors)) {
                $this->log(sprintf('Entity save errors: %s', json_encode($errors)), 'warning');

                throw new InvalidDataException(__d('bedita', 'Invalid data'), $errors);
            }

            $this->log(sprintf('Save failed - data: %s', json_encode($data['data'])), 'error');

            throw new InternalErrorException(__d('bedita', 'Save failed'));
        }

        return $success;
    }
}
