<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Action;

use Cake\Core\Configure;

/**
 * Trait to expose methods on object related query conditions.
 *
 * @since 4.0.0
 */
trait ObjectConditionsTrait
{
    /**
     * Allowed object `status` condition
     *
     * @return array Empty array if all `status` are allowed otherwise a list of allowed values
     */
    protected function statusCondition()
    {
        $filter = [
            'on' => ['status' => 'on'],
            'draft' => ['status IN' => ['on', 'draft']],
        ];
        $level = Configure::read('Status.level');
        if ($level && isset($filter[$level])) {
            return $filter[$level];
        }

        return [];
    }
}
