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

class EventTestData extends BeditaTestData {

   var $data = array(
        'dateItems' => array(
            'title' => 'event with calendar dates',
            'DateItem' => array(
                array(
                    'start_date' => '2014-05-05 09:00:00',
                    'end_date' => '2014-05-10 21:00:00'
                ),
                array(
                    'start_date' => '2014-06-01 10:30:00',
                    'end_date' => '2014-06-02 15:30:00'
                )
            )
        )
    );

}
