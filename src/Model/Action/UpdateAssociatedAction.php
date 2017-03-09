<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Model\Action;

use BEdita\Core\Model\Action\BaseAction;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Association;
use Cake\Utility\Hash;

/**
 * Command to update links between entities.
 *
 * @since 4.0.0
 */
class UpdateAssociatedAction extends BaseAction
{

    /**
     * Add associated action.
     *
     * @var \BEdita\Core\Model\Action\UpdateAssociatedAction
     */
    protected $Action;

    /**
     * Request instance.
     *
     * @var \Cake\Http\ServerRequest
     */
    protected $request;

    /**
     * {@inheritDoc}
     */
    protected function initialize(array $data)
    {
        $this->Action = $this->getConfig('action');
        $this->request = $this->getConfig('request');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $association = $this->Action->getConfig('association');
        if (!($association instanceof Association)) {
            throw new \LogicException(__d('bedita', 'Unknown association type'));
        }

        $sourceEntity = $association->getSource()->get($data['primaryKey']);

        $targetPrimaryKeys = (array)$this->request->getData('id') ?: Hash::extract($this->request->getData(), '{*}.id');
        $targetPrimaryKeys = array_unique($targetPrimaryKeys);
        $primaryKeyField = $association->getPrimaryKey();
        $targetPKField = $association->aliasField($primaryKeyField);

        $targetEntities = null;
        if (!empty($targetPrimaryKeys)) {
            $targetEntities = $association->find()
                ->where([
                    $targetPKField . ' IN' => $targetPrimaryKeys,
                ]);

            if ($targetEntities->count() !== count($targetPrimaryKeys)) {
                throw new RecordNotFoundException(
                    __('Record not found in table "{0}"', $association->getTarget()->getTable()),
                    400
                );
            }

            $targetEntities = (count($targetPrimaryKeys) > 1) ? $targetEntities->toArray() : $targetEntities->firstOrFail();
        }

        return $this->Action->execute(['entity' => $sourceEntity, 'relatedEntities' => $targetEntities]);
    }
}
