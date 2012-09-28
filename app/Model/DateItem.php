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

/**
 * Base Date object
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class DateItem extends BEAppModel 
{
	var $recursive = 0 ;

	var $validate = array(
//		'start_date' => array('rule' => 'notEmpty'),
//		'end_date' => array('rule' => 'notEmpty')
	) ;
	
	function beforeValidate() {

        $this->checkDate('start_date');
        $this->checkDate('end_date');
        $data = &$this->data[$this->name] ;
        if(!empty($data['start_date']) && !empty($data['timeStart'])) {
            $data['start_date'] .= " " . $data['timeStart'];
        }
        if (!empty($data['end_date']) && !empty($data['timeEnd'])) {
            $data['end_date'] .= " " . $data['timeEnd'];
        }
        
        return true;
	}
}
?>