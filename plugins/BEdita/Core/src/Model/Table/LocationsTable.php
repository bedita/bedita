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

namespace BEdita\Core\Model\Table;

use BEdita\Core\ORM\Inheritance\Table;
use Cake\Validation\Validator;

/**
 * Locations Model
 *
 * @method \BEdita\Core\Model\Entity\Location get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Location newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location findOrCreate($search, callable $callback = null, $options = [])
 */
class LocationsTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('locations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->extensionOf('Objects', [
            'className' => 'BEdita/Core.Objects'
        ]);

        $this->addBehavior('BEdita/Core.Relations');

        $this->addBehavior('BEdita/Core.Geometry');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('coords');

        $validator
            ->allowEmpty('address');

        $validator
            ->allowEmpty('locality');

        $validator
            ->allowEmpty('postal_code');

        $validator
            ->allowEmpty('country_name');

        $validator
            ->allowEmpty('region');

        return $validator;
    }
}
