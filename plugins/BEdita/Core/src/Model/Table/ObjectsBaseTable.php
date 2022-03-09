<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Model\Table;

use BEdita\Core\ORM\Inheritance\Table;

/**
 * Base Table class for every Table implementing a BEdita Object
 * on a table other than `objects`.
 *
 * @since 4.1.0
 */
abstract class ObjectsBaseTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BEdita/Core.ObjectModel');
    }
}
