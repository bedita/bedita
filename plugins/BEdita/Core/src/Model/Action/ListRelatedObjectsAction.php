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

use Cake\ORM\Query;

/**
 * Command to list associated objects.
 *
 * @since 4.0.0
 */
class ListRelatedObjectsAction extends ListAssociatedAction
{

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $result = parent::execute($data);
        if (!($result instanceof Query)) {
            return $result;
        }

        if ($this->Association->getSchema()->column('object_type_id') !== null) {
            $result = $result
                ->select([$this->Association->aliasField('object_type_id')]);
        }

        return $result;
    }
}
