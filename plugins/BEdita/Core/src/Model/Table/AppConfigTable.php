<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\State\CurrentApplication;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\Validation\Validator;

/**
 * App Config Model - used to handle app configuration data in DB
 * Handle `config` resources that have `context` matching 'app'
 * and `application_id` matching current application id.
 *
 * {@inheritDoc}
 *
 * @since 5.0.0
 */
class AppConfigTable extends ConfigTable
{
    protected const DEFAULT_CONTEXT = 'app';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->alphaNumeric('name')

            ->requirePresence('content', 'create')
            ->notEmptyString('content');

        return $validator;
    }

    /**
     * @inheritDoc
     */
    public function findAll(Query $query, array $options): Query
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->and([
                $exp->eq($this->aliasField('application_id'), CurrentApplication::getApplicationId()),
                $exp->eq($this->aliasField('context'), static::DEFAULT_CONTEXT),
                $exp->isNotNull($this->aliasField('application_id')),
            ]);
        });
    }

    /**
     * @inheritDoc
     */
    public function newEmptyEntity(): EntityInterface
    {
        $entity = parent::newEmptyEntity();
        $entity->set('context', static::DEFAULT_CONTEXT, ['guard' => false]);
        $entity->set('application_id', CurrentApplication::getApplicationId(), ['guard' => false]);

        return $entity;
    }
}
