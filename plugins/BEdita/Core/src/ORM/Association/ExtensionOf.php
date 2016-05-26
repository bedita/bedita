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

namespace BEdita\Core\ORM\Association;

use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\DependentDeleteTrait;

/**
 * Represents an 1 - 1 relationship where the source side of the relation is
 * related to only one record in the target table and vice versa.
 *
 * The association extends BelongsTo for saving the target side before the source side
 * but it is defined as Association::ONE_TO_ONE instead of Association::MANY_TO_ONE.
 *
 * Unlike BelongsTo associations ExtensionOf are cleared in a cascading delete scenario.
 *
 * An example of a ExtensionOf association would be Mammals is an extension of Animals.
 * In this scenario:
 *
 * - saving Mammals will save first the associated Animals
 * - deleting Mammals will also delete the associated Animals
 *
 * @since 4.0.0
 */
class ExtensionOf extends BelongsTo
{
    use DependentDeleteTrait;

    /**
     * {@inheritDoc}
     */
    protected $_dependent = true;

    /**
     * {@inheritDoc}
     */
    protected $_cascadeCallbacks = true;

    /**
     * {@inheritDoc}
     */
    protected $_joinType = 'INNER';

    /**
     * {@inheritDoc}
     */
    public function type()
    {
        return self::ONE_TO_ONE;
    }
}
