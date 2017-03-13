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
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Association;
use Cake\ORM\Association\BelongsToMany;
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

        $entity = $association->getSource()->get($data['primaryKey']);

        $requestData = $this->request->getData();
        if (!Hash::numeric(array_keys($requestData))) {
            $requestData = [$requestData];
        }

        $relatedEntities = $this->getTargetEntities($requestData, $association);
        $count = count($relatedEntities);
        if ($count === 0) {
            $relatedEntities = [];
        } elseif ($count === 1) {
            $relatedEntities = reset($relatedEntities);
        }

        return $this->Action->execute(compact('entity', 'relatedEntities'));
    }

    /**
     * Get target entities.
     *
     * @param array $data Request data.
     * @param \Cake\ORM\Association $association Association.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function getTargetEntities(array $data, Association $association)
    {
        $target = $association->getTarget();
        $primaryKeyField = $target->getPrimaryKey();
        $targetPKField = $target->aliasField($primaryKeyField);

        $targetPrimaryKeys = array_unique(Hash::extract($data, '{*}.id'));
        if (empty($targetPrimaryKeys)) {
            return [];
        }

        $targetEntities = $target->find()
            ->where(function (QueryExpression $exp) use ($targetPKField, $targetPrimaryKeys) {
                return $exp->in($targetPKField, $targetPrimaryKeys);
            });

        $targetEntities = $targetEntities->indexBy($primaryKeyField)->toArray();

        foreach ($data as $datum) {
            $id = Hash::get($datum, 'id');
            $type = Hash::get($datum, 'type');
            if (!isset($targetEntities[$id]) || ($targetEntities[$id]->has('type') && $targetEntities[$id]->get('type') !== $type)) {
                throw new RecordNotFoundException(
                    __('Record not found in table "{0}"', $type ?: $target->getTable()),
                    400
                );
            }

            $meta = Hash::get($datum, '_meta');
            if (!$this->request->is('delete') && $association instanceof BelongsToMany && $meta !== null) {
                $targetEntities[$id]->_joinData = $association->junction()->newEntity($meta);
            }
        }

        return $targetEntities;
    }
}
