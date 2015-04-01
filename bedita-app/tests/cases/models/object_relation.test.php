<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2014 ChannelWeb Srl, Chialab Srl
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
 * Object Relations test
 *
 */
require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class ObjectRelationTestCase extends BeditaTestCase {

    var $uses = array("Document", "ObjectRelation");

    function testRelationParams() {
        $this->requiredData(array("doc1", "doc2", "relationParms", "relationNoParms", "relationNewPrior", "relationNewParms"));

        // save document
        $result = $this->Document->save($this->data['doc1']) ;
        $this->assertNotEqual($result, false);		
        $idDoc1 = $this->Document->id;

        // add id to doc2
        $this->data['doc2']['RelatedObject']['testRelation'][0]['id'] = $idDoc1;
        $this->Document->create();
        $result = $this->Document->save($this->data['doc2']) ;
        $this->assertNotEqual($result, false);
        $idDoc2 = $this->Document->id;

        // save other relation
        $r = $this->data['relationParms'];
        $this->ObjectRelation->createRelation($idDoc1, $idDoc2, $r['switch'], 
                $r['priority'], true, $r['params']);

        $r = $this->data['relationNoParms'];
        $this->ObjectRelation->createRelationAndInverse($idDoc1, $idDoc2, $r['switch'], 
                $r['inverse']);
        
        // reload document
        $doc1 = $this->Document->findById($idDoc1);
        $doc2 = $this->Document->findById($idDoc2);
        foreach ($doc1["RelatedObject"] as $r) {
            if ($r['switch'] === $this->data['relationParms']['switch']) {
                $this->assertEqual($r['priority'], $this->data['relationParms']['priority']);
                $this->assertEqual($r['params'], $this->data['relationParms']['params']);
            }
        }
        foreach ($doc2["RelatedObject"] as $r) {
            if ($r['switch'] === $this->data['relationParms']['switch']) {
                $this->assertEqual($r['priority'], $this->data['relationParms']['priority']);
                $this->assertEqual($r['params'], $this->data['relationParms']['params']);
            }
        }

        // update relation priority (both sides) and params (just once)
        $this->ObjectRelation->updateRelationPriority($idDoc1, $idDoc2,
                $this->data['relationParms']['switch'], $this->data['relationNewPrior']);
        $this->ObjectRelation->updateRelationPriority($idDoc2, $idDoc1,
                $this->data['relationParms']['switch'], $this->data['relationNewPrior']);
        $this->ObjectRelation->updateRelationParams($idDoc1, $idDoc2,
                $this->data['relationParms']['switch'], $this->data['relationNewParms']);

        // reload document
        $doc1 = $this->Document->findById($idDoc1);
        $doc2 = $this->Document->findById($idDoc2);
        foreach ($doc1["RelatedObject"] as $r) {
            if ($r['switch'] === $this->data['relationParms']['switch']) {
                $this->assertEqual($r['priority'], $this->data['relationNewPrior']);
                $this->assertEqual($r['params'], $this->data['relationNewParms']);
            }
        }
        foreach ($doc2["RelatedObject"] as $r) {
            if ($r['switch'] === $this->data['relationParms']['switch']) {
                $this->assertEqual($r['priority'], $this->data['relationNewPrior']);
                $this->assertEqual($r['params'], $this->data['relationNewParms']);
            }
        }

        // remove documents
        $result = $this->Document->delete($idDoc1);
        $this->assertEqual($result, true);		
        $result = $this->Document->delete($idDoc2);
        $this->assertEqual($result, true);		
    }

     public   function __construct () {
        parent::__construct('ObjectRelation', dirname(__FILE__)) ;
    }
}
?>