<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
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

App::import('Component', 'Transaction');

/**
 * Class that provides relation repair utility methods
 */
class RelationRepair extends BEAppModel
{
    public $useTable = false;

    /**
     * Do repair relation $relationName
     *
     * @param string $relationName relation name
     * @return int number of ObjectRelation records repaired (-1 on error)
     */
    public function repair($relationName) {
        return $this->repairRelation($relationName);
    }

    /**
     * Repair relation $relationName and return number of ObjectRelation records fixed
     *
     * @param string $relationName relation name
     * @return int number of ObjectRelation records repaired (-1 on error)
     * @throw BEditaException exception
     */
    private function repairRelation($relationName) {
        $relationStats = ClassRegistry::init('RelationStats');
        $relationData = $relationStats->getRelation($relationName);
        if (empty($relationData)) {
            throw new BEditaException('Relation data not found for relation ' . $relationName);
        }
        $relationInverse = null;
        if (!empty($relationData['inverse']) && ($relationData['inverse'] != $relationName) ) {
            $relationInverse = $relationData['inverse'];
        }
        if (empty($relationData) || empty($relationInverse)) {
            return 0;
        }
        $countAllTypesL = $relationStats->getObjectRelationsCount($relationName);
        $countAllTypesR = $relationStats->getObjectRelationsCount($relationInverse);
        if ($countAllTypesL === $countAllTypesR) {
            return 0;
        }
        if ($countAllTypesL > $countAllTypesR) { // => fix right relations
            $objRels = $relationStats->getObjectRelationsByName($relationName);
            return $this->fixObjectRelations($relationName, $relationInverse, $objRels);
        }
        // => fix left relations
        $objRels = $relationStats->getObjectRelationsByName($relationInverse);
        return $this->fixObjectRelations($relationInverse, $relationName, $objRels);
    }

    private function fixObjectRelations($relationName, $relationInverse, $objRels) {
        $fixed = 0;
        $objRel = ClassRegistry::init('ObjectRelation');
        foreach ($objRels as $relRecord) {
            $rc = $relRecord['ObjectRelation'];
            if ($relationInverse == null || $objRel->relationExists($rc['object_id'], $rc['id'], $relationInverse)) {
                continue; // relation seems ok
            } else {
                if ($rc['object_id'] != $rc['id']) {
                    $priority = (!empty($rc['priority'])) ? $rc['priority'] : 1;
                    $params = (!empty($rc['params'])) ? $rc['params'] : array();
                    $relationRightWrong = $objRel->find('first', array(
                        'conditions' => array(
                            'id' => $rc['object_id'],
                            'object_id' => $rc['id'],
                            'switch' => $relationName
                        )
                    ));
                    if (!empty($relationRightWrong)) { // wrong inverse? then delete before insert...
                        $this->log('Deleting wrong inverse for ' . $relationName . ' object_id: ' . $rc['object_id'] . ' id: ' . $rc['id'] , 'debug');
                        if (!$objRel->deleteRelation($rc['object_id'], $rc['id'], $relationName, false)) {
                            throw new BEditaException('error deleting relation for id ' . $rc['id']);
                        }
                        if (!empty($relationRightWrong['ObjectRelation']['params'])) {
                            $params = $relationRightWrong['ObjectRelation']['params'];
                        }
                        if (!empty($relationRightWrong['ObjectRelation']['priority'])) {
                            $priority = $relationRightWrong['ObjectRelation']['priority'];
                        }
                    }
                    $this->log('Creating relation record for ' . $relationInverse . ' object_id: ' . $rc['object_id'] . ' id: ' . $rc['id'] , 'debug');
                    if (!$objRel->createRelation($rc['object_id'], $rc['id'], $relationInverse, $priority, false, $params)) {
                        throw new BEditaException('error creating relation for id ' . $rc['id']);
                    }
                    $fixed++;
                }
            }
        }
        return $fixed;
    }
}
