<?php
/**-----8<--------------------------------------------------------------------
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
namespace BEdita\Model\Table;

use Cake\ORM\Table;
use Cake\Model\Behavior\TimestampBehavior;
use Cake\Event\Event;
use Cake\ORM\Query;

class UsersTable extends Table {

    /**
     * Initialize the table instance
     *
     * @param  array  $config Configuration options
     * @return void
     */
    public function initialize(array $config) {
        $this->belongsToMany('Groups');
        $this->addBehavior('Timestamp');
    }

    /**
     * Add formatResults to create a list of group names
     *
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Query $query The query object
     * @param array  $options
     * @param boolean $primary Indicates whether or not this is the root query, or an associated query
     */
    public function beforeFind(Event $event, Query $query, array $options, $primary) {
        $query->formatResults(function($results) {
            return $results->map(function($row) {
                $groupsList = [];
                if (is_object($row)) {
                    if ($row->groups) {
                        foreach ($row->groups as $g) {
                            $groupsList[] = $g->name;
                        }
                        $row->set('groupsList', $groupsList);
                    }
                // else it's an array (Entity was not hydrated)
                } else {
                    if (!empty($row['groups'])) {
                        foreach ($row['groups'] as $g) {
                            $groupsList[] = $g['name'];
                        }
                        $row['groupsList'] = $groupsList;
                    }
                }
                return $row;
            });
        });
    }

}
