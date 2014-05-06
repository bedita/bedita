<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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

/**
 * EventTestCase class
 */
class EventTestCase extends BeditaTestCase {

    public $uses = array("Event");

    public function testActsAs() {
        $this->checkDuplicateBehavior($this->Event);
    }

    public function testSaveDateItems() {
        $this->requiredData(array('dateItems'));

        $res = $this->Event->save($this->data['dateItems']);
        $this->assertEqual($res, true);
        $res = $this->Event->DateItem->find('all', array(
            'conditions' => array('object_id' => $this->Event->id),
            'order' => 'id ASC'
        ));
        $this->assertNotEqual($res, false);

        foreach ($res as $k => $r) {
            $this->assertEqual($r['DateItem']['start_date'], $this->data['dateItems']['DateItem'][$k]['start_date']);
        }

        // remove date items
        $this->data['dateItems']['id'] = $this->Event->id;
        $this->data['dateItems']['DateItem'] = array(
            array(
                'start_date' => '',
                'end_date' => ''
            )
        );
        $this->Event->create();
        $res = $this->Event->save($this->data['dateItems']);
        $this->assertEqual($res, true);
        $res = $this->Event->DateItem->find('all', array(
            'conditions' => array('object_id' => $this->data['dateItems']['id'])
        ));
        $this->assertFalse($res);
    }

    public   function __construct () {
        parent::__construct('Event', dirname(__FILE__)) ;
    }
}
