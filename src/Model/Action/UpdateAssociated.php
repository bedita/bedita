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

use BEdita\Core\Model\Action\UpdateAssociated as ParentAction;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;

/**
 * Command to add links between entities.
 *
 * @since 4.0.0
 */
class UpdateAssociated
{

    /**
     * Add associated action.
     *
     * @var \BEdita\Core\Model\Action\UpdateAssociated
     */
    protected $Action;

    /**
     * Request instance.
     *
     * @var \Cake\Http\ServerRequest
     */
    protected $request;

    /**
     * Command constructor.
     *
     * @param \BEdita\Core\Model\Action\UpdateAssociated $Action Associations update action.
     * @param \Cake\Http\ServerRequest $request Request instance.
     */
    public function __construct(ParentAction $Action, ServerRequest $request)
    {
        $this->Action = $Action;
        $this->request = $request;
    }

    /**
     * Add new relations.
     *
     * @param mixed $primaryKey Source entity primary key.
     * @return int|false
     */
    public function __invoke($primaryKey)
    {
        $sourceEntity = $this->Action->association()->getSource()->get($primaryKey);

        $targetPrimaryKeys = (array)$this->request->getData('id') ?: Hash::extract($this->request->getData(), '{*}.id');
        $targetPrimaryKeys = array_unique($targetPrimaryKeys);
        $primaryKeyField = $this->Action->association()->getPrimaryKey();
        $targetPKField = $this->Action->association()->aliasField($primaryKeyField);

        $targetEntities = null;
        if (count($targetPrimaryKeys)) {
            $targetEntities = $this->Action->association()->find()
                ->where([
                    $targetPKField . ' IN' => $targetPrimaryKeys,
                ]);

            if ($targetEntities->count() !== count($targetPrimaryKeys)) {
                throw new RecordNotFoundException(
                    __('Record not found in table "{0}"', $this->Action->association()->getTarget()->getTable()),
                    400
                );
            }

            $targetEntities = (count($targetPrimaryKeys) > 1) ? $targetEntities->toArray() : $targetEntities->firstOrFail();
        }

        return call_user_func($this->Action, $sourceEntity, $targetEntities);
    }
}
