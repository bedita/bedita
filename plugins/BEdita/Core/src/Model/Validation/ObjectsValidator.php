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
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * Base validator for BEdita objects.
 *
 * @since 4.0.0
 */
class ObjectsValidator extends Validator
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct();

        $table = TableRegistry::getTableLocator()->get('Objects');
        $this->setProvider('objectsTable', $table);

        $this
            ->naturalNumber('id')
            ->allowEmptyString('id', null, 'create')
            ->requirePresence('id', 'update')

            ->inList('status', ['on', 'off', 'draft'])
            ->notEmptyString('status')

            ->ascii('uname')
            ->allowEmptyString('uname')

            ->boolean('locked')
            ->allowEmptyString('locked')

            ->boolean('deleted')
            ->allowEmptyString('deleted')

            ->dateTime('published')
            ->allowEmptyDateTime('published')

            ->allowEmptyString('title')

            ->allowEmptyString('description')
            ->maxLengthBytes('description', MysqlAdapter::TEXT_MEDIUM)

            ->allowEmptyString('body')
            ->maxLengthBytes('body', MysqlAdapter::TEXT_MEDIUM)

            ->allowEmptyArray('extra')

            ->scalar('lang')
            ->add('lang', 'languageTag', ['rule' => [Validation::class, 'languageTag']])
            ->allowEmptyString('lang')

            ->add('publish_start', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmptyDateTime('publish_start')

            ->add('publish_end', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmptyDateTime('publish_end');
    }
}
