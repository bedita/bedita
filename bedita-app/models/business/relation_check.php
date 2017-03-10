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
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class RelationCheck extends BEAppModel
{
    public $useTable = false;

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

    private function checkCounts($info, $relationName, $relationInverse, $count1, $count2, &$messageBuffer) {
        if ($count1 === $count2) {
            $messageBuffer[] = "$info [left]->'$relationName'->[right]: $count1";
            $messageBuffer[] = "$info [right]->'$relationInverse'->[left]: $count2";
            return true;
        } else if ($count1 < $count2) {
            $messageBuffer[] = "$info [left]->'$relationName'->[right]: $count1 => should be $count2";
            $messageBuffer[] = "$info [right]->'$relationInverse'->[left]: $count2";
            return false;
        } else {
            $messageBuffer[] = "$info [left]->'$relationName'->[right]: $count1";
            $messageBuffer[] = "$info [right]->'$relationInverse'->[left]: $count2 => should be $count1";
            return false;
        }
        return true;
    }
}
?>