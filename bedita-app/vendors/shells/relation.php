<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008-2016 ChannelWeb Srl, Chialab Srl
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

App::import('Component', 'Transaction');

/**
 * Relation shell: methods to check and fix relations
 */
class RelationShell extends BeditaBaseShell {

    protected $objRelationType = array();

    public function ls() {
        $this->init();
        $this->out(array_keys($this->objRelationType));
    }

    public function check() {
        $this->init();
        if (!isset($this->params['name']) && !isset($this->params['all'])) {
            $this->out('Missing param(s)');
            $this->out('    Usage: check -name <relation-name>');
            $this->out('    Usage: check -all');
            exit;
        }
        $relationNames = array();
        if (isset($this->params['name'])) {
            $relationNames[] = $this->params['name'];
        } else if (isset($this->params['all'])) {
            $relationNames = array_keys($this->objRelationType);
        }
        $this->out("-----------------------------------------------------------------");
        foreach ($relationNames as $relationName) {
            $this->in("Relation $relationName (press enter to continue)");
            $relationData = $this->relationData($relationName);
            if ($relationData == null) {
                $this->out('Relation "' . $relationName . '" not found');
                exit;
            }
            $this->describeRelation($relationName, $relationData);
            $result = $this->checkRelation($relationName, $relationData);
            if ($result !== 'ok') {
                $ans = $this->in('Do you want to try to fix relation data? [y/n]');
                if ($ans === 'y') {
                    $transaction = new TransactionComponent('default');
                    $transaction->begin();
                    if (!$this->repairRelation($relationName, $relationData)) {
                        $transaction->rollback();
                    } else {
                        $transaction->commit();
                    }
                }
            }
            $this->out("-----------------------------------------------------------------");
        }
    }

    public function fix() {
        $this->init();
        if (!isset($this->params['name'])) {
            $this->out('Missing param(s)');
            $this->out('    Usage: fix -name <relation-name>');
            exit;
        }
        $relationData = $this->relationData($this->params['name']);
        if ($relationData == null) {
            $this->out('Relation "' . $this->params['name'] . '" not found');
            exit;
        }
        $this->describeRelation($this->params['name'], $relationData);
        $transaction = new TransactionComponent('default');
        $transaction->begin();
        if (!$this->repairRelation($this->params['name'], $relationData)) {
            $transaction->rollback();
        } else {
            $transaction->commit();
        }
    }

    public function help() {
        $this->out('Available functions:');
        $this->out(' ');
        $this->out('0. ls: relations list');
        $this->out('    Usage: ls');
        $this->out(' ');
        $this->out('1. check: relations data check');
        $this->out('    Usage: check -name <relation-name>');
        $this->out('    Usage: check -all');
        $this->out(' ');
        $this->out("    -name <relation-name>   \t name of relation you want to check");
        $this->out("    -all   \t all relations");
        $this->out(' ');
        $this->out('2. fix: relations data fix');
        $this->out('    Usage: fix -name <relation-name>');
        $this->out(' ');
        $this->out("    -name <relation-name>   \t name of relation you want to fix");
        $this->out(' ');
    }

    private function relationData($relationName) {        
        if (!empty($this->objRelationType[$relationName])) {
            return $this->objRelationType[$relationName];
        }
        return null;
    }

    private function describeRelation($relationName, $relationData) {
        $description = 'Relation "' . $relationName . '"';
        if (!empty($relationData['label'])) {
            $description.= ' (' . $relationData['label'] . ')';
        }
        if (!empty($relationData['inverse'])) {
            $description.= ', inverse "' . $relationData['inverse'] . '"';
        }
        if (!empty($relationData['inverseLabel'])) {
            $description.= ' (' . $relationData['inverseLabel'] . ')';
        }
        $left = '';
        if (!empty($relationData['left'])) {
            foreach ($relationData['left'] as $leftRelation) {
                $left.= ' ' . $leftRelation;
            }
        }
        $description.= "\nfrom [left]: " . trim($left);
        $right = '';
        if (!empty($relationData['right'])) {
            foreach ($relationData['right'] as $rightRelation) {
                $right.= ' ' . $rightRelation;
            }
        }
        $description.= "\nto [right]: " . trim($right);
        $params = '';
        if (!empty($relationData['params'])) {
            foreach ($relationData['params'] as $param) {
                $params.= ' ' . json_encode($param);
            }
        }
        $description.= "\nparams: " . trim($params) . "\n";
        $this->out($description);
    }

    private function checkRelation($relationName, $relationData) {
        $result = 'ok';
        $leftObjectTypes = $this->objectTypesIdsForObjectNames($relationData,'left');
        $rightObjectTypes = $this->objectTypesIdsForObjectNames($relationData,'right');
        $objRels = $this->relationsByNameAndObjectTypes($relationName, $leftObjectTypes, $rightObjectTypes);
        $leftToRight = count($objRels);
        $leftToRightAll = $this->countRelation($relationName);
        if (!empty($relationData['inverse']) && ($relationData['inverse'] != $relationName) ) {
            $relationInverse = $relationData['inverse'];
            $objRels = $this->relationsByNameAndObjectTypes($relationInverse, $rightObjectTypes, $leftObjectTypes);
            $rightToLeft = count($objRels);
            $rightToLeftAll = $this->countRelation($relationInverse);
            if ($leftToRight == $rightToLeft) {
                $this->out("L-R [left]->'$relationName'->[right]: $leftToRight");
                $this->out("L-R [right]->'$relationInverse'->[left]: $rightToLeft");
            } else if ($leftToRight < $rightToLeft) {
                $this->out("L-R [left]->'$relationName'->[right]: $leftToRight => should be $rightToLeft");
                $this->out("L-R [right]->'$relationInverse'->[left]: $rightToLeft");
                $result = 'corrupted data';
            } else {
                $this->out("L-R [left]->'$relationName'->[right]: $leftToRight");
                $this->out("L-R [right]->'$relationInverse'->[left]: $rightToLeft => should be $leftToRight");
                $result = 'corrupted data';
            }
            if ($leftToRightAll == $rightToLeftAll) {
                $this->out("ALL [left]->'$relationName'->[right]: $leftToRightAll");
                $this->out("ALL [right]->'$relationInverse'->[left]: $rightToLeftAll");
            } else if ($leftToRightAll < $rightToLeftAll) {
                $this->out("ALL [left]->'$relationName'->[right]: $leftToRightAll => should be $rightToLeftAll");
                $this->out("ALL [right]->'$relationInverse'->[left]: $rightToLeftAll");
                $result = 'corrupted data';
            } else {
                $this->out("ALL [left]->'$relationName'->[right]: $leftToRightAll");
                $this->out("ALL [right]->'$relationInverse'->[left]: $rightToLeftAll => should be $leftToRightAll");
                $result = 'corrupted data';
            }
        } else {
            $this->out("No inverse: no checks");
        }
        $this->out($result);
        return $result;
    }

    private function repairRelation($relationName, $relationData) {
        $relationInverse = null;
        if (!empty($relationData['inverse']) && ($relationData['inverse'] != $relationName) ) {
            $relationInverse = $relationData['inverse'];
        }
        // 1. get distinct object_relation id, object_id by switch = '$relationName' with object type left ok
        $leftObjectTypes = $this->objectTypesIdsForObjectNames($relationData,'left');
        $rightObjectTypes = $this->objectTypesIdsForObjectNames($relationData,'right');
        $objRels = $this->relationsByNameAndObjectTypes($relationName, $leftObjectTypes, $rightObjectTypes);
        if (empty($objRels) && $relationInverse!=null) {
            $objRels = $this->relationsByNameAndObjectTypes($relationInverse, $rightObjectTypes, $leftObjectTypes);
            $tmp = $relationName;
            $relationInverse = $tmp;
            $tmp = $relationInverse;
            $relationName = $tmp;
        }
        $error = false;
        $fixed = 0;
        $skip = 0;
        $objRel = ClassRegistry::init('ObjectRelation');
        foreach ($objRels as $relRecord) {
            $rc = $relRecord['ObjectRelation'];
            $rcDesc = 'id: ' . $rc['id'] . ', object_id:' . $rc['object_id']; 
            if ($relationInverse == null || $objRel->relationExists($rc['object_id'], $rc['id'], $relationInverse)) {
                $skip++;
                continue;
            } else {
                $error = false;
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
                    if (!$objRel->deleteRelation($rc['object_id'], $rc['id'], $relationName, false)) {
                        $this->out('error creating relation. rolling back');
                        $error = true;
                    }
                    if (!empty($relationRightWrong['ObjectRelation']['params'])) {
                        $params = $relationRightWrong['ObjectRelation']['params'];
                    }
                    if (!empty($relationRightWrong['ObjectRelation']['priority'])) {
                        $priority = $relationRightWrong['ObjectRelation']['priority'];
                    }
                }
                if (!$objRel->createRelation($rc['object_id'], $rc['id'], $relationInverse, $priority, false, $params)) {
                    $this->out('error creating relation. rolling back');
                    $error = true;
                }                
                if ($error) {
                    break;
                } else {
                    $fixed++;
                }
            }
        }
        if ($skip > 0) {
            $this->out($skip . ' relations seem ok (skipped).');
        }
        if (!$error) {
            $this->out($fixed . ' relations have been fixed.');
        } else {
            return false;
        }
        return true;
    }

    private function objectTypesIdsForObjectNames($relationData, $field) {
        $objectTypesIds = array();
        if (!empty($relationData[$field])) {
            $objectNames = $relationData[$field];
            if (!empty($objectNames)) {
                foreach ($objectNames as $objectName) {
                    $ot = Configure::read('objectTypes.' . $objectName . '.id');
                    if (!empty($ot)) {
                        $objectTypesIds[] = $ot;
                    }
                }
            }
        } else {
            $objectTypesIds = Configure::read('objectTypes.related.id');
        }
        return $objectTypesIds;
    }

    private function relationsByNameAndObjectTypes($relationName, $leftObjectTypes, $rightObjectTypes) {
        return ClassRegistry::init('ObjectRelation')->find('all', array(
            'conditions' => array(
                'switch' => $relationName
            ),
            'joins' => array(
                array(
                    'table' => 'objects',
                    'alias' => 'ObjectLeft',
                    'type' => 'inner',
                    'conditions' => array(
                        'ObjectLeft.id = ObjectRelation.id',
                        'ObjectLeft.object_type_id' => $leftObjectTypes
                    )
                ),
                array(
                    'table' => 'objects',
                    'alias' => 'ObjectRight',
                    'type' => 'inner',
                    'conditions' => array(
                        'ObjectRight.id = ObjectRelation.object_id',
                        'ObjectRight.object_type_id' => $rightObjectTypes
                    )
                )
            )
        ));
    }

    private function countRelation($relationName) {
        return ClassRegistry::init('ObjectRelation')->find('count', array(
            'conditions' => array(
                'switch' => $relationName
            )
        ));
    }

    private function init() {
        $this->objRelationType = BeConfigure::mergeAllRelations();
    }
}
?>