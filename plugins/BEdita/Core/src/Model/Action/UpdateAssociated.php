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

use Cake\Datasource\EntityInterface;
use Cake\ORM\Association;
use Cake\ORM\Query;

/**
 * Abstract class for updating associations between entities.
 *
 * @since 4.0.0
 */
abstract class UpdateAssociated
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
     * Getter/setter for association.
     *
     * @param \Cake\ORM\Association|null $Association New association to be set.
     * @return \Cake\ORM\Association
     */
    public function association(Association $Association = null)
    {
        if ($Association !== null) {
            $this->Association = $Association;
        }

        return $this->Association;
    }

    /**
     * Find existing associations.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @return array|null
     */
    protected function existing(EntityInterface $entity)
    {
        $list = new ListAssociated($this->Association);
        $sourcePrimaryKey = (array)$this->Association->source()->primaryKey();
        $bindingKey = (array)$this->Association->bindingKey();

        $existing = $list($entity->extract($sourcePrimaryKey));

        if ($existing instanceof EntityInterface) {
            return $existing
                ->extract($bindingKey);
        }

        if (!($existing instanceof Query)) {
            return null;
        }

        return $existing
            ->map(function (EntityInterface $relatedEntity) use ($bindingKey) {
                return $relatedEntity->extract($bindingKey);
            })
            ->toArray();
    }

    /**
     * Perform update.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entity(-ies).
     * @return int|false
     */
    abstract public function __invoke(EntityInterface $entity, $relatedEntities);
}
