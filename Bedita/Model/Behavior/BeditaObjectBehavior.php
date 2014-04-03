<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */
namespace Bedita\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

/**
 * BeditaObjectBehavior
 */
class BeditaObjectBehavior extends Behavior {

    protected $table;

    public function __construct(Table $table, array $config = []) {
        parent::__construct($table, $config);
        $this->table = $table;
    }

    /**
     * add condition on object_type_id
     *
     * @param  Cake\Event\Event $event]
     * @param  Cake\ORM\Query $query The query object
     * @param  array  $options
     * @param  boolean $primary Indicates whether or not this is the root query, or an associated query
     */
    public function beforeFind($event, $query, array $options, $primary) {
        $query->where(['object_type_id' => $this->table->objectTypeId()]);
    }

}
