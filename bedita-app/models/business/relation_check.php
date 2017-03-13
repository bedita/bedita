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

/**
 * Class that provides relation check utility methods
 */
class RelationCheck extends BEAppModel
{
    public $useTable = false;

    /**
     * Check relation data consistency for relation $relationName, populating properly $messageBuffer with info about check
     *
     * @param string $relationName relation name
     * @param array $messageBuffer messages about check
     * @return array info about check
     */
    public function checkRelation($relationName, &$messageBuffer) {
        $result = true;
        $relationStats = ClassRegistry::init('RelationStats');
        $relationData = $relationStats->getRelation($relationName);
        if (empty($relationData)) {
            throw new BEditaException('Relation data not found for relation ' . $relationName);
        }
        $leftObjectTypes = $relationStats->objectTypesIdsForObjectNames($relationData,'left');
        $rightObjectTypes = $relationStats->objectTypesIdsForObjectNames($relationData,'right');
        $countAllowedTypesL = $relationStats->getObjectRelationsCount($relationName, $leftObjectTypes);
        $countAllTypesL = $relationStats->getObjectRelationsCount($relationName);
        $results[] = $this->checkTypesForRelation($relationName, $countAllowedTypesL, $countAllTypesL, $leftObjectTypes, $messageBuffer);
        if (!empty($relationData['inverse']) && ($relationData['inverse'] != $relationName) ) {
            $relationInverse = $relationData['inverse'];
            $countAllowedTypesR = $relationStats->getObjectRelationsCount($relationInverse, $rightObjectTypes);
            $countAllTypesR = $relationStats->getObjectRelationsCount($relationInverse);
            $results[] = $this->checkTypesForRelation($relationInverse, $countAllowedTypesR, $countAllTypesR, $rightObjectTypes, $messageBuffer);
            $results[] = $this->checkCounts('allowed types', $relationName, $relationInverse, $countAllowedTypesL, $countAllowedTypesR, $messageBuffer);
            $results[] = $this->checkCounts('all types', $relationName, $relationInverse, $countAllTypesL, $countAllTypesR, $messageBuffer);
        } else {
            $messageBuffer[] = 'No inverse: no checks';
        }
        foreach ($results as $r) {
            if ($r === false) {
                $result = false;
            }
        }
        $messageBuffer[] = ($result) ? 'ok' : 'corrupted data';
        return $result;
    }

    /**
     * check counts consistency for relation data, by types (allowed types by model and all types), populating properly $messageBuffer with info about check
     *
     * @param string $relationName relation name
     * @param int $countRelAllowedTypes number of relation data found for allowed types
     * @param int $countRelAllTypes number of relation data found for all types
     * @param array $allowedTypes object types allowed per relation
     * @param array $messageBuffer messages about check
     * @return void
     */
    private function checkTypesForRelation($relationName, $countRelAllowedTypes, $countRelAllTypes, $allowedTypes, &$messageBuffer) {
        if ($countRelAllowedTypes != $countRelAllTypes) {
            $messageBuffer[] = abs($countRelAllTypes - $countRelAllowedTypes) . ' records refer to a type not related to relation "' . $relationName . '" (MODEL-RELATED-TYPES-COUNT: ' . $countRelAllowedTypes . ' / ALL-TYPES-COUNT: ' . $countRelAllTypes . ')';
            $relationStats = ClassRegistry::init('RelationStats');
            $countRelationsGroupByType = $relationStats->getObjectRelationsCountGroupByType($relationName);
            foreach ($countRelationsGroupByType as $objectTypeId => $data) {
                if (!in_array($objectTypeId, $allowedTypes)) {
                    $ot = Configure::read('objectTypes.'.$objectTypeId.'.name');
                    $messageBuffer[] = '|=> ' . $data . ' records refer to the object type ' . $ot . ' (the object type is not related to relation ' . $relationName . ')';
                }
            }
        }
    }

    /**
     * verify counts consistency for relation and inverse, populating properly $messageBuffer with info about check
     *
     * @param string $info prefix info for messages in buffer
     * @param string $relationName relation name
     * @param string $relationInverse relation inverse
     * @param int $countRelation number of elements by relation
     * @param int $countInverse number of elements by relation inverse
     * @param array $messageBuffer messages about check
     * @return bool result
     */
    private function checkCounts($info, $relationName, $relationInverse, $countRelation, $countInverse, &$messageBuffer) {
        if ($countRelation === $countInverse) {
            $messageBuffer[] = "$info [left]->'$relationName'->[right]: $countRelation";
            $messageBuffer[] = "$info [right]->'$relationInverse'->[left]: $countInverse";
            return true;
        } else if ($countRelation < $countInverse) {
            $messageBuffer[] = "$info [left]->'$relationName'->[right]: $countRelation => should be $countInverse";
            $messageBuffer[] = "$info [right]->'$relationInverse'->[left]: $countInverse";
            return false;
        } else {
            $messageBuffer[] = "$info [left]->'$relationName'->[right]: $countRelation";
            $messageBuffer[] = "$info [right]->'$relationInverse'->[left]: $countInverse => should be $countRelation";
            return false;
        }
        return true;
    }
}
