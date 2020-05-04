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
 * Links Model
 *
 * @method \BEdita\Core\Model\Entity\Link get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Link newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Link[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Link|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Link saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Link patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Link[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Link findOrCreate($search, callable $callback = null, $options = [])
 */
class LinksTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('links');
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
            ->allowEmptyString('url');

        $validator
            ->allowEmptyString('http_status');

        $validator
            ->add('last_update', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmptyDate('last_update');

        return $validator;
    }
}
