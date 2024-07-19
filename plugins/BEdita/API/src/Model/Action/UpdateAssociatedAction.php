<?php
declare(strict_types=1);

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

use Authorization\Policy\Exception\MissingPolicyException;
use BEdita\Core\Model\Action\BaseAction;
use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Association;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasOne;
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
     * @inheritDoc
     */
    protected function initialize(array $data)
    {
        $this->Action = $this->getConfig('action');
        $this->request = $this->getConfig('request');
    }

    /**
     * @inheritDoc
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
        $this->authorizeAction($entity, $relatedEntities, $association);
        $count = count($relatedEntities);
        if ($count === 0) {
            $relatedEntities = [];
        } elseif ($count === 1 && ($association instanceof BelongsTo || $association instanceof HasOne)) {
            $relatedEntities = reset($relatedEntities);
        }

        return $this->Action->execute(compact('entity', 'relatedEntities'));
    }

    /**
     * Authorize action.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param array $targetEntities The target entities
     * @param \Cake\ORM\Association $association The association between $entity and $targetEntities
     * @return void
     */
    protected function authorizeAction(EntityInterface $entity, array $targetEntities, Association $association): void
    {
        /** @var \Authorization\Identity $identity */
        $identity = $this->request->getAttribute('identity');

        // For patch on Parents association ensures that parents of main entity aren't forbidden to user.
        // Patch will replace all current parents with target entities so if some parent of $entity is protected
        // the action can't be authorized.
        if ($this->request->is('patch') && $association->getName() === 'Parents' && !$identity->can('updateParents', $entity)) {
            throw new ForbiddenException(
                __d(
                    'bedita',
                    '{0} [id={1}] patching "Parents" is forbidden due to restricted permission on some parent',
                    [get_class($entity), $entity->id]
                )
            );
        }

        foreach ([$entity, ...$targetEntities] as $ent) {
            try {
                if ($identity->can('update', $ent) === false) {
                    throw new ForbiddenException(
                        __d('bedita', '{0} [id={1}] update is forbidden for user', [get_class($ent), $ent->id])
                    );
                }
            } catch (MissingPolicyException $e) {
                continue;
            }
        }
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
        $targetEntities = $targetEntities->all()->indexBy($primaryKeyField)->toArray();
        /** @var \Cake\Datasource\EntityInterface[] $targetEntities */

        // sort following the original order
        uksort(
            $targetEntities,
            function ($a, $b) use ($targetPrimaryKeys) {
                return array_search($a, $targetPrimaryKeys) - array_search($b, $targetPrimaryKeys);
            }
        );

        foreach ($data as $datum) {
            $id = Hash::get($datum, 'id');
            $type = Hash::get($datum, 'type');
            if (!isset($targetEntities[$id]) || ($targetEntities[$id]->has('type') && $targetEntities[$id]->get('type') !== $type)) {
                throw new RecordNotFoundException(__('Record not found in table "{0}"', $type ?: $target->getTable()));
            }

            if (!$this->request->is('delete') && $association instanceof BelongsToMany) {
                $meta = $this->prepareMeta($association, Hash::get($datum, '_meta.relation'));
                if ($meta !== null) {
                    $targetEntities[$id]->_joinData = $meta;
                }
            }
        }

        return $targetEntities;
    }

    /**
     * Prepare relation metadata.
     *
     * @param \Cake\ORM\Association&\Cake\ORM\Association\BelongsToMany $association The association.
     * @param array|null $meta Relation metadata.
     * @return array|null
     */
    protected function prepareMeta($association, $meta)
    {
        if (!$association instanceof RelatedTo) {
            return $meta;
        }

        $relation = $association->getRelation();
        if ($relation === null || empty($relation->params)) {
            return $meta;
        }

        // Cast stdClass to array recursively
        $params = json_decode(json_encode($relation->params), true);
        $defaultParams = array_filter(
            Hash::get($params, 'properties', []),
            function (array $prop): bool {
                return array_key_exists('default', $prop);
            },
        );
        if (count($defaultParams) === 0) {
            return $meta;
        }
        if ($meta === null) {
            $meta = ['params' => []];
        }

        foreach ($defaultParams as $name => $data) {
            if (empty($meta['params'][$name])) {
                $meta['params'][$name] = $data['default'];
            }
        }

        return $meta;
    }
}
