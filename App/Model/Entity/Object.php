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
namespace BEdita\Model\Entity;

use Cake\ORM\Entity;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use BEdita\Lib\Utility\String;

class Object extends Entity {

    /**
     * Build object unique name
     *
     * @param string $value
     * @return string
     */
    public function defaultNickname($value) {
        $nickname = $nickname_base = String::friendlyUrl($value);
        $nickOk = false;
        $countNick = 1;
        $reservedWords = array_merge(Configure::read('defaultReservedWords'), Configure::read('cfgReservedWords'));
        debug($this->object_type_id);

        if (empty($nickname)) {
            //$nickname_base = $conf->objectTypes[$objTypeId]["name"] . "-" . time(); // default name - model type name - timestamp
            $nickname_base = 'object_' . $this->object_type_id . '-' . time();
            $nickname = $nickname_base ;
        };

        $aliasTable = TableRegistry::get('Aliases');
        $objectsTable = TableRegistry::get('Objects');
        while (!$nickOk) {

            $query = $objectsTable->find()
                ->where(['nickname' => $nickname]);
            if ($this->id) {
                $query->andWhere(['id <>' => $this->id]);
            }
            $numNickDb = $query->count();

            // check nickname in db and in reservedWords
            if ($numNickDb == 0 && !in_array($nickname, $reservedWords)) {
                // check aliases
                $numAlias = $aliasTable->find()
                    ->where(['nickname_alias' => $nickname])
                    ->count();
                if ($numAlias == 0) {
                    $nickOk = true;
                }
            }
            if (!$nickOk) {
                $nickname = $nickname_base . '-' . $countNick++;
            }
        }

        return $nickname;
    }

}
