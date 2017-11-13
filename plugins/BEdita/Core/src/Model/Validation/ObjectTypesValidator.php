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

namespace BEdita\Core\Model\Validation;

use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Validator for object types.
 *
 * @since 4.0.0
 */
class ObjectTypesValidator extends Validator
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct();

        $this->setProvider('table', TableRegistry::get('ObjectTypes'));
        $this->setProvider('reserved', new NotReserved());

        $this
            ->integer('id')
            ->allowEmpty('id', 'create');

        $this
            ->requirePresence('name', 'create')
            ->notEmpty('name')
            // `name` must contain at least a letter (avoid conflicts with ids)
            ->regex('name', '/^([a-zA-Z]+)/')
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->add('name', 'notReserved', ['rule' => 'allowed', 'provider' => 'reserved']);

        $this
            ->requirePresence('singular', 'create')
            ->notEmpty('singular')
            // `singular` must contain at least a letter (avoid conflicts with ids)
            ->regex('singular', '/^([a-zA-Z]+)/')
            ->add('singular', 'notSameAs', [
                'rule' => function ($value, $context) {
                    if (empty($context['data']['name'])) {
                        return true;
                    }

                    return ($context['data']['name'] !== $value);
                },
                'message' => __d('bedita', 'Name and singular fields must be different')
            ])
            ->add('singular', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->add('singular', 'notReserved', ['rule' => 'allowed', 'provider' => 'reserved']);

        $this
            ->allowEmpty('description');

        $this
            ->allowEmpty('associations');

        $this
            ->allowEmpty('hidden');

        return $this;
    }
}
