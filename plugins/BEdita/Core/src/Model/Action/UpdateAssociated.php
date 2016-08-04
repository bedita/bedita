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
     * Perform update.
     *
     * @param \Cake\Datasource\EntityInterface $entity
     * @param $relatedEntities
     * @return bool
     */
    abstract public function __invoke(EntityInterface $entity, $relatedEntities);
}
