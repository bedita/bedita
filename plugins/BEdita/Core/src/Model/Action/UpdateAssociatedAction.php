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

/**
 * Abstract class for updating associations between entities.
 *
 * @since 4.0.0
 * @property \Cake\ORM\Association $Association
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
     * @inheritDoc
     */
    protected function initialize(array $config)
    {
        $this->Association = $this->getConfig('association');
    }

    /**
     * {@inheritDoc}
     *
     * @return array|int|false
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
     * @return array|int|false
     */
    abstract protected function update(EntityInterface $entity, $relatedEntities);
}
