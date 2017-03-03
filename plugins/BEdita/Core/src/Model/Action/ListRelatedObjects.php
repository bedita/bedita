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

use Cake\ORM\Association;

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
     * @param \Cake\ORM\Association $Association Association.
     */
    public function __construct(Association $Association)
    {
        if (!$Association->getSource()->hasBehavior('Relations')) {
            throw new \InvalidArgumentException(
                __d('bedita', 'Table "{0}" does not implement relations', $Association->getSource()->getRegistryAlias())
            );
        }

        $this->Association = $Association;
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
