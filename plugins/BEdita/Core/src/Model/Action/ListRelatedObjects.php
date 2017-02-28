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

namespace BEdita\Core\Model\Action;

use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

/**
 * Command to list associated objects.
 *
 * @since 4.0.0
 */
class ListRelatedObjects
{

    /**
     * Inner action.
     *
     * @var \BEdita\Core\Model\Action\ListAssociated
     */
    protected $Action;

    /**
     * Association.
     *
     * @var \Cake\ORM\Association\BelongsToMany
     */
    protected $Association;

    /**
     * Command constructor.
     *
     * @param \Cake\ORM\Table $Table Table object instance.
     * @param string $relation Relation name.
     */
    public function __construct(Table $Table, $relation)
    {
        if (!$Table->hasBehavior('Relations')) {
            throw new \InvalidArgumentException(
                __d('bedita', 'Table "{0}" does not implement relations', $Table->getRegistryAlias())
            );
        }

        $associationName = Inflector::camelize($relation);
        if (!$Table->associations()->has($associationName)) {
            throw new NotFoundException(
                __d(
                    'bedita',
                    'Relation "{0}" does not exist for object type "{1}"',
                    Inflector::underscore($relation),
                    Inflector::underscore($Table->getAlias())
                )
            );
        }

        $this->Association = $Table->association($associationName);
        $this->Action = new ListAssociated($this->Association);
    }

    /**
     * Find existing relations.
     *
     * @param int $id Object ID.
     * @return \Cake\ORM\Query
     */
    public function __invoke($id)
    {
        return $this->Action->__invoke($id)
            ->select([$this->Association->aliasField('object_type_id')])
            ->order($this->Association->sort());
    }
}
