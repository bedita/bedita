<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2016 Chialab Srl, ChannelWeb Srl
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

require_once 'bedita_base.php';

/**
 * Update `publisher` field for contents created by users to NULL.
 */
class PublisherUserContentsShell extends BeditaBaseShell
{
    /**
     * Models used by this shell.
     *
     * @var array
     */
    public $uses = array('BEObject');

    /**
     * Display help.
     */
    public function help() {
        $this->out('Update `publisher` field for all user-contents');
        $this->out();

        $this->out('  Usage: publisher_user_contents');
    }

    /**
     * Convert all date items in the database.
     */
    public function main() {
        // Count objects affected by this operation.
        $count = $this->BEObject->find('first', array(
            'fields' => array('COUNT(DISTINCT BEObject.id) AS count'),
            'contain' => array(),
            'joins' => array(
                array(
                    'table' => 'groups_users',
                    'alias' => 'GroupsUser',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GroupsUser.user_id = BEObject.user_created',
                    ),
                ),
                array(
                    'table' => 'groups',
                    'alias' => 'Group',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Group.id = GroupsUser.group_id',
                        'Group.backend_auth' => 0,
                    ),
                ),
            ),
            'conditions' => array(
                'BEObject.publisher' => '',
            ),
        ));
        $count = $count[0]['count'];

        $question = sprintf('=====> %d objects will be updated. Do you wish to continue?', $count);
        if ($this->in($question, array('y', 'n'), 'n') !== 'y') {
            $this->err('Aborting');

            exit(1);
        }

        // Perform update.
        App::import('Sanitize');
        $this->out('=====> Updating rows in database...');
        $this->BEObject->query(
            'UPDATE `objects`' . PHP_EOL .
            '    INNER JOIN `groups_users` ON (`groups_users`.`user_id` = `objects`.`user_created`)' . PHP_EOL .
            '    INNER JOIN `groups` ON (`groups`.`id` = `groups_users`.`group_id`)' . PHP_EOL .
            '    SET `objects`.`publisher` = NULL' . PHP_EOL .
            '    WHERE (`objects`.`publisher` = \'\') AND `groups`.`backend_auth` = 0'
        );

        // Clean up cache.
        $question = '=====> It is STRONGLY suggested that you empty your cache, in order to avoid having outdated publisher fields.' . PHP_EOL;
        $question .= '=====> Do you want to clear your cache now?';
        if ($this->in($question, array('y', 'n'), 'y') !== 'n') {
            $this->out('=====> Cleaning up cache...');
            ClassRegistry::init('Utility')
                ->call('cleanupCache', array(
                    'basePath' => TMP,
                    'frontendsToo' => false,
                    'cleanAll' => true,
                ));
        }

        $this->out('=====> DONE');
    }
}
