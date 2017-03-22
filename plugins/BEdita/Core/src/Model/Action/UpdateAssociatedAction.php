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
use Cake\ORM\Query;

/**
 * Abstract class for updating associations between entities.
 *
 * @since 4.0.0
 */
abstract class UpdateAssociatedAction extends BaseAction
{

    /**
     * Association.
     *
     * @var \Cake\ORM\Association
     */
    protected $Association;

    /**
     * {@inheritDoc}
     */
    protected function initialize(array $config)
    {
        $this->Association = $this->getConfig('association');
    }

    /**
     * Find existing associations.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @return array|null
     */
    protected function existing(EntityInterface $entity)
    {
        $list = new ListAssociatedAction(['association' => $this->Association]);
        $sourcePrimaryKey = (array)$this->Association->getSource()->getPrimaryKey();
        $bindingKey = (array)$this->Association->getBindingKey();

        $existing = $list(['primaryKey' => $entity->extract($sourcePrimaryKey), 'list' => true]);

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
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        return $this->update($data['entity'], $data['relatedEntities']);
    }

    /**
     * Perform update.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entity(-ies).
     * @return int|false
     */
    abstract protected function update(EntityInterface $entity, $relatedEntities);
}
