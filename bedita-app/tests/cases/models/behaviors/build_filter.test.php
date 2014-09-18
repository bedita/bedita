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

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class BuildFilterTestCase extends BeditaTestCase {

    protected $BEObject;

    protected $db;

    protected $s;

    protected $e;

    public function __construct() {
        // model to use BuildFilterBehavior
        $this->BEObject = ClassRegistry::init('BEObject');
        $this->BEObject->Behaviors->attach('BuildFilter');
        $this->db = ConnectionManager::getDataSource($this->BEObject->useDbConfig);
        $this->s = $this->db->startQuote;
        $this->e = $this->db->endQuote;
        parent::__construct('BuildFilter', dirname(__FILE__));
    }

    public function testConditions() {
        // simpleFilter
        $this->requiredData(array('simpleFilter'));
        $conditions = $this->buildStatements($this->data['simpleFilter'], 'conditions');
        $expected = array('BEObject.object_type_id' => 1);
        $this->assertEqual($conditions, $expected);
        $where = $this->db->conditions($conditions, true, false);
        $expectedWhere = $this->quoteFields('BEObject.object_type_id = 1');
        $this->assertEqual($where, $expectedWhere);

        // simpleFilterArr
        $this->requiredData(array('simpleFilterArr'));
        $conditions = $this->buildStatements($this->data['simpleFilterArr'], 'conditions');
        $expected = array('BEObject.object_type_id' => array(1, 3, 22));
        $this->assertEqual($conditions, $expected);
        $where = $this->db->conditions($conditions, true, false);
        $expectedWhere = $this->quoteFields('BEObject.object_type_id IN (1, 3, 22)');
        $this->assertEqual($where, $expectedWhere);

        // selectStreamFields
        $this->requiredData(array('selectStreamFields'));
        $conditions = $this->buildStatements($this->data['selectStreamFields'], 'conditions');
        $expected = array($this->quoteFields('BEObject.id = Stream.id')); // join condition between objects and streams
        $this->assertEqual($conditions, $expected);
        $where = $this->db->conditions($conditions, true, false);
        $expectedWhere = $this->quoteFields('BEObject.id = Stream.id');
        $this->assertEqual($where, $expectedWhere);

        // notFilter
        $this->requiredData(array('notFilter'));
        $conditions = $this->buildStatements($this->data['notFilter'], 'conditions');
        $expected = $this->data['notFilter'];
        $this->assertEqual($conditions, $expected);
        $where = $this->db->conditions($conditions, true, false);
        $expectedWhere = $this->quoteFields("NOT (BEObject.status IN ('draft', 'off'))");
        $this->assertEqual($where, $expectedWhere);

        // orFilter
        $this->requiredData(array('orFilter'));
        $conditions = $this->buildStatements($this->data['orFilter'], 'conditions');
        $expected = array(
            'BEObject.object_type_id' => 1,
            'OR' => array(
                'BEObject.status' => 'off',
                'BEObject.id' => array(10, 20)
            )
        );
        $this->assertEqual($conditions, $expected);
        $where = $this->db->conditions($conditions, true, false);
        $expectedWhere = $this->quoteFields("BEObject.object_type_id = 1 AND ((BEObject.status = 'off') OR (BEObject.id IN (10, 20)))");
        $this->assertEqual($where, $expectedWhere);

        // notOldStyle
        $this->requiredData(array('notOldStyle'));
        $conditions = $this->buildStatements($this->data['notOldStyle'], 'conditions');
        $expected = array(
            'NOT' => array(
                'Stream.mime_type' => array('image/jpeg', 'image/png')
            ),
            $this->quoteFields('BEObject.id = Stream.id') // auto join
        );
        $this->assertEqual($conditions, $expected);
        $where = $this->db->conditions($conditions, true, false);
        $expectedWhere = $this->quoteFields("NOT (Stream.mime_type IN ('image/jpeg', 'image/png')) AND BEObject.id = Stream.id");
        $this->assertEqual($where, $expectedWhere);

        // orOnSameField
        $this->requiredData(array('orOnSameField'));
        $conditions = $this->buildStatements($this->data['orOnSameField'], 'conditions');
        $expected = $this->data['orOnSameField'];
        $this->assertEqual($conditions, $expected);
        $where = $this->db->conditions($conditions, true, false);
        $expectedWhere = $this->quoteFields("((BEObject.title LIKE '%one%') OR (BEObject.title LIKE '%two%'))");
        $this->assertEqual($where, $expectedWhere);

        // signedFilter
        $this->requiredData(array('signedFilter'));
        $conditions = $this->buildStatements($this->data['signedFilter'], 'conditions');
        $expected = $this->data['signedFilter'];
        $this->assertEqual($conditions, $expected);
        $where = $this->db->conditions($conditions, true, false);
        $expectedWhere = $this->quoteFields("Content.end_date <= '2014-06-30' AND BEObject.title LIKE '%title%'");
        $this->assertEqual($where, $expectedWhere);

        // complexConditions
        $this->requiredData(array('complexConditions'));
        $conditions = $this->buildStatements($this->data['complexConditions'], 'conditions');
        $expected = $this->data['complexConditions'];
        $this->assertEqual($conditions, $expected);
        $where = $this->db->conditions($conditions, true, false);
        $expectedWhere = $this->quoteFields("Content.start_date > '2014-06-23 09:30:00' AND ((BEObject.title = 'title one') OR (BEObject.title = 'title two')) AND ((Stream.mime_type = 'image/png') OR (NOT (BEObject.status IN ('on', 'off'))))");
        $this->assertEqual($where, $expectedWhere);

        // sqlInjection
        $this->requiredData(array('sqlInjection'));
        $conditions = $this->buildStatements($this->data['sqlInjection'], 'conditions');
        $expected = $this->data['sqlInjection'];
        $this->assertEqual($conditions, $expected);
        $where = $this->db->conditions($conditions, true, false);
        $expectedWhere = $this->quoteFields("BEObject.object_type_id = '1\' OR \'1\' = \'1'");
        $this->assertEqual($where, $expectedWhere);
    }

    public function testFields() {
        // simpleFilter
        $this->requiredData(array('simpleFilter'));
        $fields = $this->buildStatements($this->data['simpleFilter'], 'fields');
        $expected = $this->quoteFields(', BEObject.object_type_id');
        $this->assertEqual($fields, $expected);

        // selectStreamFields
        $this->requiredData(array('selectStreamFields'));
        $fields = $this->buildStatements($this->data['selectStreamFields'], 'fields');
        $expected = ', ' . $this->BEObject->fieldsString('Stream');
        $this->assertEqual($fields, $expected);

        // notOldStyle
        $this->requiredData(array('notOldStyle'));
        $fields = $this->buildStatements($this->data['notOldStyle'], 'fields');
        $expected = ', ' . $this->quoteFields('Stream.mime_type');
        $this->assertEqual($fields, $expected);
    }

    private function quoteFields($string) {
        $string = preg_replace_callback('/(?:[\'\"][^\'\"\\\]*(?:\\\.[^\'\"\\\]*)*[\'\"])|([a-z0-9_' . $this->s . $this->e . ']*\\.[a-z0-9_' . $this->s . $this->e . ']*)/i', array(&$this, 'quoteMatchedField'), $string);
        return $string;
    }

    private function quoteMatchedField($match) {
        if (is_numeric($match[0])) {
            return $match[0];
        }
        return $this->db->name($match[0]);
    }

    private function buildStatements($filter, $returnType = null) {
        $statements = $this->BEObject->getSqlItems($filter);

        if ($returnType && isset($statements[$returnType])) {
            return $statements[$returnType];
        }

        return $statements;
    }

}