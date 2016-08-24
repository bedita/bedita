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

namespace BEdita\Core\Model\Action;

use Cake\ORM\Association;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;
use Cake\ORM\Query;

/**
 * Command to list entities associated to another entity.
 *
 * @since 4.0.0
 */
class ListAssociated
{

    /**
     * Association.
     *
     * @var \Cake\ORM\Association
     */
    protected $Association;

    /**
     * Command constructor.
     *
     * @param \Cake\ORM\Association $Association Association.
     */
    public function __construct(Association $Association)
    {
        $this->Association = $Association;
    }

    /**
     * Find existing relations.
     *
     * @param mixed $primaryKey Primary key of entity to find associations for.
     * @return \Cake\Datasource\EntityInterface[]|\Cake\Datasource\EntityInterface|null
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException Throws an exception if an invalid
     *      primary key is passed.
     */
    public function __invoke($primaryKey)
    {
        $sourcePrimaryKey = array_map(
            [$this->Association->source(), 'aliasField'],
            (array)$this->Association->source()->primaryKey()
        );

        $joinFields = [];
        if ($this->Association instanceof HasMany || $this->Association instanceof HasOne) {
            $joinFields = (array)$this->Association->foreignKey();
        }

        $associated = $this->Association->source()
            ->get($primaryKey, [
                'fields' => $sourcePrimaryKey,
                'contain' => [
                    $this->Association->name() => function (Query $q) use ($joinFields) {
                        $fields = array_merge((array)$this->Association->primaryKey(), $joinFields);

                        return $q
                            ->select($fields);
                    },
                ],
            ])
            ->get($this->Association->property());

        if (is_array($associated)) {
            $associated = array_map(
                function ($entity) use ($joinFields) {
                    foreach ($joinFields as $joinField) {
                        unset($entity[$joinField]);
                    }
                    unset($entity['_joinData']);

                    return $entity;
                },
                $associated
            );
        }

        return $associated;
    }
}
