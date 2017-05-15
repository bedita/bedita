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

    public function ls() {
        $relationStats = ClassRegistry::init('RelationStats');
        $this->out($relationStats->getRelationNames());
    }

    public function check() {
        if (!isset($this->params['name']) && !isset($this->params['all'])) {
            $this->out('Missing param(s)');
            $this->out('    Usage: check -name <relation-name>');
            $this->out('    Usage: check -all');
            exit;
        }
        $relationStats = ClassRegistry::init('RelationStats');
        $relationCheck = ClassRegistry::init('RelationCheck');
        $relationRepair = ClassRegistry::init('RelationRepair');
        $relationNames = (isset($this->params['name'])) ? array($this->params['name']) : $relationStats->getRelationNames();
        foreach ($relationNames as $relationName) {
            $this->out("-----------------------------------------------------------------");
            $this->in("Relation $relationName (press enter to continue)");
            $relationData = $relationStats->getRelation($relationName);
            if ($relationData == null) {
                $this->out('Relation "' . $relationName . '" not found');
                exit;
            }
            $this->out($relationStats->getDescription($relationName));
            $this->out("-----------------------------------------------------------------");
            $messages = array();
            $result = $relationCheck->checkRelation($relationName, $messages);
            $this->outMessages($messages);
            if ($result === false) {
                if (!empty($relationData['inverse'])) {
                    $countAllTypesL = $relationStats->getObjectRelationsCount($relationName);
                    $countAllTypesR = $relationStats->getObjectRelationsCount($relationData['inverse']);
                    if ($countAllTypesL != $countAllTypesR) {
                        $ans = $this->in('Do you want to try to fix relation data? [y/n]');
                        if ($ans === 'y') {
                            $repaired = $relationRepair->repair($relationName);
                            $this->out($repaired . ' ObjectRelation records repaired');
                        }
                    }
                }
            }
            $this->out("-----------------------------------------------------------------");
        }
    }

    public function fix() {
        if (!isset($this->params['name'])) {
            $this->out('Missing param(s)');
            $this->out('    Usage: fix -name <relation-name>');
            exit;
        }
        $relationName = $this->params['name'];
        $relationStats = ClassRegistry::init('RelationStats');
        $relationData = $relationStats->getRelation($relationName);
        if (empty($relationData)) {
            $this->out('Relation "' . $relationName . '" not found');
            exit;
        }
        $this->out($relationStats->getDescription($relationName));
        $transaction = new TransactionComponent('default');
        $repaired = 0;
        try {
            $transaction->begin();
            $repaired = ClassRegistry::init('RelationRepair')->repair($relationName);
            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollback();
        }
        $this->out($repaired . ' ObjectRelation records repaired');
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

    private function outMessages($messages) {
        foreach ($messages as $message) {
            $this->out($message);
        }
    }
}
?>