<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\Model\Table\ObjectsBaseTable as Table;
use BEdita\Core\Model\Validation\Validation;
use Cake\Validation\Validator;

/**
 * Publications Model
 *
 * @method \BEdita\Core\Model\Entity\Publication get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Publication newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Publication saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Publication patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication findOrCreate($search, callable $callback = null, $options = [])
 */
class PublicationsTable extends Table
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('publications');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->extensionOf('Objects');
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->allowEmptyString('public_name');

        $validator
            ->allowEmptyString('public_url');

        $validator
            ->allowEmptyString('staging_url');

        $validator
            ->allowEmptyString('stats_code');

        return $validator;
    }
}
